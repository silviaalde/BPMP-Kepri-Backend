<?php

namespace App\Http\Controllers;

use App\Models\FileUnduhan;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;

class FileUnduhanController extends Controller
{
    public function DeleteFileUnduhan ($id)
    {
        try {
            $file = FileUnduhan::find($id);

            if (!$file) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data File Unduhan not found'
                ], 404);
            };

            $file->delete();

            return response()->json([
                'status' => true,
                'message' => 'File Unduhan successfully deleted'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting File Unduhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function CreateFileUnduhan(Request $request) 
    {
        try {
            $request->validate([
                'file' => 'required|mimes:pdf,jpg,jpeg,png|max:1024000',
                'title' => 'required | string',
                'size' => 'required | string',
                'unduhan_id' => 'required | string',
            ]);

            if ($request->hasFile('file')) {
                // Mendapatkan nama file dan menyimpannya di folder 'public/content'
                $filePath = $request->file('file')->storeAs('/content/document', uniqid());
                // Mendapatkan URL gambar yang bisa diakses secara publik
                $fileUrl = Storage::url($filePath);
            } else {
                $fileUrl = null;  // Jika tidak ada gambar, set null
            }

            $fileUnduhan = FileUnduhan::create([
                'file' => $fileUrl,
                'title' => $request->title,
                'size' => $request->size,
                'unduhan_id' => $request->unduhan_id
            ]);

            if (!$fileUnduhan) {
                return response()->json([
                    'message' => 'File Unduhan creation failed'
                ], 404);
            }

             // Jika berhasil
             return response()->json([
                'message' => 'File Unduhan successfully created',
                'data' => $fileUnduhan
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error creat File Unduhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
