<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class ImageCarousel extends Model
{
    use HasFactory;

    protected $table = 'image_carousel';
    protected $fillable=
    ['title',
    'name_image',
    'description',
    'url'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
