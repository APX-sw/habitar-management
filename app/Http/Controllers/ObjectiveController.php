<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objective;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class ObjectiveController extends Controller
{
    /**
     * Admin: List all objectives
     */
    public function index(Request $request)
    {
        $query = Objective::with(['employee', 'creator'])->orderBy('created_at', 'desc');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $objectives = $query->paginate(15);
        $employees = Employee::orderBy('last_name')->get();

        return view('objectives.index', compact('objectives', 'employees'));
    }

    /**
     * Admin: Store a newly created objective in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'period' => 'required|in:daily,weekly,monthly',
            'due_date' => 'nullable|date',
        ]);

        $validated['creator_id'] = Auth::id();
        $validated['status'] = 'pending';

        Objective::create($validated);

        return back()->with('success', 'Objetivo creado correctamente.');
    }

    /**
     * Employee: Store a newly created objective from workspace.
     */
    public function employeeStore(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'period' => 'required|in:daily,weekly,monthly',
            'due_date' => 'nullable|date',
        ]);

        $validated['employee_id'] = $employee->id;
        $validated['creator_id'] = Auth::id();
        $validated['status'] = 'pending';

        Objective::create($validated);

        return redirect()->route('workspace.index')->with('success', 'Objetivo creado correctamente.');
    }

    /**
     * Admin: Remove the specified objective from storage.
     */
    public function destroy(Objective $objective)
    {
        $objective->delete();
        return back()->with('success', 'Objetivo eliminado correctamente.');
    }

    /**
     * Admin: Store feedback (admin_comment)
     */
    public function storeFeedback(Request $request, Objective $objective)
    {
        $request->validate([
            'admin_comment' => 'required|string'
        ]);

        $timestamp = now()->format('d/m/Y H:i');
        $newComment = "[{$timestamp}] " . $request->admin_comment;
        $updatedComment = $objective->admin_comment ? $newComment . "\n\n" . $objective->admin_comment : $newComment;

        $objective->update([
            'admin_comment' => $updatedComment
        ]);

        return back()->with('success', 'Feedback guardado correctamente.');
    }

    /**
     * Employee: Update status
     */
    public function updateStatus(Request $request, Objective $objective)
    {
        // Ensure the logged in user is the owner of this objective
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();
        if ($objective->employee_id !== $employee->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $objective->update([
            'status' => $request->status
        ]);

        return redirect()->route('workspace.index')->with('success', 'Estado del objetivo actualizado.');
    }

    /**
     * Employee: Update notes
     */
    public function updateNotes(Request $request, Objective $objective)
    {
        // Ensure the logged in user is the owner of this objective
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();
        if ($objective->employee_id !== $employee->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'employee_notes' => 'required|string'
        ]);

        $timestamp = now()->format('d/m/Y H:i');
        $newNote = "[{$timestamp}] " . $request->employee_notes;
        $updatedNotes = $objective->employee_notes ? $newNote . "\n\n" . $objective->employee_notes : $newNote;

        $objective->update([
            'employee_notes' => $updatedNotes
        ]);

        return redirect()->route('workspace.index')->with('success', 'Notas del objetivo actualizadas.');
    /**
     * Store a new comment/note for the objective
     */
    public function storeComment(Request $request, Objective $objective)
    {
        $request->validate([
            'comment' => 'required|string',
            'attachment' => 'nullable|file|max:10240' // 10MB
        ]);

        $comment = new \App\Models\ObjectiveComment([
            'objective_id' => $objective->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName();
            $path = $file->store('objective_attachments', 'public');
            
            $comment->file_name = $fileName;
            $comment->file_path = $path;
        }

        $comment->save();

        return back()->with('success', 'Comentario guardado correctamente.');
    }
}
