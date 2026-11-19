<?php

namespace App\Models\Doc\SuratPermintaanIzinPenyitaanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPermintaanIzinPenyitaanDocumentSeizedItem extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_permintaan_izin_penyitaan_document_seized_items';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'id' => 'integer',
        'jumlah' => 'decimal:2',
    ];

    public function scopeWithRelated($query)
    {
        return $query->with([
            'suratPermintaanIzinPenyitaanDocument',
        ]);
    }

    public function suratPermintaanIzinPenyitaanDocument()
    {
        return $this->belongsTo(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocument',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        );
    }
}