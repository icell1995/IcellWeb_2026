<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class uploadImage extends Model
{
    use HasFactory;
    
    public $incrementing = false;

    protected $table = 'upload_image';

    protected $fillable =[
        'accident_id',
        'name',
        'image',
        'category'
    ]; 
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
