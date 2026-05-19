<?php

namespace App\Http\Controllers;

use App\Models\ExpenseDocument;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseDocumentController extends Controller
{
    public function index(Expense $expense)
    {
        return response()->json($expense->documents);
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('expenses/' . $request->expense_id, 'public');

        $doc = ExpenseDocument::create([
            'expense_id' => $request->expense_id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comprobante subido correctamente.',
                'document' => $doc
            ]);
        }

        return back()->with('success', 'Comprobante subido correctamente.');
    }

    public function destroy(ExpenseDocument $expenseDocument, Request $request)
    {
        Storage::disk('public')->delete($expenseDocument->path);
        $expenseDocument->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comprobante eliminado.'
            ]);
        }

        return back()->with('success', 'Comprobante eliminado.');
    }
}
