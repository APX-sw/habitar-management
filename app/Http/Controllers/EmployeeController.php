<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\User;
use App\Models\IndexType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->paginate(15);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $users = User::all();
        $indexTypes = IndexType::all();
        return view('employees.create', compact('users', 'indexTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'document_number' => 'required|string|max:50',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'hire_date' => 'required|date',
            'job_title' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'cbu_alias' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'update_type' => 'nullable|in:fixed,indexed',
            'update_frequency_months' => 'nullable|integer|min:1|max:24',
            'increase_index_id' => 'nullable|exists:index_types,id',
            'increase_fixed_percentage' => 'nullable|numeric|min:0',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Legajo de empleado creado con éxito.');
    }

    public function show(Employee $employee)
    {
        $employee->load('documents', 'attendances');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $users = User::all();
        $indexTypes = IndexType::all();
        return view('employees.edit', compact('employee', 'users', 'indexTypes'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'document_number' => 'required|string|max:50',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'hire_date' => 'required|date',
            'job_title' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'cbu_alias' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'update_type' => 'nullable|in:fixed,indexed',
            'update_frequency_months' => 'nullable|integer|min:1|max:24',
            'increase_index_id' => 'nullable|exists:index_types,id',
            'increase_fixed_percentage' => 'nullable|numeric|min:0',
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Legajo de empleado actualizado con éxito.');
    }

    public function destroy(Employee $employee)
    {
        // Delete document files first
        foreach ($employee->documents as $document) {
            Storage::delete($document->file_path);
            $document->delete();
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Legajo de empleado eliminado con éxito.');
    }

    // Document Management
    public function storeDocument(Request $request, Employee $employee)
    {
        $request->validate([
            'document_type' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB Limit
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('employee_documents');

            EmployeeDocument::create([
                'employee_id' => $employee->id,
                'document_type' => $request->document_type,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);

            return back()->with('success', 'Documento subido correctamente.');
        }

        return back()->with('error', 'Error al subir el documento.');
    }

    public function destroyDocument(EmployeeDocument $document)
    {
        Storage::delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Documento eliminado correctamente.');
    }
}
