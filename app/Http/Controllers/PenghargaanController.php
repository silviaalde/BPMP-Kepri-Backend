<?php

namespace App\Http\Controllers;

use App\Models\Penghargaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class PenghargaanController extends Controller
{
   public function CreatePenghargaan (Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'date' => 'required|date',
                'content' => 'required|string',
                'location' => 'required|string',
                'category' => 'required|in:BPMP Prov.kepri,Pemerintahan Daerah,Satuan Pendidikan',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Menyimpan gambar di folder storage lokal
            if ($request->hasFile('image')) {
                // Mendapatkan nama file dan menyimpannya di folder 'public/content'
                $imagePath = $request->file('image')->storeAs('/content/penghargaan', time().'_'.$request->file('image')->getClientOriginalName());
                // Mendapatkan URL gambar yang bisa diakses secara publik
                $imageUrl = Storage::url($imagePath);
            } else {
                $imageUrl = null;  // Jika tidak ada gambar, set null
            }

            $penghargaan = Penghargaan::create([
                'title' => $request->title,
                'content' => $request->content,
                'date' => $request->date,
                'location' => $request->location,
                'category' => $request->category,
                'image' => $imageUrl,
            ]);

            if (!$penghargaan) {
                return response()->json([
                    'message' => 'Penghargaan creation failed'
                ], 404);
            }

            return response()->json([
                'message' => 'Content successfully created',
                'data' => $penghargaan
            ], 200);

        } catch (Exception $e) {
            // Menangani error dan menampilkan pesan error
            return response()->json([
                'message' => 'An error occurred while creating penghargaann.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function GetPenghargaan (Request $request)
    {
        try {
            $id = $request->query('id');
            $category = $request->query('category');
            $title = $request->query('title');

            $query = Penghargaan::query();

            if ($id) {
                $query->where('id', $id);
            }

            if ($category) {
                $query->where('category', $category);
            }

            if ($title) {
                $query->where('title', 'like', '%' . $title . '%');
            }

            $penghargaan = $query->get();

            if ($penghargaan->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No Penghargaan found matching the criteria'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'successfully',
                'data' => $penghargaan
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while retrieving penghargaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function DeletePenghargaan ($id)
    {
        try {
            $penghargaan = Penghargaan::find($id);
            
            if (!$penghargaan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Penghargaan not found'
                ], 404);
            }

            if ($penghargaan->image) {
                $imagePath = str_replace('/storage', 'public', $penghargaan->image);
                Storage::delete($imagePath);
            }

            $penghargaan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Penghargaan successfully deleted'
            ], 200);

        } catch (Exception $e) {
            // Menangani error
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting penghargaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function UpdatePenghargaan (Request $request, $id)
    {
        try { 
            $validateData = $request->validate([
                'title' => 'sometimes|string',
                'date' => 'sometimes|date',
                'content' => 'sometimes|string',
                'location' => 'sometimes|string',
                'category' => 'sometimes|in:BPMP Prov.kepri,Pemerintahan Daerah,Satuan Pendidikan',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $penghargaan = Penghargaan::find($id);

            if (!$penghargaan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Penghargaann not found'
                ], 404);
            }

            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($penghargaan->image) {
                    $oldImagePath = str_replace('/content/penghargaan', 'public', $penghargaan->image);
                    Storage::delete($oldImagePath);
                }

                // Simpan gambar baru
                $imagePath = $request->file('image')->storeAs('content', time() . '_' . $request->file('image')->getClientOriginalName());
                $penghargaan->image = Storage::url($imagePath); // Update path gambar baru
            }

            // Update data lainnya jika ada request
            $penghargaan->title = $validateData['title'] ?? $penghargaan->title;
            $penghargaan->date = $validateData['date'] ?? $penghargaan->date;
            $penghargaan->content = $validateData['content'] ?? $penghargaan->content;
            $penghargaan->location = $validateData['location'] ?? $penghargaan->location;
            $penghargaan->category = $validateData['category'] ?? $penghargaan->category;
            $penghargaan->save();

            return response()->json([
                'status' => true,
                'message' => 'Penghargaan successfully updated',
                'data' => $penghargaan
            ], 200);

        } catch (Exception $e) {
            // Menangani error
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while Update penghargaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
