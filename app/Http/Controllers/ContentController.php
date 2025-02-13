<?php

// app/Http/Controllers/ContentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Content;
use Exception; // Import Exception class

class ContentController extends Controller
{
    public function CreateContent(Request $request)
    {
        try {
            // Validasi data yang dikirimkan
            $request->validate([
                'name' => 'required|string',
                'title' => 'required|string',
                'date' => 'required|date',
                'article' => 'required|string',
                'category' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|in:published,draft,archived', // Validasi gambar
            ]);

            // Menyimpan gambar di folder storage lokal
            if ($request->hasFile('image')) {
                // Mendapatkan nama file dan menyimpannya di folder 'public/content'
                $imagePath = $request->file('image')->storeAs('/content/blog', time().'_'.$request->file('image')->getClientOriginalName());
                // Mendapatkan URL gambar yang bisa diakses secara publik
                $imageUrl = Storage::url($imagePath);
            } else {
                $imageUrl = null;  // Jika tidak ada gambar, set null
            }

            // Menyimpan data konten ke database
            $content = Content::create([
                'name' => $request->name,
                'title' => $request->title,
                'date' => $request->date,
                'category' => $request->category,
                'article' => $request->article,
                'image' => $imageUrl, 
                'status' => $request->status, // Menyimpan URL gambar di database
            ]);

            // Jika gagal menyimpan
            if (!$content) {
                return response()->json([
                    'message' => 'Content creation failed'
                ], 404);
            }

            // Jika berhasil
            return response()->json([
                'message' => 'Content successfully created',
                'data' => $content
            ], 200);

        } catch (Exception $e) {
            // Menangani error dan menampilkan pesan error
            return response()->json([
                'message' => 'An error occurred while creating content.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function UpdateContent(Request $request, $id)
    {
        try {
            // Validasi data yang dikirimkan (hanya kolom yang ingin diperbarui)
            $validatedData = $request->validate([
                'name' => 'sometimes|string',
                'title' => 'sometimes|string',
                'date' => 'sometimes|date',
                'article' => 'sometimes|string',
                'category' => 'sometimes|string',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
                'status' => 'sometimes|in:published,draft,archived',
            ]);

            // Cari data konten berdasarkan ID
            $content = Content::find($id);

            if (!$content) {
                return response()->json([
                    'status' => false,
                    'message' => 'Content not found'
                ], 404);
            }

            // Update gambar jika ada
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($content->image) {
                    $oldImagePath = str_replace('/storage', 'public', $content->image);
                    Storage::delete($oldImagePath);
                }

                // Simpan gambar baru
                $imagePath = $request->file('image')->storeAs('content', time() . '_' . $request->file('image')->getClientOriginalName());
                $content->image = Storage::url($imagePath); // Update path gambar baru
            }

            // Update kolom lainnya jika ada di request
            $content->name = $validatedData['name'] ?? $content->name;
            $content->title = $validatedData['title'] ?? $content->title;
            $content->date = $validatedData['date'] ?? $content->date;
            $content->category = $validatedData['category'] ?? $content->category;
            $content->article = $validatedData['article'] ?? $content->article;
            $content->status = $validatedData['status'] ?? $content->status;

            // Simpan perubahan ke database
            $content->save();

            return response()->json([
                'status' => true,
                'message' => 'Content successfully updated',
                'data' => $content
            ], 200);
        } catch (Exception $e) {
            // Menangani error
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating content.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function DeleteContent($id)
    {
        try {
            // Cari data berdasarkan ID
            $content = Content::find($id);

            if (!$content) {
                return response()->json([
                    'status' => false,
                    'message' => 'Content not found'
                ], 404);
            }

            // Hapus gambar dari storage jika ada
            if ($content->image) {
                $imagePath = str_replace('/storage', 'public', $content->image);
                Storage::delete($imagePath);
            }

            // Hapus data dari database
            $content->delete();

            return response()->json([
                'status' => true,
                'message' => 'Content successfully deleted'
            ], 200);

        } catch (Exception $e) {
            // Menangani error
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting content.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getContentCount()
    {
        // Menghitung jumlah total konten
        // Daftar kategori yang ingin dihitung
        $categories = ['Berita', 'Opini', 'Artikel'];

        // Menghitung jumlah konten untuk setiap kategori
        $categoryCounts = [];
        foreach ($categories as $category) {
            $categoryCounts[$category] = Content::where('category', $category)->count();
        }

        // Menghitung jumlah total konten
        $totalCount = Content::count();

        return response()->json([
            'status' => true,
            'message' => 'Jumlah konten per kategori dan total konten',
            'data' => [
                'category_counts' => $categoryCounts,
                'total_count' => $totalCount
            ]
        ], 200);
    }

    public function GetContents(Request $request)
    {
        try {
            // Ambil query parameters
            $id = $request->query('id');
            $name = $request->query('name');
            $category = $request->query('category');
            $status = $request->query('status');

            // Query dasar
            $query = Content::query();

        
            if ($id) {
                $query->where('id', $id);
            }

            if ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            }

            if ($category) {
                $query->where('category', $category);
            }

            if ($status) {
                $query->where('status', $status);
            }

            // Ambil hasil query
            $contents = $query->get();

            // Jika tidak ada data
            if ($contents->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No content found matching the criteria'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Content retrieved successfully',
                'data' => $contents
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while retrieving content.',
                'error' => $e->getMessage()
            ], 500);
        }
    }    
}
