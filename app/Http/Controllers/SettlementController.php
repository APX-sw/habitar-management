<?php

namespace App\Http\Controllers;

use App\Models\Settlement;
use App\Models\SettlementPayment;
use App\Models\Property;
use App\Models\Collection;
use App\Models\Expense;
use App\Models\Account;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SettlementController extends Controller
{
    public function index(Request $request)
    {
        $query = Settlement::with('owner')->orderBy('year', 'desc')->orderBy('month', 'desc');

        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $settlements = $query->paginate(20)->withQueryString();
        $owners = \App\Models\Owner::orderBy('name')->get();

        if ($request->ajax()) {
            return view('settlements.partials.table', compact('settlements'))->render();
        }

        return view('settlements.index', compact('settlements', 'owners'));
    }

    public function create(Request $request)
    {
        $owners = \App\Models\Owner::all();
        $ownerId = $request->get('owner_id');
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        // Si no hay owner_id, redirigimos a una vista de "Previa Masiva"
        if (!$ownerId) {
            return $this->bulkPreview($month, $year);
        }

        $rentTotal = 0;
        $income = 0;
        $expense = 0;
        $collections = collect();
        $expenses = collect();
        $commissionPercentage = 10; // Default a 10%

        $previousDebt = 0;

        if ($ownerId) {
            $propertyIds = Property::where('owner_id', $ownerId)->pluck('id');

            $collections = Collection::whereHas('lease', function($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds);
            })->where('month', $month)->where('year', $year)->where('status', '!=', 'draft')->get();

            $income = $collections->sum(function($c) {
                return $c->details->where('destination', 'owner')->sum('amount');
            });
            $rentTotal = $collections->sum('rent_amount');
            
            // Pagos ya transferidos directo al propietario
            $directPayments = $collections->sum(function($c) {
                return $c->payments->where('transferred_to_owner', true)->sum('amount');
            });

            $expenses = Expense::whereIn('property_id', $propertyIds)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('applies_to_settlement', true)
                ->get();
            
            $expense = $expenses->sum('amount');

            // Buscar deuda del mes anterior
            $lastSettlement = Settlement::where('owner_id', $ownerId)
                ->where('status', '!=', 'paid')
                ->where('net_amount', '<', 0)
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->first();

            if ($lastSettlement) {
                $paidDebt = $lastSettlement->payments()->sum('amount');
                $previousDebt = abs($lastSettlement->net_amount) - $paidDebt;
                if ($previousDebt < 0) $previousDebt = 0;
            }
        }

        return view('settlements.create', compact('owners', 'ownerId', 'month', 'year', 'income', 'expense', 'collections', 'expenses', 'rentTotal', 'commissionPercentage', 'directPayments', 'previousDebt'));
    }

    public function bulkPreview($month, $year)
    {
        $owners = \App\Models\Owner::whereHas('properties', function($q) use ($month, $year) {
            $q->whereHas('expenses', function($sq) use ($month, $year) {
                $sq->whereMonth('date', $month)->whereYear('date', $year);
            })->orWhereHas('leases.collections', function($sq) use ($month, $year) {
                $sq->where('month', $month)->where('year', $year)->where('status', '!=', 'draft');
            });
        })->with('properties')->get();

        $previews = [];

        foreach ($owners as $owner) {
            $propertyIds = $owner->properties->pluck('id');

            $collections = Collection::whereHas('lease', function($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds);
            })->where('month', $month)->where('year', $year)->where('status', '!=', 'draft')->get();

            $income = $collections->sum(function($c) {
                return $c->details->where('destination', 'owner')->sum('amount');
            });
            $rentBaseForCommission = $collections->sum('rent_amount');
            
            $directPayments = $collections->sum(function($c) {
                return $c->payments->where('transferred_to_owner', true)->sum('amount');
            });

            $expensesSum = Expense::whereIn('property_id', $propertyIds)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            // La comisión se cobra SOLO sobre los alquileres (sin incluir extras)
            $agencyCommission = $rentBaseForCommission * (($owner->commission_percentage ?? 10) / 100);

            $previousDebt = 0;
            $lastSettlement = Settlement::where('owner_id', $owner->id)
                ->where('status', '!=', 'paid')
                ->where('net_amount', '<', 0)
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->first();

            if ($lastSettlement) {
                $paidDebt = $lastSettlement->payments()->sum('amount');
                $previousDebt = abs($lastSettlement->net_amount) - $paidDebt;
                if ($previousDebt < 0) $previousDebt = 0;
            }

            $net = $income - $expensesSum - $agencyCommission - $directPayments - $previousDebt;

            // Solo agregamos si hay movimientos o si el neto no es cero (o si hay deuda previa)
            if ($income > 0 || $expensesSum > 0 || $directPayments > 0 || $previousDebt > 0) {
                $previews[] = [
                    'owner' => $owner,
                    'income' => $income,
                    'expenses' => $expensesSum,
                    'agency_commission' => $agencyCommission,
                    'direct_payments' => $directPayments,
                    'previous_debt' => $previousDebt,
                    'net' => $net,
                    'rent_total' => $rentBaseForCommission // Base para recalcular en JS
                ];
            }
        }

        return view('settlements.bulk_preview', compact('previews', 'month', 'year'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer',
            'owners' => 'required|array',
            'owners.*.owner_id' => 'required|exists:owners,id',
        ]);

        $count = 0;
        foreach ($request->owners as $data) {
            if (!isset($data['selected'])) continue;

            $previousDebt = 0;
            $lastSettlement = Settlement::where('owner_id', $data['owner_id'])
                ->where('status', '!=', 'paid')
                ->where('net_amount', '<', 0)
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->first();

            if ($lastSettlement) {
                $paidDebt = $lastSettlement->payments()->sum('amount');
                $previousDebt = abs($lastSettlement->net_amount) - $paidDebt;
                if ($previousDebt < 0) $previousDebt = 0;
            }

            $netAmount = $data['total_income'] - $data['total_expense'] - $data['agency_commission'] - $previousDebt;

            $settlement = Settlement::updateOrCreate(
                ['owner_id' => $data['owner_id'], 'month' => $request->month, 'year' => $request->year],
                [
                    'rent_total' => $data['rent_total'],
                    'total_income' => $data['total_income'],
                    'total_expense' => $data['total_expense'],
                    'agency_commission' => $data['agency_commission'],
                    'net_amount' => $netAmount,
                    'status' => 'ready'
                ]
            );

            if ($previousDebt > 0) {
                \App\Models\SettlementExtraFee::updateOrCreate(
                    [
                        'settlement_id' => $settlement->id,
                        'description' => 'Deuda arrastrada del mes anterior'
                    ],
                    [
                        'amount' => $previousDebt
                    ]
                );
            }

            $count++;
        }

        return redirect()->route('settlements.index')->with('success', "$count rendiciones generadas masivamente.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'rent_total' => 'required|numeric',
            'total_income' => 'required|numeric',
            'total_expense' => 'required|numeric',
            'agency_commission' => 'required|numeric',
            'net_amount' => 'required|numeric',
        ]);

        $previousDebt = 0;
        $lastSettlement = Settlement::where('owner_id', $request->owner_id)
            ->where('status', '!=', 'paid')
            ->where('net_amount', '<', 0)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        if ($lastSettlement) {
            $paidDebt = $lastSettlement->payments()->sum('amount');
            $previousDebt = abs($lastSettlement->net_amount) - $paidDebt;
            if ($previousDebt < 0) $previousDebt = 0;
        }

        $netAmount = $request->total_income - $request->total_expense - $request->agency_commission - $previousDebt;

        $settlement = Settlement::updateOrCreate(
            ['owner_id' => $request->owner_id, 'month' => $request->month, 'year' => $request->year],
            [
                'rent_total' => $request->rent_total,
                'total_income' => $request->total_income,
                'total_expense' => $request->total_expense,
                'agency_commission' => $request->agency_commission,
                'net_amount' => $netAmount,
                'status' => 'ready'
            ]
        );

        if ($previousDebt > 0) {
            \App\Models\SettlementExtraFee::updateOrCreate(
                [
                    'settlement_id' => $settlement->id,
                    'description' => 'Deuda arrastrada del mes anterior'
                ],
                [
                    'amount' => $previousDebt
                ]
            );
        }

        return redirect()->route('settlements.show', $settlement)->with('success', 'Rendición generada correctamente.');
    }

    public function show(Settlement $settlement)
    {
        $settlement->load(['owner.bankAccounts', 'payments.account', 'payments.ownerBankAccount']);
        $accounts = Account::where('is_active', true)->get();

        // Obtener detalles de lo que compone la rendición para el desglose
        $propertyIds = Property::where('owner_id', $settlement->owner_id)->pluck('id');

        $collections = Collection::with(['lease.property', 'lease.tenant', 'details'])
            ->whereHas('lease', function($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds);
            })
            ->where('month', $settlement->month)
            ->where('year', $settlement->year)
            ->where('status', '!=', 'draft')
            ->get();

        $expenses = Expense::with('property')
            ->whereIn('property_id', $propertyIds)
            ->whereMonth('date', $settlement->month)
            ->whereYear('date', $settlement->year)
            ->get();

        $invoicingData = null;
        $invoicingItems = [];

        foreach ($collections as $col) {
            $lease = $col->lease;
            $invoiceAmount = $lease->getInvoiceAmountForDate($settlement->month, $settlement->year);
            if ($invoiceAmount !== null) {
                $invoicingItems[] = [
                    'property'   => $lease->property->location,
                    'rent'       => $lease->calculateRentForDate($settlement->month, $settlement->year),
                    'percentage' => $lease->invoicing_percentage,
                    'amount'     => $invoiceAmount,
                ];
            }
        }

        if (count($invoicingItems) > 0) {
            $invoicingTotal = array_sum(array_column($invoicingItems, 'amount'));
            $invoicingData = [
                'items' => $invoicingItems,
                'total' => $invoicingTotal,
                'iva_21' => round($invoicingTotal * 0.21, 2),
            ];
        }

        return view('settlements.show', compact('settlement', 'accounts', 'collections', 'expenses', 'invoicingData'));
    }

    public function addExtraFee(Request $request, Settlement $settlement)
    {
        $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01'
        ]);

        \App\Models\SettlementExtraFee::create([
            'settlement_id' => $settlement->id,
            'description' => $request->description,
            'amount' => $request->amount
        ]);

        $settlement->recalculateNet();

        return back()->with('success', 'Honorario extra agregado.');
    }

    public function removeExtraFee(Settlement $settlement, \App\Models\SettlementExtraFee $extraFee)
    {
        if ($extraFee->settlement_id == $settlement->id) {
            $extraFee->delete();
            $settlement->recalculateNet();
        }

        return back()->with('success', 'Honorario extra eliminado.');
    }

    public function sendToOwner(Request $request, Settlement $settlement)
    {
        $type = $request->get('type', 'settlement'); // 'settlement' o 'payment_confirmation'
        $settlement->load(['owner.bankAccounts', 'payments.account', 'payments.ownerBankAccount']);
        
        $propertyIds = Property::where('owner_id', $settlement->owner_id)->pluck('id');
        $collections = Collection::with(['lease.property', 'lease.tenant', 'details'])
            ->whereHas('lease', function($q) use ($propertyIds) {
                $q->whereIn('property_id', $propertyIds);
            })
            ->where('month', $settlement->month)
            ->where('year', $settlement->year)
            ->where('status', '!=', 'draft')
            ->get();

        $expenses = Expense::with(['property', 'transactionCategory'])
            ->whereIn('property_id', $propertyIds)
            ->whereMonth('date', $settlement->month)
            ->whereYear('date', $settlement->year)
            ->where('applies_to_settlement', true)
            ->get();

        $payload = [
            'type' => $type,
            'settlement_id' => $settlement->id,
            'period' => $settlement->month . '/' . $settlement->year,
            'owner' => [
                'name' => $settlement->owner->name,
                'email' => $settlement->owner->email,
            ],
            'totals' => [
                'total_income' => $settlement->total_income,
                'total_expenses' => $settlement->total_expense,
                'agency_commission' => $settlement->agency_commission,
                'extra_fees_total' => $settlement->extraFees->sum('amount'),
                'net_amount' => $settlement->net_amount
            ],
            'details' => [
                'collections' => $collections->map(function($c) {
                    return [
                        'property' => $c->lease->property->location,
                        'tenant' => $c->lease->tenant->name,
                        'items' => $c->details()->get()->map(function($d) {
                            return [
                                'concept' => $d->name,
                                'amount' => $d->amount,
                                'destination' => $d->destination // 'owner' o 'agency'
                            ];
                        })
                    ];
                }),
                'expenses' => $expenses->map(function($e) {
                    return [
                        'property' => $e->property->location,
                        'description' => $e->description ?? ($e->transactionCategory->name ?? 'Gasto Extraordinario'),
                        'amount' => $e->amount
                    ];
                }),
                'extra_fees' => $settlement->extraFees->map(function($ef) {
                    return [
                        'description' => $ef->description,
                        'amount' => $ef->amount
                    ];
                })
            ]
        ];

        if ($type === 'payment_confirmation') {
            $payload['payments'] = $settlement->payments->map(function($p) {
                return [
                    'amount' => $p->amount,
                    'bank' => $p->ownerBankAccount->cbu_alias,
                    'holder' => $p->ownerBankAccount->holder_name,
                    'cuit' => $p->ownerBankAccount->holder_cuit,
                    'date' => $p->date
                ];
            });
        }

        $payload['n8n_code'] = ($type === 'settlement')
            ? \App\Services\N8nCodeService::getSettlementMailCode()
            : \App\Services\N8nCodeService::getSettlementPaymentConfirmCode();

        try {
            // Webhooks diferenciados por tipo
            $webhookUrl = ($type === 'settlement') 
                ? 'https://n8n.apxsoftware.com.ar/webhook/826bdd12-5d40-4574-bf7a-72d90c4d3ee7' 
                : 'https://n8n.apxsoftware.com.ar/webhook/72e5f4f5-f7b8-4b00-beb1-6ea4281bad8d';

            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(15)->post($webhookUrl, $payload);
            
            if (!$response->successful()) {
                throw new \Exception("El servidor respondió con código " . $response->status() . ": " . $response->body());
            }
            
            return back()->with('success', 'Información enviada correctamente para generar el documento y enviar mail.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error Webhook Settlement: " . $e->getMessage());
            return back()->with('error', 'Error al enviar al webhook: ' . $e->getMessage());
        }
    }

    public function pay(Request $request, Settlement $settlement)
    {
        $isNegative = $settlement->net_amount < 0;

        $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.account_id' => 'required|exists:accounts,id',
            'payments.*.owner_bank_account_id' => $isNegative ? 'nullable|exists:owner_bank_accounts,id' : 'required|exists:owner_bank_accounts,id',
            'payments.*.date' => 'required|date',
        ]);

        // 1. Validar que cada cuenta tenga saldo suficiente antes de procesar cualquier movimiento (solo si es egreso)
        if (!$isNegative) {
            $requestedDebits = [];
            foreach ($request->payments as $pData) {
                $accountId = $pData['account_id'];
                $amount = (float)$pData['amount'];
                if (!isset($requestedDebits[$accountId])) {
                    $requestedDebits[$accountId] = 0.0;
                }
                $requestedDebits[$accountId] += $amount;
            }

            foreach ($requestedDebits as $accountId => $totalDebit) {
                $account = Account::findOrFail($accountId);
                if ($account->current_balance < $totalDebit) {
                    return back()->withErrors([
                        'payments' => "Saldo insuficiente en la cuenta \"{$account->name}\". Saldo disponible: \$" . number_format($account->current_balance, 2, ',', '.') . " e intentaste pagar \$" . number_format($totalDebit, 2, ',', '.') . "."
                    ])->withInput();
                }
            }
        }

        // 2. Transacción de base de datos para guardar todo de forma atómica
        \DB::transaction(function () use ($request, $settlement, $isNegative) {
            foreach ($request->payments as $pData) {
                $payment = $settlement->payments()->create($pData);

                // Generar movimiento de caja (Egreso o Ingreso según el neto de la rendición)
                $payment->movement()->create([
                    'account_id' => $pData['account_id'],
                    'type' => $isNegative ? 'income' : 'expense',
                    'amount' => $pData['amount'],
                    'description' => ($isNegative ? 'Cobro Rendición de: ' : 'Pago Rendición a: ') . $settlement->owner->name . ' (' . $settlement->month . '/' . $settlement->year . ')',
                    'movement_date' => Carbon::parse($pData['date'])->setTimeFrom(now()),
                    'transaction_category_id' => $isNegative ? 18 : 5 // 18: Cobro Rendición, 5: Pago Rendición
                ]);

                // SHADOW MOVEMENT a CAJA HABITAR para la comisión (Proporcional)
                $proportion = abs($settlement->net_amount) > 0 ? ($pData['amount'] / abs($settlement->net_amount)) : 1;
                $commissionAmount = $settlement->agency_commission * $proportion;

                $habitarAccount = \App\Models\Account::where('type', 'habitar_fund')->first();
                if ($habitarAccount && $commissionAmount > 0) {
                    \App\Models\CashRegisterMovement::create([
                        'account_id' => $habitarAccount->id,
                        'type' => 'income',
                        'amount' => $commissionAmount,
                        'description' => "Ingreso Caja Habitar (Comisión Rendición) - " . $settlement->owner->name . " (" . $settlement->month . "/" . $settlement->year . ")",
                        'movement_date' => Carbon::parse($pData['date'])->setTimeFrom(now()),
                        'transaction_category_id' => 2, // Honorarios Inmobiliarios
                        'user_id' => auth()->id(),
                        'related_id' => $payment->id,
                        'related_type' => \App\Models\SettlementPayment::class,
                    ]);
                }
            }
        });

        $totalPaid = $settlement->payments()->sum('amount');
        // Toleramos hasta $1.00 ARS de diferencia por el redondeo
        if ($isNegative) {
            if ((abs($settlement->net_amount) - $totalPaid) <= 1.00) {
                $settlement->update(['status' => 'paid']);
            }
        } else {
            if (($settlement->net_amount - $totalPaid) <= 1.00) {
                $settlement->update(['status' => 'paid']);
            }
        }

        return back()->with('success', 'Pago(s) registrado(s) correctamente.');
    }

    public function carryOver(Settlement $settlement)
    {
        if ($settlement->net_amount >= 0) {
            return back()->with('error', 'Solo se pueden arrastrar rendiciones con saldo negativo.');
        }

        $settlement->update(['status' => 'carried_over']);

        return back()->with('success', 'El saldo deudor se ha arrastrado exitosamente y se descontará en la próxima liquidación de este propietario.');
    }
}
