<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SuratPermintaanIzinPenyitaanDocumentWitness extends Pivot
{
    use HasFactory;

    protected $table = 'pivot.surat_permintaan_izin_penyitaan_document_witnesses';

    protected $primaryKey = 'id';

    protected $fillable = [
        'surat_permintaan_izin_penyitaan_document_id',
        'witness_id',
    ];

    public function suratPermintaanIzinPenyitaanDocument()
    {
        return $this->belongsTo(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocument',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        );
    }

    public function witness()
    {
        return $this->belongsTo(
            'App\Models\Witness',
            'witness_id',
            'id'
        );
    }
}
