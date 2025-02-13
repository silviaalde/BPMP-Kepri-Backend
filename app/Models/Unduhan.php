<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unduhan extends Model
{
    use HasFactory;

    protected $table = 'unduhan'; // Nama tabel
    public $incrementing = false; // Karena menggunakan UUID
    protected $keyType = 'string'; // Tipe UUID adalah string

    protected $fillable = [
        'id',
        'title',
        'content',
        'category',
        'date',
    ];

    // Relasi One-to-Many ke FileUnduhan
    public function files()
    {
        return $this->hasMany(FileUnduhan::class, 'unduhan_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
