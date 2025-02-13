<?php

namespace App\Models;

use App\Models\Unduhan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUnduhan extends Model
{
    use HasFactory;

    protected $table = 'file_unduhan'; // Nama tabel
    public $incrementing = false; // Karena menggunakan UUID
    protected $keyType = 'string'; // Tipe UUID adalah string

    protected $fillable = [
        'id',
        'unduhan_id',
        'file',
        'title',
        'size',
    ];

    // Relasi Many-to-One ke Unduhan
    public function unduhan()
    {
        return $this->belongsTo(Unduhan::class, 'unduhan_id');
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