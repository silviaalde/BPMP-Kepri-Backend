<?php

namespace App\Http\Controllers;

use App\Models\ImageKegiatan;
use App\Models\Kegiatan;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KegiatanController extends Controller
{
    public function GetAll(Request $request) 
    {
        try {
            // Get the search query parameters
            $id = $request->query('id');
            $title = $request->query('title'); 

            // Initialize the query with the Kegiatan model
            $query = Kegiatan::with('imageKegiatan');

            // Apply filters conditionally
            if ($id) {
                $query->where('id', $id);
            }

            if ($title) {
                $query->where('title', 'like', '%' . $title . '%');
            }

            // Execute the query
            $kegiatan = $query->get();

            // Return success response
            return response()->json([
                'message' => 'success',
                'data' => $kegiatan
            ], 200);

        } catch (Exception $e) {
            // Handle error and return error response
            return response()->json([
                'message' => 'An error occurred while fetching Kegiatan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function CreateKegiatan(Request $request)
    {
        try {

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|date',
                'location' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'images' => 'required|array',  // Validasi array images
                'images.*.file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Validasi gambar
                'images.*.title' => 'required|string|max:255', 
            ]);

            // Membuat kegiatan baru
            $kegiatan = Kegiatan::create([
                'title' => $request->title,
                'description' => $request->description,
                'date' => $request->date,
                'location' => $request->location,
                'department' => $request->department,
            ]);

            if($kegiatan) {
                foreach ($request->images as $imageData) {
                    try {
                        $imageFile = $imageData['file'];
            
                        // Simpan gambar di folder 'content', dengan nama file yang unik
                        $imagePath = $imageFile->storeAs('/content/kegiatan', uniqid() . '_' . $imageFile->getClientOriginalName());
            
                        // Mendapatkan URL gambar yang bisa diakses secara publik
                        $imageUrl = Storage::url($imagePath);
            
                        // Ambil title dari imageData
                        $imageTitle = $imageData['title'];

                        ImageKegiatan::create([
                            'name' => $imageTitle,
                            'image' => $imageUrl,
                            'kegiatan_id' => $kegiatan->id, // Mengaitkan dengan id kegiatan yang baru dibuat
                        ]);
                    } catch (Exception $e) {
                        // Menangani error setiap gambar yang gagal disimpan
                        
                        return response()->json([
                            'message' => 'An error occurred while saving the image.',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                }
                
            }
            

            return response()->json([
                'message' => 'Kegiatan berhasil dibuat!',
                'kegiatan' => $kegiatan
            ], 200);

            
        } catch (Exception $e) {
            // Menangani error dan menampilkan pesan error
            return response()->json([
                'message' => 'An error occurred while creating Kegiatan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function DeleteKegiatan($id) 
    {
        try {
            $kegiatan = Kegiatan::find($id);

            if (!$kegiatan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kegiatan not found'
                ], 404);
            }

            $kegiatan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Kegiatan successfully deleted'
            ], 200);

        } catch (Exception $e) {
            // Menangani error
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting kegiatan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function UpdateKegiatan(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'sometimes|string',
                'description' => 'sometimes|string',
                'date' => 'sometimes|date',
                'location' => 'sometimes|string',
                'department' => 'sometimes|string',
            ]);

            $kegiatan = Kegiatan::find($id);

            if (!$kegiatan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kegiatan not found'
                ], 404);
            };
            
            $kegiatan->title = $validatedData['title'] ?? $kegiatan->title;
            $kegiatan->description = $validatedData['description'] ?? $kegiatan->description;
            $kegiatan->date = $validatedData['date'] ?? $kegiatan->date;
            $kegiatan->location = $validatedData['location'] ?? $kegiatan->location;
            $kegiatan->department = $validatedData['department'] ?? $kegiatan->department;

            $kegiatan->save();

            return response()->json([
                'status' => true,
                'message' => 'Kegiatan successfully updated',
                'kegiatan' => $kegiatan
            ]);
        } catch (Exception $e) {
            // Menangani error
            return response()->json([
                'status' => false,
                'message' => 'An error Update Kegiatan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
