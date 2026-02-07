<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CKEditorUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:10240', // 10MB max
        ]);

        $file = $request->file('upload');
        $path = $file->store('ckeditor-uploads', 'public');

        if (!$path) {
            return response()->json([
                'uploaded' => false,
                'error' => [
                    'message' => 'Upload failed'
                ]
            ], 500);
        }

        $url = Storage::disk('public')->url($path);

        // CKEditor 4 expects this response format
        $funcNum = $request->input('CKEditorFuncNum');
        $message = 'Image uploaded successfully';

        return "<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
    }
}
