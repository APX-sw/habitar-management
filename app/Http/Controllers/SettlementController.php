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
                ->get();
            
            $expense = $expenses->sum('amount');
        }

        return view('settlements.create', compact('owners', 'ownerId', 'month', 'year', 'income', 'expense', 'collections', 'expenses', 'rentTotal', 'commissionPercentage', 'directPayments'));
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
            $net = $income - $expensesSum - $agencyCommission - $directPayments;

            // Solo agregamos si hay movimientos o si el neto no es cero
            if ($income > 0 || $expensesSum > 0 || $directPayments > 0) {
                $previews[] = [
                    'owner' => $owner,
                    'income' => $income,
                    'expenses' => $expensesSum,
                    'agency_commission' => $agencyCommission,
                    'direct_payments' => $directPayments,
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

            Settlement::updateOrCreate(
                ['owner_id' => $data['owner_id'], 'month' => $request->month, 'year' => $request->year],
                [
                    'rent_total' => $data['rent_total'],
                    'total_income' => $data['total_income'],
                    'total_expense' => $data['total_expense'],
                    'agency_commission' => $data['agency_commission'],
                    'net_amount' => $data['net_amount'],
                    'status' => 'ready'
                ]
            );
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

        $settlement = Settlement::updateOrCreate(
            ['owner_id' => $request->owner_id, 'month' => $request->month, 'year' => $request->year],
            [
                'rent_total' => $request->rent_total,
                'total_income' => $request->total_income,
                'total_expense' => $request->total_expense,
                'agency_commission' => $request->agency_commission,
                'net_amount' => $request->net_amount,
                'status' => 'ready'
            ]
        );

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

        return view('settlements.show', compact('settlement', 'accounts', 'collections', 'expenses'));
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
                ? 'https://n8n.dev.jfsdevs.com.ar/webhook/826bdd12-5d40-4574-bf7a-72d90c4d3ee7' 
                : 'https://n8n.dev.jfsdevs.com.ar/webhook/72e5f4f5-f7b8-4b00-beb1-6ea4281bad8d';

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
        $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.account_id' => 'required|exists:accounts,id',
            'payments.*.owner_bank_account_id' => 'required|exists:owner_bank_accounts,id',
            'payments.*.date' => 'required|date',
        ]);

        // 1. Validar que cada cuenta tenga saldo suficiente antes de procesar cualquier movimiento
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

        // 2. Transacción de base de datos para guardar todo de forma atómica
        \DB::transaction(function () use ($request, $settlement) {
            foreach ($request->payments as $pData) {
                $payment = $settlement->payments()->create($pData);

                // Generar movimiento de caja (Egreso)
                $payment->movement()->create([
                    'account_id' => $pData['account_id'],
                    'type' => 'expense',
                    'amount' => $pData['amount'],
                    'description' => 'Pago Rendición a: ' . $settlement->owner->name . ' (' . $settlement->month . '/' . $settlement->year . ')',
                    'movement_date' => Carbon::parse($pData['date'])->setTimeFrom(now()),
                    'transaction_category_id' => 5 // Pago Rendición a Propietario
                ]);
            }
        });

        $totalPaid = $settlement->payments()->sum('amount');
        // Toleramos hasta $1.00 ARS de diferencia por el redondeo de centavos habitual en Argentina
        if (($settlement->net_amount - $totalPaid) <= 1.00) {
            $settlement->update(['status' => 'paid']);
        }

        return back()->with('success', 'Pago(s) registrado(s) correctamente.');
    }
}
