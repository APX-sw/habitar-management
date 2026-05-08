<?php

namespace App\Http\Controllers;

use App\Models\LeaseDocument;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaseDocumentController extends Controller
{
    public function index(Lease $lease)
    {
        return response()->json($lease->documents);
    }

    public function store(Request $request)
    {
        $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('leases/' . $request->lease_id, 'public');

        $doc = LeaseDocument::create([
            'lease_id' => $request->lease_id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return response()->json([
            'success' => true,
            'document' => $doc
        ]);
    }

    public function destroy(LeaseDocument $leaseDocument)
    {
        Storage::disk('public')->delete($leaseDocument->path);
        $leaseDocument->delete();

        return response()->json(['success' => true]);
    }
}
