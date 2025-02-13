<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kegiatan extends Model
{
    use HasFactory;

    // Tentukan kolom yang dapat diisi (fillable)
    protected $fillable = ['title', 'description', 'date', 'location', 'department'];
    protected $table = 'kegiatan'; 

    // Menentukan bahwa kolom id menggunakan UUID
    public $incrementing = false; // Nonaktifkan auto increment
    protected $keyType = 'string'; // Tipe primary key adalah string

    // Mengatur UUID secara otomatis
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid(); // Generate UUID saat membuat model
        });
    }

    public function imageKegiatan()
    {
        return $this->hasMany(ImageKegiatan::class, 'kegiatan_id', 'id');
    }
}
