<?php

namespace App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class SuratPermohonanPerpanjanganPenahananKejaksaanDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_documents";

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'nomor' => 'string',
        'document_number' => 'string',
        'tanggal' => 'date',
        'document_date' => 'date',
        'klasifikasi' => 'string',
        'lampiran' => 'string',
        'tempat_surat' => 'string',
        'nama_kejaksaan' => 'string',
        'lokasi_kejaksaan' => 'string',
        'nomor_spdp' => 'string',
        'tanggal_spdp' => 'date',
        'kode_satker_penerbit_spdp' => 'string',
        'nomor_sprindik' => 'string',
        'tanggal_sprindik' => 'date',
        'nomor_penetapan_tersangka' => 'string',
        'tanggal_penetapan_tersangka' => 'date',
        'nomor_surat_perintah_penahanan' => 'string',
        'tanggal_surat_perintah_penahanan' => 'date',
        'satker_penyidik' => 'string',
        'dugaan_tindak_pidana' => 'string',
        'pasal_diduga' => 'string',
        'tempat_kejadian' => 'string',
        'kurun_waktu' => 'string',
        'tanggal_akhir_penahanan_lama' => 'date',
        'nama_rutan' => 'string',
        'jumlah_hari' => 'integer',
        'tanggal_mulai_perpanjangan' => 'date',
        'tanggal_akhir_perpanjangan' => 'date',
        'contact_officer_id' => 'string',
        'contact_officer_name' => 'string',
        'contact_officer_phone' => 'string',
        'carbon_copies' => 'json',
        'signatory_id' => 'string',
        'signatory_head_text' => 'string',
        'signatory_position' => 'string',
        'signatory_name' => 'string',
        'signatory_rank' => 'string',
        'signatory_nrp' => 'string',
        'messages' => 'json',
        'ip_addresses' => 'json',
        'payload' => 'json',
        'is_active' => 'boolean',
        'is_legacy' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
            if (empty($model->status_id)) {
                $model->status_id = '2';
            }
            if (empty($model->document_category_id)) {
                $model->document_category_id = '0605';
            }
        });

        self::created(function ($model) {
            if ($model->accident) {
                $model->accident->update([
                    'last_update' => Carbon::now(),
                    'category' => '',
                    'tipe_update' => 'MEMBUAT',
                ]);
            }
        });

        self::updated(function ($model) {
            if ($model->accident) {
                $model->accident->update([
                    'last_update' => Carbon::now(),
                    'category' => '',
                    'tipe_update' => 'MENGUBAH',
                ]);
            }
        });

        self::deleted(function ($model) {
            if ($model->accident) {
                $model->accident->update([
                    'last_update' => Carbon::now(),
                    'category' => '',
                    'tipe_update' => 'MENGHAPUS',
                ]);
            }
        });
    }

    public function scopeWithRelated($query)
    {
        return $query->with([
            'accident',
            'documentCategory',
            'prosecutor',
            'suratPerintahPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument',
            'suratPerintahPenahananDocument',
            'suspects',
            'attachment',
            'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }

    // Accessors / Mutators untuk sinkronisasi nama field identitas vs internal
    public function getDocumentNumberAttribute()
    {
        return $this->attributes['document_number'] ?? $this->attributes['nomor'] ?? null;
    }

    public function setDocumentNumberAttribute($value)
    {
        $this->attributes['document_number'] = $value;
        $this->attributes['nomor'] = $value;
    }

    public function getNomorAttribute()
    {
        return $this->attributes['nomor'] ?? $this->attributes['document_number'] ?? null;
    }

    public function setNomorAttribute($value)
    {
        $this->attributes['nomor'] = $value;
        $this->attributes['document_number'] = $value;
    }

    public function getDocumentDateAttribute()
    {
        return $this->attributes['document_date'] ?? $this->attributes['tanggal'] ?? null;
    }

    public function setDocumentDateAttribute($value)
    {
        $this->attributes['document_date'] = $value;
        $this->attributes['tanggal'] = $value;
    }

    public function getTanggalAttribute()
    {
        return $this->attributes['tanggal'] ?? $this->attributes['document_date'] ?? null;
    }

    public function setTanggalAttribute($value)
    {
        $this->attributes['tanggal'] = $value;
        $this->attributes['document_date'] = $value;
    }

    // Relationships
    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id')->with(['police', 'polres', 'polres.polda']);
    }

    public function prosecutor()
    {
        return $this->belongsTo('App\Models\Lib\Prosecutor', 'prosecutor_id', 'id');
    }

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'surat_perintah_penyidikan_document_id', 'id');
    }

    public function suratKetetapanTentangPenetapanTersangkaDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument', 'surat_ketetapan_penetapan_tersangka_id', 'id');
    }

    public function suratPerintahPenahananDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument', 'surat_perintah_penahanan_document_id', 'id');
    }

    public function suspects()
    {
        return $this->belongsToMany(
            'App\Models\Suspect',
            'public.pivot_surat_permohonan_perpanjangan_penahanan_kejaksaan_document_suspect',
            'document_id',
            'suspect_id'
        )->using('App\Models\Pivot\SuratPermohonanPerpanjanganPenahananKejaksaanDocumentSuspect')
         ->withPivot('id')
         ->withTimestamps();
    }

    public function officers()
    {
        return $this->hasMany('App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer', 'doc_id', 'id');
    }

    public function suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers()
    {
        return $this->hasMany('App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer', 'doc_id', 'id');
    }

    public function signatory()
    {
        return $this->hasOne('App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer', 'doc_id', 'id')
            ->where('class', 'SIGNATORY');
    }

    public function contactOfficer()
    {
        return $this->hasOne('App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer', 'doc_id', 'id')
            ->where('class', 'CONTACT');
    }

    public function attachment()
    {
        return $this->hasOne('App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocumentAttachment', 'doc_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Opt\Status', 'status_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id', 'id')->with('rank');
    }

    public function updatedByUser()
    {
        return $this->belongsTo('App\Models\User', 'updated_by_user_id', 'id')->with('rank');
    }

    public function deletedByUser()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by_user_id', 'id');
    }

    public function suratPermintaanPerpanjanganPenahananLanjutanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument\SuratPermintaanPerpanjanganPenahananLanjutanDocument', 'surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id', 'id');
    }

    public function suratPermintaanPerpanjanganPenahananLanjutanKeduaDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument', 'surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id', 'id');
    }
}
