<?php

namespace App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SuratLaporanPersetujuanPenyitaanDocumentSeizedItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_laporan_persetujuan_penyitaan_document_seized_items';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'surat_laporan_persetujuan_penyitaan_document_person_id',
        'name',
        'quantity',
        'unit',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function person()
    {
        return $this->belongsTo(SuratLaporanPersetujuanPenyitaanDocumentPerson::class, 'surat_laporan_persetujuan_penyitaan_document_person_id');
    }
}