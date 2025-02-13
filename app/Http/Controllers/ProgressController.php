<?php

namespace App\Http\Controllers;

use App\Models\progress;
use Illuminate\Http\Request;
use Exception;

class ProgressController extends Controller
{
    public function CreateProgress (Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'realisasi_anggaran' => 'required|numeric|min:0|max:100',
                'evaluasi_anggaran' => 'required|numeric|min:0|max:100',
                'date' => 'required|date|unique:progress,date',
            ]);

            // Extract bulan dan tahun dari input date
            $date = $validated['date'];
            $month = date('m', strtotime($date));
            $year = date('Y', strtotime($date));

             // Cek apakah sudah ada data dengan bulan dan tahun yang sama
            $existingProgress = progress::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->first();

            if ($existingProgress) {
                return response()->json([
                    'message' => 'Data for this month and year already exists.',
                ], 400);
            }

            // Buat data baru
            $progress = Progress::create([
                'realisasi_anggaran' => $validated['realisasi_anggaran'],
                'evaluasi_anggaran' => $validated['evaluasi_anggaran'],
                'date' => $validated['date'],
            ]);

            return response()->json([
                'message' => 'Progress created successfully.',
                'data' => $progress,
            ], 200);

        } catch (Exception $e) {
            // Handle error and return error message
            return response()->json([
                'message' => 'An error create progress.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function GetProgress(Request $request) 
    {
        try {
            $id = $request->query('id');
            $month = $request->query('month'); // Ambil query bulan
            $year = $request->query('year');   // Ambil query tahun
    
            $progress = progress::query();
    
            // Filter berdasarkan ID jika ada
            if ($id) {
                $progress->where('id', $id);
            }
    
            // Filter berdasarkan bulan jika ada
            if ($month) {
                $progress->whereMonth('date', $month);
            }
    
            // Filter berdasarkan tahun jika ada
            if ($year) {
                $progress->whereYear('date', $year);
            }
    
            // Ambil data
            $progress = $progress->get();
    
            // Jika data kosong, kembalikan respons 404
            if ($progress->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No progress found matching the criteria'
                ], 404);
            }
    
            // Kembalikan data jika ditemukan
            return response()->json([
                'status' => true,
                'message' => 'Successfully.',
                'data' => $progress
            ], 200);
        } catch (Exception $e) {
            // Handle error dan kembalikan pesan error
            return response()->json([
                'message' => 'An error occurred while getting progress.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function DeleteProgress($id)
    {
        try {
            $progress = progress::find($id);

            if (!$progress) {
                return response()->json([
                    'status' => false,
                    'message' => 'Progress not found'
                ], 404);
            }

            $progress->delete();

            return response()->json([
                'status' => true,
                'message' => 'Progress deleted successfully.'
            ], 200);
        } catch (Exception $e) {
            // Handle error and return error message
            return response()->json([
                'message' => 'An error Get progress.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function UpdateProgress(Request $request, $id)
    {
        try {
            $request->validate([
                'realisasi_anggaran' => 'required|numeric|min:0|max:100',
                'evaluasi_anggaran' => 'required|numeric|min:0|max:100',
                'date' => 'required|date',
            ]);

            $progress = progress::find($id);

            if (!$progress) {
                return response()->json([
                    'status' => false,
                    'message' => 'Progress not found'
                ], 404);
            }

            $progress->realisasi_anggaran = $request->realisasi_anggaran;
            $progress->evaluasi_anggaran = $request->evaluasi_anggaran;
            $progress->date = $request->date;

            $progress->save();  

            return response()->json([
                'status' => true,
                'message' => 'Progress updated successfully.',
                'data' => $progress
            ], 200);
        } catch (Exception $e) {
            // Handle error and return error message
            return response()->json([
                'message' => 'An error Update progress.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
