<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class UploadSuratKetetapan extends Model
{
    use HasFactory;

    // Jika PostgreSQL dan schema = public:
    // protected $table = 'public.upload_surat_ketetapan';
    protected $table = 'upload_surat_ketetapan';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Set true/false sesuai tabel Anda
    public $timestamps = false;

    protected $fillable = [
        'id',
        'accident_id',
        'name',
        'category',
        'initial',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Pilih salah satu generator UUID sesuai yang dipakai project Anda:

            // 1) Jika gunakan package Uuid (seperti di kode Anda):
            $model->id = $model->id ?: (string) Uuid::generate();

            // 2) Atau gunakan helper bawaan Laravel (disarankan kalau tidak pakai package):
            // $model->id = $model->id ?: (string) Str::uuid();
        });
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id', 'id');
    }
}