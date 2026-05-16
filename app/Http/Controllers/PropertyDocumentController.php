<?php

namespace App\Http\Controllers;

use App\Models\PropertyDocument;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyDocumentController extends Controller
{
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

        return back()->with('success', 'Documento subido correctamente.');
    }

    public function destroy(PropertyDocument $propertyDocument)
    {
        Storage::disk('public')->delete($propertyDocument->path);
        $propertyDocument->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
