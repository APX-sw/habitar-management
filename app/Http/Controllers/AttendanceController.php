<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\AbsenceReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Administrative Report & View (Task 6)
     */
    public function index(Request $request)
    {
        $query = Attendance::with('employee', 'absenceReason');

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Filter by Employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Absence Reason
        if ($request->filled('absence_reason_id')) {
            $query->where('absence_reason_id', $request->absence_reason_id);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);
        
        $employees = Employee::orderBy('first_name')->get();
        $reasons = AbsenceReason::where('is_active', true)->get();

        return view('attendances.report', compact('attendances', 'employees', 'reasons'));
    }

    /**
     * Employee Check-In (Task 5)
     */
    public function checkIn(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            return back()->with('error', 'Tu usuario no tiene un legajo de empleado asignado. Contactá al administrador.');
        }

        $today = date('Y-m-d');

        // Check if already checked in today
        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            return back()->with('error', 'Ya registraste tu asistencia para el día de hoy.');
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'status' => 'present',
        ]);

        return back()->with('success', '¡Ingreso marcado con éxito! Que tengas una excelente jornada laboral.');
    }

    /**
     * Employee Check-Out
     */
    public function checkOut(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            return back()->with('error', 'Tu usuario no tiene un legajo de empleado asignado.');
        }

        $today = date('Y-m-d');

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->where('status', 'present')
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No registraste un ingreso para el día de hoy.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Ya registraste tu salida del día de hoy.');
        }

        $attendance->update([
            'check_out' => date('H:i:s')
        ]);

        return back()->with('success', '¡Salida registrada con éxito! Que tengas un excelente descanso.');
    }

    /**
     * Employee Report Absence (Task 5)
     */
    public function reportAbsence(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'absence_reason_id' => 'required|exists:absence_reasons,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            return back()->with('error', 'Tu usuario no tiene un legajo de empleado asignado.');
        }

        // Check if there is already an attendance/absence recorded for that date
        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existing) {
            return back()->with('error', 'Ya hay una asistencia o ausencia registrada para la fecha seleccionada.');
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $request->date,
            'status' => 'absent',
            'absence_reason_id' => $request->absence_reason_id,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Ausencia reportada con éxito.');
    }

    /**
     * Tablero Oficina - Real-time daily overview (Task 2)
     */
    public function office(Request $request)
    {
        $dateStr = $request->input('date', date('Y-m-d'));
        
        try {
            $date = \Carbon\Carbon::parse($dateStr);
        } catch (\Exception $e) {
            $date = \Carbon\Carbon::today();
        }
        
        $formattedDate = $date->format('Y-m-d');
        
        // Load all employees
        $employees = Employee::orderBy('first_name')->get();
        
        // Load attendances for the given date
        $attendances = Attendance::with('absenceReason')
            ->whereDate('date', $formattedDate)
            ->get()
            ->keyBy('employee_id');
            
        $pendientes = [];
        $enOficina = [];
        $retirados = [];
        $ausentes = [];
        
        foreach ($employees as $employee) {
            $attendance = $attendances->get($employee->id);
            
            if (!$attendance) {
                $pendientes[] = [
                    'employee' => $employee,
                    'attendance' => null,
                ];
            } elseif ($attendance->status === 'present') {
                if (is_null($attendance->check_out)) {
                    $enOficina[] = [
                        'employee' => $employee,
                        'attendance' => $attendance,
                    ];
                } else {
                    $retirados[] = [
                        'employee' => $employee,
                        'attendance' => $attendance,
                    ];
                }
            } elseif ($attendance->status === 'absent') {
                $ausentes[] = [
                    'employee' => $employee,
                    'attendance' => $attendance,
                ];
            }
        }
        
        return view('attendances.office', compact(
            'formattedDate',
            'date',
            'pendientes',
            'enOficina',
            'retirados',
            'ausentes'
        ));
    }
}

