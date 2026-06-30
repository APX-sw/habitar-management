<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalarySettlement;
use App\Models\EmployeeSalaryBonus;
use App\Models\Expense;
use App\Models\TransactionCategory;
use App\Models\Account;
use App\Models\CashRegisterMovement;
use App\Models\IndexValue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeSalarySettlementController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeSalarySettlement::query();

        if ($request->filled('filter_year')) {
            $query->where('year', $request->filter_year);
        }

        if ($request->filled('filter_month')) {
            $query->where('month', $request->filter_month);
        }

        $periods = $query->selectRaw('month, year, count(*) as total_settlements, sum(net_amount) as sum_total, 
                                     sum(case when status = "paid" then 1 else 0 end) as paid_count,
                                     sum(case when status != "paid" then 1 else 0 end) as pending_count')
            ->groupBy('month', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $availableYears = EmployeeSalarySettlement::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('salaries.index', compact('periods', 'availableYears'));
    }

    public function create(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $employees = Employee::all();
        $accounts = Account::all();

        return view('salaries.create', compact('employees', 'month', 'year', 'accounts'));
    }

    public function showPeriod($month, $year)
    {
        $employees = Employee::with([
            'salarySettlements' => function ($query) use ($month, $year) {
                $query->where('month', $month)->where('year', $year)->with('bonuses', 'payments', 'payments.account');
            },
            'salarySettlements.bonuses'
        ])->orderBy('last_name')->get();

        $advances = \App\Models\Expense::whereHas('transactionCategory', function($q) {
            $q->where('name', 'Adelanto de Sueldo');
        })
        ->whereMonth('date', $month)
        ->whereYear('date', $year)
        ->get()
        ->groupBy('employee_id');

        $pastBonuses = \App\Models\EmployeeSalaryBonus::with('settlement')
            ->whereHas('settlement', function($q) use ($month, $year) {
                $q->where('month', '<', $month)->where('year', '<=', $year)
                  ->orWhere('year', '<', $year);
            })
            ->latest('id')
            ->get()
            ->groupBy(function($bonus) {
                return $bonus->settlement->employee_id;
            })->map->take(3);

        $accounts = Account::all();

        return view('salaries.period', compact('employees', 'month', 'year', 'accounts', 'advances', 'pastBonuses'));
    }

    public function storeAdvance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'account_id' => 'required|exists:accounts,id',
            'description' => 'nullable|string'
        ]);

        $category = TransactionCategory::firstOrCreate(['name' => 'Adelanto de Sueldo'], ['type' => 'expense']);
        $account = Account::findOrFail($request->account_id);

        $date = Carbon::parse($request->date);
        if ($date->isToday()) {
            $date = now();
        }

        $expense = Expense::create([
            'employee_id' => $request->employee_id,
            'account_id' => $request->account_id,
            'date' => $date,
            'amount' => $request->amount,
            'description' => $request->description ?? 'Adelanto de sueldo',
            'transaction_category_id' => $category->id,
            'is_paid' => true,
        ]);

        CashRegisterMovement::create([
            'account_id' => $account->id,
            'type' => 'expense',
            'amount' => $request->amount,
            'description' => 'Adelanto de sueldo - ' . $expense->employee->full_name,
            'movement_date' => $date,
            'related_id' => $expense->id,
            'related_type' => Expense::class,
            'transaction_category_id' => $category->id,
        ]);

        return back()->with('success', 'Adelanto de sueldo registrado correctamente.');
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer'
        ]);

        $month = $request->month;
        $year = $request->year;

        $employees = Employee::all();
        $generatedCount = 0;

        foreach ($employees as $employee) {
            $settlementExists = EmployeeSalarySettlement::where('employee_id', $employee->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if (!$settlementExists) {
                // Lógica de aumentos automáticos
                $this->processAutoIncrease($employee, $month, $year);

                $baseAmount = $employee->base_salary ?? 0;
                
                $advancesAmount = Expense::where('employee_id', $employee->id)
                    ->whereHas('transactionCategory', function($q) {
                        $q->where('name', 'Adelanto de Sueldo');
                    })
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->sum('amount');

                $netAmount = $baseAmount - $advancesAmount;

                EmployeeSalarySettlement::create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                    'base_amount' => $baseAmount,
                    'advances_amount' => $advancesAmount,
                    'bonuses_amount' => 0,
                    'net_amount' => $netAmount,
                    'status' => 'draft'
                ]);

                $generatedCount++;
            }
        }

        return redirect()->route('salaries.show_period', ['month' => $month, 'year' => $year])
            ->with('success', "Se generaron $generatedCount liquidaciones en borrador para el período.");
    }

    public function store(Request $request, Employee $employee)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'bonuses' => 'nullable|array',
            'bonuses.*.description' => 'required_with:bonuses|string',
            'bonuses.*.amount' => 'required_with:bonuses|numeric|min:1',
        ]);

        $month = $request->month;
        $year = $request->year;

        // Check if settlement already exists
        $settlement = EmployeeSalarySettlement::where('employee_id', $employee->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($settlement && $settlement->status == 'paid') {
            return back()->with('error', 'El sueldo de este mes ya fue pagado.');
        }

        // Lógica de aumentos automáticos
        $this->processAutoIncrease($employee, $month, $year);

        $baseAmount = $employee->base_salary ?? 0;
        
        $advancesAmount = Expense::where('employee_id', $employee->id)
            ->whereHas('transactionCategory', function($q) {
                $q->where('name', 'Adelanto de Sueldo');
            })
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $bonusesAmount = 0;
        if ($request->has('bonuses')) {
            foreach ($request->bonuses as $bonus) {
                $bonusesAmount += $bonus['amount'];
            }
        }

        $netAmount = ($baseAmount + $bonusesAmount) - $advancesAmount;

        if (!$settlement) {
            $settlement = EmployeeSalarySettlement::create([
                'employee_id' => $employee->id,
                'month' => $month,
                'year' => $year,
                'base_amount' => $baseAmount,
                'advances_amount' => $advancesAmount,
                'bonuses_amount' => $bonusesAmount,
                'net_amount' => $netAmount,
                'status' => 'ready'
            ]);
        } else {
            $settlement->update([
                'base_amount' => $baseAmount,
                'advances_amount' => $advancesAmount,
                'bonuses_amount' => $bonusesAmount,
                'net_amount' => $netAmount,
                'status' => 'ready'
            ]);
            $settlement->bonuses()->delete();
        }

        if ($request->has('bonuses')) {
            foreach ($request->bonuses as $bonus) {
                $settlement->bonuses()->create([
                    'description' => $bonus['description'],
                    'amount' => $bonus['amount']
                ]);
            }
        }

        return back()->with('success', 'Borrador de sueldo guardado como listo para pagar.');
    }

    public function pay(Request $request, EmployeeSalarySettlement $settlement)
    {
        $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.account_id' => 'required|exists:accounts,id',
            'payments.*.payment_date' => 'required|date',
        ]);

        if ($settlement->status == 'paid') {
            return back()->with('error', 'El sueldo ya está pagado en su totalidad.');
        }

        if ($settlement->status == 'draft') {
            return back()->with('error', 'El sueldo debe guardarse como listo para poder pagarse.');
        }

        $remaining = $settlement->net_amount - $settlement->paid_amount;
        $totalToPay = collect($request->payments)->sum('amount');

        if (round($totalToPay, 2) > round($remaining, 2)) {
            return back()->with('error', "El monto total a pagar (\$$totalToPay) supera el saldo restante del sueldo (\$$remaining).");
        }

        $category = TransactionCategory::firstOrCreate(['name' => 'Pago de Sueldo'], ['type' => 'expense']);

        // Validar saldos antes de procesar pagos para evitar inconsistencias
        $accountTotals = [];
        foreach ($request->payments as $paymentData) {
            $accId = $paymentData['account_id'];
            $amt = round($paymentData['amount'], 2);
            if (!isset($accountTotals[$accId])) {
                $accountTotals[$accId] = 0;
            }
            $accountTotals[$accId] += $amt;
        }

        foreach ($accountTotals as $accId => $totalReq) {
            $account = Account::findOrFail($accId);
            if ($account->current_balance < $totalReq) {
                return back()->with('error', "Saldo insuficiente en la cuenta '{$account->name}'. (Requiere \$$totalReq)");
            }
        }

        foreach ($request->payments as $paymentData) {
            $amount = round($paymentData['amount'], 2);
            $account = Account::findOrFail($paymentData['account_id']);

            $payment = \App\Models\EmployeeSalaryPayment::create([
                'employee_salary_settlement_id' => $settlement->id,
                'amount' => $amount,
                'account_id' => $account->id,
                'payment_date' => $paymentData['payment_date'],
            ]);

            CashRegisterMovement::create([
                'account_id' => $account->id,
                'type' => 'expense',
                'amount' => $amount,
                'description' => 'Pago de Sueldo (' . str_pad($settlement->month, 2, '0', STR_PAD_LEFT) . '/' . $settlement->year . ') - ' . $settlement->employee->full_name,
                'movement_date' => $paymentData['payment_date'],
                'related_id' => $payment->id,
                'related_type' => \App\Models\EmployeeSalaryPayment::class,
                'transaction_category_id' => $category->id,
            ]);
        }

        $newPaidAmount = $settlement->paid_amount + $totalToPay;
        $status = (round($newPaidAmount, 2) >= round($settlement->net_amount, 2)) ? 'paid' : 'partial';

        $settlement->update([
            'status' => $status,
            'paid_amount' => $newPaidAmount,
        ]);

        return back()->with('success', 'Pagos registrados correctamente.');
    }

    public function receipt(\App\Models\EmployeeSalaryPayment $payment)
    {
        // En lugar de dompdf, retornaremos una vista simple para imprimir del navegador
        return view('salaries.receipt', compact('payment'));
    }

    private function processAutoIncrease(Employee $employee, $month, $year)
    {
        if (!$employee->update_frequency_months || !$employee->base_salary) return;

        $lastIncreaseDate = $employee->last_increase_date ? Carbon::parse($employee->last_increase_date) : Carbon::parse($employee->hire_date);
        $currentDate = Carbon::createFromDate($year, $month, 1);
        
        $monthsDiff = $lastIncreaseDate->diffInMonths($currentDate);
        $shouldIncrease = $monthsDiff >= $employee->update_frequency_months;

        if ($shouldIncrease) {
            $newSalary = $employee->base_salary;
            
            if ($employee->update_type == 'fixed' && $employee->increase_fixed_percentage) {
                $newSalary = $employee->base_salary * (1 + ($employee->increase_fixed_percentage / 100));
            } elseif ($employee->update_type == 'indexed' && $employee->increase_index_id) {
                // Simplified Index logic: get the index value for the current month and apply it
                // Note: Real logic might need to aggregate months, but for now we take the latest value published
                $indexValue = IndexValue::where('index_type_id', $employee->increase_index_id)
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->first();
                
                if ($indexValue && $indexValue->value > 0) {
                    $newSalary = $employee->base_salary * (1 + ($indexValue->value / 100));
                }
            }

            $employee->update([
                'base_salary' => $newSalary,
                'last_increase_date' => $currentDate->format('Y-m-d')
            ]);
            
            session()->flash('info', 'El sueldo de ' . $employee->full_name . ' fue actualizado automáticamente por aumento.');
        }
    }
}
