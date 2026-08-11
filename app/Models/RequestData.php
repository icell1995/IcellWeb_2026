<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestData extends Model
{
    use HasFactory;

    protected $table = 'request_data';

    protected $fillable = [
        'catatan_permintaan',
        'nama_lengkap_pemohon',
        'no_telp_pemohon',
        'jenis_institusi',
        'polda_id',
        'polres_id',
        'instansi_lain',
        'evidence_path',
        'evidence_name',
        'tanggal_permintaan',
        'tanggal_penyajian',
        'penyedia_data_id',
        'file_data_path',
        'file_data_name',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
        'tanggal_penyajian'  => 'date',
        'status'             => 'boolean',
    ];

    /* ========================
     * Relationships
     * ======================== */

    public function polda()
    {
        return $this->belongsTo(Polda::class, 'polda_id', 'id');
    }

    public function polres()
    {
        return $this->belongsTo(Polres::class, 'polres_id', 'id');
    }

    public function penyediaData()
    {
        return $this->belongsTo(User::class, 'penyedia_data_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /* ========================
     * Helpers
     * ======================== */

    /**
     * Label instansi yang ditampilkan di tabel.
     */
    public function getInstansiLabelAttribute(): string
    {
        return match ($this->jenis_institusi) {
            'korlantas' => 'KORLANTAS',
            'polda'     => optional($this->polda)->name ?? 'POLDA',
            'polres'    => optional($this->polres)->name ?? 'POLRES',
            'lainnya'   => $this->instansi_lain ?? '-',
            default     => '-',
        };
    }
}
