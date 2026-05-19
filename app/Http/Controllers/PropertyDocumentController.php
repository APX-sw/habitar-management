<?php

namespace App\Http\Controllers;

use App\Models\PropertyDocument;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyDocumentController extends Controller
{
    public function index(Property $property)
    {
        return response()->json($property->documents);
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('properties/' . $request->property_id, 'public');

        $doc = PropertyDocument::create([
            'property_id' => $request->property_id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Documento subido correctamente.',
                'document' => $doc
            ]);
        }

        return back()->with('success', 'Documento subido correctamente.');
    }

    public function destroy(PropertyDocument $propertyDocument, Request $request)
    {
        Storage::disk('public')->delete($propertyDocument->path);
        $propertyDocument->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Documento eliminado.'
            ]);
        }

        return back()->with('success', 'Documento eliminado.');
    }
}
