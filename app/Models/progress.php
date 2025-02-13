<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class progress extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'progress';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'realisasi_anggaran', 'evaluasi_anggaran', 'date'
    ];

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
