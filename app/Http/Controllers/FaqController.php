<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Exception;

class FaqController extends Controller
{
    public function CreateFaq(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string'
            ]);   

            $faq = Faq::create([
                'title' => $request->title,
                'content' => $request->content,
                'category' => $request->category,
            ]);

            if (!$faq) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create Faq.'
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Faq created successfully.',
                'faq' => $faq
            ], 200);
        } catch (Exception $e) {
            // Handle error and return error response
            return response()->json([
                'message' => 'An error create Faq.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function GetFaq (Request $request)
    {
        try {
            $id = $request->query('id');
            $category = $request->query('category');

            $query = Faq::query();

            if ($id) {
                $query->where('id', $id);
            }

            if ($category) {
                $query->where('category', $category);
            }

            $faq = $query->get();

            if ($faq->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No Faq found matching the criteria'
                ], 404);
            };

            return response()->json([
                'status' => true,
                'message' => 'successfully.',
                'data' => $faq
            ], 200);
        } catch (Exception $e) {
            // Handle error and return error response
            return response()->json([
                'message' => 'An error occurred while fetching Faq.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function DeleteFaq ($id)
    {
        try {
            $faq = Faq::find($id);
            
            if (!$faq) {
                return response()->json([
                    'status' => false,
                    'message' => 'Faq not found'
                ], 404);
            }

            $faq->delete();

            return response()->json([
                'status' => true,
                'message' => 'Faq successfully deleted'
            ], 200);

        } catch (Exception $e) {
            // Menangani error
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting Faq.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function UpdateFaq(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string'
            ]);

            $faq = Faq::find($id);

            if (!$faq) {
                return response()->json([
                    'status' => false,
                    'message' => 'Faq not found'
                ], 404);
            }

            $faq->title = $request->title;
            $faq->content = $request->content;
            $faq->category = $request->category;
            $faq->save();

            return response()->json([
                'status' => true,
                'message' => 'Faq updated successfully.',
                'faq' => $faq
            ], 200);
        } catch (Exception $e) {
            // Handle error and return error response
            return response()->json([
                'message' => 'An error occurred while updating Faq.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


