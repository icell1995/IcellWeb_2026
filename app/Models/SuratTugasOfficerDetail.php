<?php

namespace App\Models;
use Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugasOfficerDetail extends Model
{
    use HasFactory;
    protected $table = 'surat_tugas_officer_detail';

    protected $fillable = [
        'surat_tugas_id',
        'officer_id'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

}
