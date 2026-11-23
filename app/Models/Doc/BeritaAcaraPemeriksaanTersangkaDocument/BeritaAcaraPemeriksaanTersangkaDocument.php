<?php

namespace App\Models\Doc\BeritaAcaraPemeriksaanTersangkaDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Accident;
use Illuminate\Support\Str;

class BeritaAcaraPemeriksaanTersangkaDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.berita_acara_pemeriksaan_tersangka_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'document_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id');
    }
}
