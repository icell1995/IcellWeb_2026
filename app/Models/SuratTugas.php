<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;


class SuratTugas extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas';
    
    protected $fillable = [
        'accident_id',
        'officer_id'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

    public function surattugasdetail()
    {   
        return $this->hasMany(SuratTugasDetail::class);
        // ->withPivot(['role_id', 'permission_id']);
    }
}
