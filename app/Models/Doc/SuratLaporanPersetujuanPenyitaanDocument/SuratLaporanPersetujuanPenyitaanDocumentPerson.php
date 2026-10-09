<?php

namespace App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SuratLaporanPersetujuanPenyitaanDocumentPerson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_laporan_persetujuan_penyitaan_document_persons';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'surat_laporan_persetujuan_penyitaan_document_id',
        'suspect_id',
        'witness_id',
        'reported_person_id',
        'bap_date',
        'bap_file',
        'is_seized_at_work_unit',
        'seized_location',
    ];

    protected $casts = [
        'bap_date' => 'date',
        'is_seized_at_work_unit' => 'boolean',
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

    public function document()
    {
        return $this->belongsTo(SuratLaporanPersetujuanPenyitaanDocument::class, 'surat_laporan_persetujuan_penyitaan_document_id');
    }

    public function suspect()
    {
        return $this->belongsTo(\App\Models\Suspect::class, 'suspect_id');
    }

    public function witness()
    {
        return $this->belongsTo(\App\Models\Witness::class, 'witness_id');
    }

    public function reportedPerson()
    {
        return $this->belongsTo(\App\Models\ReportedPerson::class, 'reported_person_id');
    }

    public function seizedItems()
    {
        return $this->hasMany(SuratLaporanPersetujuanPenyitaanDocumentSeizedItem::class, 'surat_laporan_persetujuan_penyitaan_document_person_id');
    }
}
