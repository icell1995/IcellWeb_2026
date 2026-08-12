<?php

namespace App\Models\Doc\SuratPemberitahuanPerkembanganHasilPenyidikanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPemberitahuanPerkembanganHasilPenyidikanDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'accident_id',
        'document_category_id',
        'reporting_person_id',
        'nomor_lp',
        'tanggal_lp',
        'nomor_surat',
        'tanggal_surat',
        'tempat_surat',
        'tipe_sp2hp',
        'tingkat_kasus',
        // penerima_ fields removed (now in reporting_persons table)
        'pelapor_nama',
        'pelapor_alamat',
        'pelapor_jabatan',
        'pelapor_nrp',
        'pelapor_unit',
        'pelapor_telepon',
        'pelapor_email',
        // tersangka_ fields removed (retrieved from suspects)
        // korban_ fields removed (retrieved from victims)
        'kendaraan_data', // JSON field for kendaraan
        // uraian_peristiwa removed (retrieved from accident)
        // penyidik fields removed (now in sp2hp_officers table)
        'type_specific_data', // JSON field for type-specific data
        'a4_tindakan_list', // JSON field for A4 tindakan yang telah dilakukan
        'pasal_diduga',
        'barang_bukti',
        'catatan',
        'status',
        'created_by',
        'updated_by',
        'submitted_at',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_lp' => 'date',
        'kendaraan_data' => 'array',
        'type_specific_data' => 'array',
        'a4_tindakan_list' => 'array',
        'submitted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
            $model->document_category_id = '0709';
        });
    }

    /**
     * Relations
     */
    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id');
    }

    public function reportingPerson()
    {
        return $this->belongsTo('App\Models\ReportingPerson', 'reporting_person_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    /**
     * Relasi ke Officers - one to many
     */
    public function officers()
    {
        return $this->hasMany(SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::class, 'sp2hp_document_id')->ordered();
    }

    /**
     * Scopes
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeByAccident($query, $accidentId)
    {
        return $query->where('accident_id', $accidentId);
    }
}
