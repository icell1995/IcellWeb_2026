<?php

namespace App\Models\Doc\SuratPerintahPenahananDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class SuratPerintahPenahananDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "doc.surat_perintah_penahanan_documents";

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'nomor' => 'string',
        'document_number' => 'string',
        'tanggal' => 'date',
        'document_date' => 'date',
        'nomor_spdp' => 'string',
        'tanggal_spdp' => 'date',
        'kode_satker_penerbit_spdp' => 'string',
        'nomor_surat_perintah_penangkapan' => 'string',
        'kode_jenis_penahanan' => 'integer',
        'kode_satker_tempat_penahanan' => 'string',
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
        'messages' => 'json',
        'ip_addresses' => 'json',
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
                $model->document_category_id = '0601';
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
            'suratPerintahPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument',
            'suspects',
            'attachment',
            'suratPerintahPenahananDocumentOfficers',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }

    // Accessors / Mutators untuk sinkronisasi nama field Gambar 1-2 vs sistem internal
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

    public function getStartDateAttribute()
    {
        return $this->attributes['start_date'] ?? $this->attributes['tanggal_mulai'] ?? null;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value;
        if (empty($this->attributes['tanggal_mulai'])) {
            $this->attributes['tanggal_mulai'] = $value ? date('Y-m-d', strtotime($value)) : null;
        }
    }

    public function getTanggalMulaiAttribute()
    {
        return $this->attributes['tanggal_mulai'] ?? ($this->attributes['start_date'] ? date('Y-m-d', strtotime($this->attributes['start_date'])) : null);
    }

    public function setTanggalMulaiAttribute($value)
    {
        $this->attributes['tanggal_mulai'] = $value;
        $this->attributes['start_date'] = $value;
    }

    public function getEndDateAttribute()
    {
        return $this->attributes['end_date'] ?? $this->attributes['tanggal_akhir'] ?? null;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value;
        if (empty($this->attributes['tanggal_akhir'])) {
            $this->attributes['tanggal_akhir'] = $value ? date('Y-m-d', strtotime($value)) : null;
        }
    }

    public function getTanggalAkhirAttribute()
    {
        return $this->attributes['tanggal_akhir'] ?? ($this->attributes['end_date'] ? date('Y-m-d', strtotime($this->attributes['end_date'])) : null);
    }

    public function setTanggalAkhirAttribute($value)
    {
        $this->attributes['tanggal_akhir'] = $value;
        $this->attributes['end_date'] = $value;
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

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'surat_perintah_penyidikan_document_id', 'id');
    }

    public function suratKetetapanTentangPenetapanTersangkaDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument', 'surat_ketetapan_penetapan_tersangka_id', 'id');
    }

    public function suratKetetapanPenetapanTersangkaDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument', 'surat_ketetapan_penetapan_tersangka_id', 'id');
    }

    public function suspects()
    {
        return $this->belongsToMany(
            'App\Models\Suspect',
            'pivot.surat_perintah_penahanan_document_suspect',
            'surat_perintah_penahanan_document_id',
            'suspect_id'
        )->withTimestamps();
    }

    public function officers()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentOfficer', 'surat_perintah_penahanan_document_id', 'id');
    }

    public function suratPerintahPenahananDocumentOfficers()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentOfficer', 'surat_perintah_penahanan_document_id', 'id');
    }

    public function signatory()
    {
        return $this->hasOne('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentOfficer', 'surat_perintah_penahanan_document_id', 'id')
            ->where('class', 'SIGNATORY');
    }

    public function leaderOfficer()
    {
        return $this->hasOne('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentOfficer', 'surat_perintah_penahanan_document_id', 'id')
            ->where('class', 'LEADER');
    }

    public function memberOfficers()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentOfficer', 'surat_perintah_penahanan_document_id', 'id')
            ->where('class', 'MEMBER')
            ->orderBy('sort');
    }

    public function attachment()
    {
        return $this->hasOne('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentAttachment', 'surat_perintah_penahanan_document_id', 'id');
    }

    public function suratPerintahPenahananDocumentAttachment()
    {
        return $this->hasOne('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentAttachment', 'surat_perintah_penahanan_document_id', 'id');
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
}
