<?php

namespace App\Http\Controllers;

use App\Models\ImageKegiatan;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ImageKegiatanController extends Controller
{
    public function DeleteImageKegiatan($id) 
    {
        try {
            $image = ImageKegiatan::find($id);

            if (!$image) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Image Kegiatan not found'
                ], 404);
            };

            $image->delete();

            return response()->json([
                'status' => true,
                'message' => 'Image Kegiatan successfully deleted'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting Image Kegiatan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function CreateImageKegiatan (Request $request) 
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480',
                'name' => 'required|string',
                'kegiatan_id' => [
                    'required',
                    'uuid',
                    Rule::exists('kegiatan', 'id') // Validasi foreign key
                ]
            ]);

            if ($request->hasFile('image')) {
                // Mendapatkan nama file dan menyimpannya di folder 'public/content'
                $imagePath = $request->file('image')->storeAs('/content/kegiatan', time().'_'.$request->file('image')->getClientOriginalName());
                // Mendapatkan URL gambar yang bisa diakses secara publik
                $imageUrl = Storage::url($imagePath);
            } else {
                $imageUrl = null;  // Jika tidak ada gambar, set null
            }

            $imageKegiatan = ImageKegiatan::create([
                'image' => $imageUrl,
                'name' => $request->name,
                'kegiatan_id' => $request->kegiatan_id,
            ]);

            if (!$imageKegiatan) {
                return response()->json([
                    'status' => false,
                    'message' => 'An error Create Image Kegiatan',
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Image Kegiatan successfully created',
                'data' => $imageKegiatan
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error Create Image Kegiatan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
