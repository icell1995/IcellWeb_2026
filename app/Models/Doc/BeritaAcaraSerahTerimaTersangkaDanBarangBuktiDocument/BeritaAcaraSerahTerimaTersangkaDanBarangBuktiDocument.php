<?php

namespace App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "doc.berita_acara_serah_terima_tersangka_documents";

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
        'hari' => 'string',
        'jam' => 'string',
        'tempat' => 'string',
        'nomor_spdp' => 'string',
        'tanggal_spdp' => 'date',
        'kode_satker_penerbit_spdp' => 'string',
        'nomor_sprindik' => 'string',
        'tanggal_sprindik' => 'date',
        'nomor_surat_perintah_penahanan' => 'string',
        'tanggal_surat_perintah_penahanan' => 'date',
        'nomor_p21' => 'string',
        'tanggal_p21' => 'date',
        'nama_kejaksaan' => 'string',
        'lokasi_kejaksaan' => 'string',
        'pihak_kedua_nama' => 'string',
        'pihak_kedua_nip' => 'string',
        'pihak_kedua_pangkat' => 'string',
        'pihak_kedua_jabatan' => 'string',
        'pihak_kedua_instansi' => 'string',
        'daftar_barang_bukti' => 'string',
        'catatan' => 'string',
        'signatory_id' => 'string',
        'signatory_head_text' => 'string',
        'signatory_position' => 'string',
        'signatory_name' => 'string',
        'signatory_rank' => 'string',
        'signatory_nrp' => 'string',

        // Template Word Variables
        'daerah_police_full_name' => 'string',
        'resor_police_full_name' => 'string',
        'resor_police_address' => 'string',
        'document_day_name' => 'string',
        'document_day_date' => 'string',
        'document_month' => 'string',
        'document_year' => 'string',
        'document_date_text' => 'string',
        'document_time' => 'string',
        'document_location' => 'string',

        'investigator_name' => 'string',
        'investigator_rank_name' => 'string',
        'investigator_register_number' => 'string',
        'investigator_position_name' => 'string',
        'investigator_role' => 'string',

        'officer1_name' => 'string',
        'officer1_rank_name' => 'string',
        'officer1_register_number' => 'string',
        'officer1_position_name' => 'string',
        'officer2_name' => 'string',
        'officer2_rank_name' => 'string',
        'officer2_register_number' => 'string',
        'officer2_position_name' => 'string',

        'prosecutor_institution_name' => 'string',
        'p21_document_number' => 'string',
        'p21_document_date' => 'string',
        'deliverer_head_title' => 'string',
        'delivery_letter_number' => 'string',
        'delivery_letter_date' => 'string',

        'suspect_name' => 'string',
        'suspect2_line' => 'string',
        'berkas_number' => 'string',
        'berkas_date' => 'string',

        'prosecutor_officer_name' => 'string',
        'prosecutor_officer_rank_nip' => 'string',
        'prosecutor_officer_position_name' => 'string',

        'dugaan_tindak_pidana' => 'string',
        'pasal_diduga' => 'string',
        'uu_nomor' => 'string',
        'uu_tahun' => 'string',
        'uu_tentang' => 'string',
        'tempat_kejadian' => 'string',
        'waktu_kejadian' => 'string',

        'handover_day' => 'string',
        'handover_date' => 'string',
        'handover_place' => 'string',
        'execution_description' => 'string',

        'witness1_name' => 'string',
        'witness1_rank_nip' => 'string',
        'witness1_position_name' => 'string',
        'witness1_address' => 'string',
        'witness2_name' => 'string',
        'witness2_rank_nip' => 'string',
        'witness2_position_name' => 'string',
        'witness2_address' => 'string',

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
                $model->document_category_id = '0602';
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
            'suratPerintahPenahananDocument',
            'suspects',
            'attachment',
            'officers',
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

    public function suratPerintahPenahananDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument', 'surat_perintah_penahanan_document_id', 'id');
    }

    public function suspects()
    {
        return $this->belongsToMany(
            'App\Models\Suspect',
            'public.pivot_ba_serah_terima_tersangka_suspect',
            'document_id',
            'suspect_id'
        )->withPivot('id')->withTimestamps();
    }

    public function officers()
    {
        return $this->hasMany('App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentOfficer', 'doc_id', 'id');
    }

    public function delivererOfficers()
    {
        return $this->hasMany('App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentOfficer', 'doc_id', 'id')
            ->where('class', 'DELIVERER')
            ->orderBy('order_number');
    }

    public function delivererOfficer()
    {
        return $this->hasOne('App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentOfficer', 'doc_id', 'id')
            ->where('class', 'DELIVERER');
    }

    public function signatory()
    {
        return $this->hasOne('App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentOfficer', 'doc_id', 'id')
            ->where('class', 'SIGNATORY');
    }

    public function attachment()
    {
        return $this->hasOne('App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentAttachment', 'doc_id', 'id');
    }

    public function authorizedSignatory()
    {
        return $this->belongsTo('App\Models\Officer', 'signatory_id', 'id');
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
        return $this->belongsTo('App\Models\Ref', 'status_id', 'id');
    }
}
