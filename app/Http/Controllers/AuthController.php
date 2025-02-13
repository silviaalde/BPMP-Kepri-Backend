<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{
    public function Login(Request $request) 
    {
        try {
            // Validasi input dari request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 422);
            }

            // Cek apakah email terdaftar
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'error' => 'User not found.'
                ], 404);
            }

            // Cek apakah password yang diberikan benar
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => 'Invalid credentials.'
                ], 401);
            }

            // Generate token (contoh: menggunakan random string untuk token)
            $token = $user->createToken('authToken')->plainTextToken;

            // Simpan token ke database
            $user->token = $token;
            $user->save();

            // Return token sebagai bagian dari response
            return response()->json([
                'status_code' => 200,
                'message' => 'User logged in successfully!',
                'data' => [
                    'token' => $token,
                    'id' => $user->id
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(), // Menyertakan stack trace
                'input_data' => $request->all(), // Menyertakan data yang dikirimkan dalam request
            ]);
            // Menangkap kesalahan dan mengembalikan response dengan error 500
            return response()->json([
                'error' => 'Something went wrong during registration.',
                'message' => $e->getMessage() // Menampilkan pesan kesalahan dari exception
            ], 500); // 500 untuk internal server error
        }
    }

    public function Register(Request $request)
    {
       
        try {
            // Validasi input dari request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed', // Pastikan password di-confirmasi
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 422);
            }
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), 
                
            ]);
    
            return response()->json([
                'status_code' => 200,
                'message' => 'User Register in successfully!',
            ]);

        } catch (Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(), // Menyertakan stack trace
                'input_data' => $request->all(), // Menyertakan data yang dikirimkan dalam request
            ]);
            // Menangkap kesalahan dan mengembalikan response dengan error 500
            return response()->json([
                'error' => 'Something went wrong during registration.',
                'message' => $e->getMessage() // Menampilkan pesan kesalahan dari exception
            ], 500); // 500 untuk internal server error
        }
    }
}
