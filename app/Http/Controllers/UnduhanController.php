<?php

namespace App\Http\Controllers;

use App\Models\Unduhan;
use Illuminate\Http\Request;
use Exception;
use App\Models\FileUnduhan;
use Illuminate\Support\Facades\Storage;

class UnduhanController extends Controller
{
    public function GetAll (Request $request) 
    {
        try {
            // Get the search queries (if any) from the request
            $title = $request->query('title');
            $category = $request->query('category');
            $id = $request->query('id');
            

            // If a title and/or category query exists, apply filters
            $unduhanQuery = Unduhan::with('files');


            if ($id) {
                $unduhanQuery->where('id', $id);
            }

            if ($title) {
                // Filter by title if 'title' query exists
                $unduhanQuery->where('title', 'like', '%' . $title . '%');
            }

            if ($category) {
                // Filter by category if 'category' query exists
                $unduhanQuery->where('category', 'like', '%' . $category . '%');
            }



            // Get the filtered results
            $unduhan = $unduhanQuery->get();

            return response()->json([
                'message' => 'success',
                'data' => $unduhan
            ], 200);
        } catch (Exception $e) {
            // Handle error and return error message
            return response()->json([
                'message' => 'An error occurred while fetching Kegiatan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function CreateUnduhan (Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string',
                'date' => 'required|date',
                'fileunduhan' => 'required|array',
                'fileunduhan.*.file' => 'required|mimes:pdf,jpg,jpeg,png|max:1024000',
                'fileunduhan.*.title' => 'required|string',
                'filunduhan.*.size' => 'required|string'
            ]);

            $unduhan = Unduhan::create([
                'title' => $request->title,
                'content' => $request->content,
                'category' => $request->category,
                'date' => $request->date,
            ]);

            if ($unduhan) {
                foreach ($request->fileunduhan as $fileData) {
                    try {
                        $file = $fileData['file'];

                        $filePath = $file->storeAs('/content/document', uniqid());
                        
                        $fileUrl = Storage::url($filePath);

                        $fileTitle = $fileData['title'];
                        $fileSize = $fileData['size'];

                        FileUnduhan::create([
                            'file' => $fileUrl,
                            'title' => $fileTitle,
                            'size' => $fileSize,
                            'unduhan_id' => $unduhan->id,
                        ]);

                    } catch (Exception $e) {
                        // Menangani error setiap gambar yang gagal disimpan
                        return response()->json([
                            'message' => 'An error occurred while saving the file.',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                }
            }

            return response()->json([
                'message' => 'Unduhan berhasil dibuat!',
                'kegiatan' => $unduhan
            ], 200);
        } catch (Exception $e) {
            // Menangani error dan menampilkan pesan error
            return response()->json([
                'message' => 'An error occurred while creating Kegiatan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function DeleteUnduhan ($id)
    {
        try {
            $unduhan = Unduhan::find($id);

            if (!$unduhan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Unduhan not found'
                ], 404);
            }

            $unduhan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Unduhan successfully deleted'
            ], 200);
        } catch (Exception $e) {
            // Menangani error
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting Unduhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function UpdateUnduhan (Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'sometimes|string',
                'content' => 'sometimes|string',
                'date' => 'sometimes|date',
                'category' => 'sometimes|string',
            ]);

            $unduhan = Unduhan::find($id);

            if (!$unduhan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unduhan not found'
                ], 404);
            };

            $unduhan->title = $validatedData['title'] ?? $unduhan->title;
            $unduhan->content = $validatedData['content'] ?? $unduhan->content;
            $unduhan->date = $validatedData['date'] ?? $unduhan->date;
            $unduhan->category = $validatedData['category'] ?? $unduhan->category;

            $unduhan->save();

            return response()->json([
                'status' => true,
                'message' => 'Unduhan successfully updated',
                'unduhan' => $unduhan
            ]);

        } catch (Exception $e) {
            // Menangani error
            return response()->json([
                'status' => false,
                'message' => 'An error update Unduhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
