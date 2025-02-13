<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Faq;
use App\Models\Kegiatan;
use App\Models\Unduhan;
use Illuminate\Http\Request;
use Exception;

class StatsController extends Controller
{
    public function GetStats() 
    {
        try {
            // List of categories you want to count the content for
            $categories = ['Berita', 'Opini', 'Artikel'];

            // Initialize an array to store category counts
            $categoryCounts = [];

            // Loop through each category and count the content
            foreach ($categories as $category) {
                $categoryCounts[$category] = Content::where('category', $category)->count();
            }

            // Count the total number of content
            $totalCount = Content::count();

            $kegiatanCount = Kegiatan::count();

            $unduhanCount = Unduhan::count();

            $faqCount = Faq::count();

            // Return the data as a JSON response
            return response()->json([
                'BlogcategoryCount' => $categoryCounts,
                'BlogCount' => $totalCount,
                'kegiatanCount' => $kegiatanCount,
                'unduhanCount' => $unduhanCount,
                'faqCount' => $faqCount
            ], 200);
        } catch (Exception $e) {
            // Menangani error dan menampilkan pesan error
            return response()->json([
                'message' => 'An error get stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
