<?php

namespace App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

use App\Models\ReturnDocuments;

class SuratKetetapanPenghentianPenyidikanDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_ketetapan_penghentian_penyidikan_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'document_date' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rj_tanggal_kesepakatan' => 'date',
        'rj_dokumen_pendukung' => 'array',
        'effective_date' => 'date',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'is_legacy' => 'boolean',
        'messages' => 'json',
        'barang_bukti' => 'json',
        'timestamps' => 'json',
        'ip_addresses' => 'json',
    ];

    /**
     * Boot function
     */
    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
            $model->status_id = '2';
            $model->document_category_id = '0206';
        });

        self::created(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => '',
                'tipe_update' => 'MEMBUAT',
            ]);
        });

        self::updated(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => '',
                'tipe_update' => 'MENGUBAH',
            ]);
        });

        self::deleted(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => '',
                'tipe_update' => 'MENGHAPUS',
            ]);
        });
    }

    public function scopeWithRelated($query)
    {
        return $query->with([
            'accident',
            'documentCategory',
            'suratPerintahPenyidikan',
            'suratPerintahPenyidikan.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPemberitahuanDimulainyaPenyidikan',
            'laporanHasilGelarPerkara',
            'officers',
            'attachment',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }

    /**
     * Relations
     */
    
    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id')->with(['police']);
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function suratPerintahPenyidikan()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'surat_perintah_penyidikan_id');
    }

    public function laporanHasilGelarPerkara()
    {
        return $this->belongsTo('App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument', 'laporan_hasil_gelar_perkara_id');
    }

    public function suratPemberitahuanDimulainyaPenyidikan()
    {
        return $this->belongsTo('App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument', 'surat_pemberitahuan_dimulainya_penyidikan_id');
    }

    public function officers()
    {
        return $this->hasMany('App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocumentOfficer', 'surat_ketetapan_penghentian_penyidikan_document_id', 'id');
    }

    public function attachment()
    {
        return $this->hasOne('App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocumentAttachment', 'surat_ketetapan_penghentian_penyidikan_document_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocumentAttachment', 'surat_ketetapan_penghentian_penyidikan_document_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo('App\Models\User', 'updated_by_user_id', 'id');
    }

    public function deletedByUser()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by_user_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Opt\Status', 'status_id', 'id');
    }

    public function prosecutor()
    {
        return $this->belongsTo('App\Models\Lib\Prosecutor', 'prosecutor_id', 'id');
    }

    public function court()
    {
        return $this->belongsTo('App\Models\Lib\Court', 'court_id', 'id');
    }

    public function suspect()
    {
        return $this->belongsToMany('App\Models\Suspect', 'pivot.surat_ketetapan_penghentian_penyidikan_document_suspect', 'surat_ketetapan_penghentian_penyidikan_document_id', 'suspect_id');
    }

    /**
     * Get signatory officer
     */
    public function getSignatoryAttribute()
    {
        return $this->officers()->where('class', 'SIGNATORY')->first();
    }

    /**
     * Get formatted nomor surat
     */
    public function getFormattedNomorSuratAttribute()
    {
        return $this->document_number ?: '-';
    }

    /**
     * Check if document is editable
     * Status: 1 = DRAFT, 2 = DIBUAT, 4 = REVISI
     */
    public function isEditable()
    {
        return in_array($this->status_id, ['1', '2', '4']) || empty($this->status_id);
    }

    /**
     * Check if document can be submitted
     * Status: 2 = DIBUAT
     */
    public function canBeSubmitted()
    {
        return $this->status_id === '2';
    }

    /**
     * Check if document can be approved
     * Status: 3 = MENUNGGU PERSETUJUAN
     */
    public function canBeApproved()
    {
        return $this->status_id === '3';
    }

    /**
     * Scope untuk filter berdasarkan accident
     */
    public function scopeByAccident($query, $accidentId)
    {
        return $query->where('accident_id', $accidentId);
    }

    public function returnDocuments(): MorphMany
    {
        return $this->morphMany(ReturnDocuments::class, 'documentable');
    }
}
