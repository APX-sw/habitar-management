<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Objective;
use App\Models\Attendance;
use App\Models\AbsenceReason;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        
        if (!$employee) {
            return redirect('/')->with('error', 'No tienes un legajo de empleado asignado para acceder al Workspace.');
        }

        $todayAttendance = Attendance::where('employee_id', $employee->id)
                                     ->whereDate('date', date('Y-m-d'))
                                     ->first();
                                     
        $activeAbsenceReasons = AbsenceReason::where('is_active', true)->get();
        
        $objectives = Objective::where('employee_id', $employee->id)
                               ->orderBy('created_at', 'desc')
                               ->get();

        return view('workspace.index', compact('employee', 'todayAttendance', 'activeAbsenceReasons', 'objectives'));
    }
}
