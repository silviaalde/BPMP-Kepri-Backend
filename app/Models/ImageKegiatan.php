<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageKegiatan extends Model
{
    use HasFactory;


    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'image_kegiatan';

    protected $fillable = [
        'id',
        'name',
        'image',
        'kegiatan_id',
    ];

    // Relasi dengan Gallery
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
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
