<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FileUnduhanController;
use App\Http\Controllers\ImageKegiatanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PenghargaanController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UnduhanController;
use App\Models\ImageKegiatan;
use Illuminate\Support\Facades\Route;



Route::post('/login', [AuthController::class, 'Login']);
Route::post('/register', [AuthController::class, 'Register']);

Route::get('/blog', [ContentController::class, 'GetContents']);
Route::get('/blog-count', [ContentController::class, 'getContentCount']);
Route::post('/kegiatan-image', [ImageKegiatanController::class, 'CreateImageKegiatan']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/blog', [ContentController::class, 'CreateContent']);
    Route::post('/blog-update/{id}', [ContentController::class, 'UpdateContent']);
    Route::delete('/blog/{id}', [ContentController::class, 'DeleteContent']);
    
    Route::post('/kegiatan', [KegiatanController::class, 'CreateKegiatan']);
    Route::post('/kegiatan-update/{id}', [KegiatanController::class, 'UpdateKegiatan']);
    Route::delete('/kegiatan/{id}', [KegiatanController::class, 'DeleteKegiatan']);
    
    Route::delete('/kegiatan-image/{id}', [ImageKegiatanController::class, 'DeleteImageKegiatan']);
    
    Route::post('/penghargaan', [PenghargaanController::class, 'CreatePenghargaan']);
    Route::post('/penghargaan-update/{id}', [PenghargaanController::class, 'UpdatePenghargaan']);
    Route::delete('/penghargaan/{id}', [PenghargaanController::class, 'DeletePenghargaan']);


    Route::post('/unduhan', [UnduhanController::class, 'CreateUnduhan']);
    Route::post('/unduhan-update/{id}', [UnduhanController::class, 'UpdateUnduhan']);
    Route::delete('/unduhan/{id}', [UnduhanController::class, 'DeleteUnduhan']);
    
    Route::post('/file-unduhan', [FileUnduhanController::class, 'CreateFileUnduhan']);
    Route::delete('/file-unduhan/{id}', [FileUnduhanController::class, 'DeleteFileUnduhan']);
    
    Route::post('/faq', [FaqController::class, 'CreateFaq']);
    Route::post('/faq-update/{id}', [FaqController::class, 'UpdateFaq']);
    Route::delete('/faq/{id}', [FaqController::class, 'DeleteFaq']);
    
    Route::post('/progress', [ProgressController::class, 'CreateProgress']);
    Route::post('/progress-update/{id}', [ProgressController::class, 'UpdateProgress']);
    Route::delete('/progress/{id}', [ProgressController::class, 'DeleteProgress']);
});

Route::get('/unduhan', [UnduhanController::class, 'GetAll']);

Route::get('/kegiatan', [KegiatanController::class, 'GetAll']);

Route::get('/penghargaan', [PenghargaanController::class, 'GetPenghargaan']);

Route::get('/faq', [FaqController::class, 'GetFaq']);
Route::get('/data-stats', [StatsController::class, 'GetStats']);

Route::get('/progress', [ProgressController::class, 'GetProgress']);