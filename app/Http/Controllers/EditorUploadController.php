<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EditorUploadController extends Controller
{
    /**
     * Minimal public-disk upload handler for Filament RichEditor attachFiles.
     *
     * Returns JSON: { "url": "https://.../storage/editor/filename.ext" }
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480|mimes:jpeg,png,jpg,gif,webp,svg,mp4,webm,ogg',
        ]);

        $uploaded = $request->file('file');

        // store on public disk under editor/uploads
        $path = $uploaded->store('editor/uploads', 'public');

        if (! $path) {
            throw ValidationException::withMessages(['file' => __('The file could not be stored.')]);
        }

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ], 201);
    }
}
