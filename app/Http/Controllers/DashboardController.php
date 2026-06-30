<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Collection;
use App\Models\Settlement;
use App\Models\CollectionPayment;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 0. Datos de Autogestión del Empleado (si tiene legajo)
        $employee = \App\Models\Employee::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
        $todayAttendance = null;
        $activeAbsenceReasons = [];

        if ($employee) {
            // Redirect non-admins directly to their workspace
            if (!\Illuminate\Support\Facades\Auth::user()->can('dashboard.read')) {
                return redirect()->route('workspace.index');
            }

            $todayAttendance = \App\Models\Attendance::where('employee_id', $employee->id)
                ->whereDate('date', date('Y-m-d'))
                ->first();
            $activeAbsenceReasons = \App\Models\AbsenceReason::where('is_active', true)->get();
        }
        $selectedMonth = intval($request->input('month', Carbon::now()->month));
        $selectedYear = intval($request->input('year', Carbon::now()->year));

        $selectedDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $now = Carbon::now();

        // KPIs Básicos
        $propertiesCount = Property::count();
        $activeLeasesCount = Lease::where('is_active', true)->count();
        $expiringThisMonthCount = Lease::where('is_active', true)
            ->whereMonth('end_date', $selectedMonth)
            ->whereYear('end_date', $selectedYear)
            ->count();
        $pendingCollectionsCount = Collection::where('status', '!=', 'paid')->count();

        // Resumen Financiero del Mes Seleccionado
        // Cobros (Ingresos de Alquileres)
        $collectionsThisMonth = Collection::where('month', $selectedMonth)
            ->where('year', $selectedYear)
            ->get();
        $expectedIncome = $collectionsThisMonth->sum('total_amount');
        $collectedIncome = $collectionsThisMonth->sum(function ($col) {
            return $col->total_paid;
        });

        // Liquidaciones (Egresos a Propietarios)
        $settlementsThisMonth = Settlement::where('month', $selectedMonth)
            ->where('year', $selectedYear)
            ->get();
        $expectedSettlements = $settlementsThisMonth->sum('net_amount');
        $paidSettlements = $settlementsThisMonth->where('status', 'paid')->sum('net_amount');
        
        // Ganancia estimada (Comisión Inmobiliaria del mes)
        $agencyCommission = $settlementsThisMonth->sum('agency_commission');

        // Próximos Vencimientos (Próximos 3 meses)
        $upcomingExpirations = Lease::where('is_active', true)
            ->whereBetween('end_date', [$now->copy()->startOfDay(), $now->copy()->addMonths(3)->endOfDay()])
            ->orderBy('end_date', 'asc')
            ->get();

        // Actividad Reciente
        $recentActivity = Activity::latest()->take(5)->get();

        return view('welcome', compact(
            'propertiesCount',
            'activeLeasesCount',
            'expiringThisMonthCount',
            'pendingCollectionsCount',
            'expectedIncome',
            'collectedIncome',
            'expectedSettlements',
            'paidSettlements',
            'agencyCommission',
            'upcomingExpirations',
            'recentActivity',
            'selectedMonth',
            'selectedYear',
            'employee',
            'todayAttendance',
            'activeAbsenceReasons'
        ));
    }
}
