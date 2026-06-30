<?php

namespace App\Http\Controllers;

use App\Models\AbsenceReason;
use Illuminate\Http\Request;

class AbsenceReasonController extends Controller
{
    public function index()
    {
        $reasons = AbsenceReason::paginate(15);
        return view('absence_reasons.index', compact('reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        AbsenceReason::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('absence-reasons.index')
            ->with('success', 'Motivo de ausencia agregado con éxito.');
    }

    public function update(Request $request, AbsenceReason $absenceReason)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'required|boolean',
        ]);

        $absenceReason->update($request->all());

        return redirect()->route('absence-reasons.index')
            ->with('success', 'Motivo de ausencia actualizado con éxito.');
    }

    public function destroy(AbsenceReason $absenceReason)
    {
        // Simple check to prevent deleting reasons used in attendances
        if ($absenceReason->attendances()->count() > 0) {
            return redirect()->route('absence-reasons.index')
                ->with('error', 'No se puede eliminar este motivo porque ya está registrado en asistencias. Podés desactivarlo en su lugar.');
        }

        $absenceReason->delete();

        return redirect()->route('absence-reasons.index')
            ->with('success', 'Motivo de ausencia eliminado con éxito.');
    }
}
