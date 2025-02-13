<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'faq';
    public $incrementing = false;  // Menonaktifkan auto increment karena menggunakan UUID
    protected $keyType = 'string';  // Menentukan tipe primary key sebagai string (UUID)

    // Menentukan atribut yang boleh diisi massal
    protected $fillable = [
        'id',  'title', 'content', 'category'
    ];

    // Boot method untuk otomatis generate UUID saat membuat data baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();  // Generate UUID
            }
        });
    }
}
