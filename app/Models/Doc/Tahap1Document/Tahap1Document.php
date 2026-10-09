<?php

namespace App\Models\Doc\Tahap1Document;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class Tahap1Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.tahap_1_documents';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'accident_id',
        'document_number',
        'document_date',
        'no_berkas_perkara',
        'no_spdp',
        'document_classification_id',
        'prosecutor_id',
        'messages',
        'suspect_ids',
        'carbon_copies',
        'appendix',
        'is_active',
        'is_legacy',
        'status_id',
        'document_category_id',
        'timestamps_log',
        'ip_addresses',
        'released_at',
        'approved_at',
        'rejected_at',
        'last_synced_at',
        'created_by_user_id',
        'updated_by_user_id',
        'deleted_by_user_id',
        'klasifikasi',
        'lampiran',
        'surat_perintah_penyidikan_id',
        'surat_pemberitahuan_dimulainya_penyidikan_id',
        'surat_ketetapan_penetapan_tersangka_id',
        'berkas_perkara_number',
        'berkas_perkara_date',
        'berkas_perkara_rangkap',
        'pasal_disangkakan',
        'penahanan_rutan',
        'penahanan_cabang',
        'penahanan_start_date',
        'penahanan_end_date',
        'surat_perintah_penahanan_number',
        'surat_perintah_penahanan_date',
        'surat_perpanjangan_penahanan_number',
        'surat_perpanjangan_penahanan_date',
        'surat_perpanjangan_penahanan_court_number',
        'surat_perpanjangan_penahanan_court_date',
        'penahanan_status',
        'surat_penangguhan_penahanan_number',
        'surat_penangguhan_penahanan_date',
        'barang_bukti_storage',
        'barang_bukti',
        'jumlah_bb',
        'investigator_pangkat_nama',
        'investigator_hp',
        'tembusan',
    ];

    protected $casts = [
        'messages'       => 'array',
        'suspect_ids'    => 'array',
        'carbon_copies'  => 'array',
        'timestamps_log' => 'array',
        'ip_addresses'   => 'array',
        'tembusan'       => 'array',
        'barang_bukti'   => 'array',
        'is_active'      => 'boolean',
        'is_legacy'      => 'boolean',
        'document_date'  => 'date',
        'tanggal_terima_p21' => 'date',
        'berkas_perkara_date' => 'date',
        'penahanan_start_date' => 'date',
        'penahanan_end_date' => 'date',
        'surat_perintah_penahanan_date' => 'date',
        'surat_perpanjangan_penahanan_date' => 'date',
        'surat_perpanjangan_penahanan_court_date' => 'date',
        'surat_penangguhan_penahanan_date' => 'date',
        'released_at'    => 'datetime',
        'approved_at'    => 'datetime',
        'rejected_at'    => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
            $model->status_id = '2';
            $model->document_category_id = '0806'; // BPT1
        });

        self::created(function ($model) {
            $accident = $model->accident;
            if ($accident) {
                $accident->update([
                    'last_update' => Carbon::now(),
                    'category'    => '',
                    'tipe_update' => 'MEMBUAT',
                ]);
            }
        });

        self::updated(function ($model) {
            $accident = $model->accident;
            if ($accident) {
                $accident->update([
                    'last_update' => Carbon::now(),
                    'category'    => '',
                    'tipe_update' => 'MENGUBAH',
                ]);
            }
        });

        self::deleted(function ($model) {
            $accident = $model->accident;
            if ($accident) {
                $accident->update([
                    'last_update' => Carbon::now(),
                    'category'    => '',
                    'tipe_update' => 'MENGHAPUS',
                ]);
            }
        });
    }

    public function accident()
    {
        return $this->belongsTo(\App\Models\Accident::class, 'accident_id');
    }

    public function prosecutor()
    {
        return $this->belongsTo(\App\Models\Lib\Prosecutor::class, 'prosecutor_id');
    }

    public function documentClassification()
    {
        return $this->belongsTo(\App\Models\Lib\DocumentClassification::class, 'document_classification_id');
    }

    public function getBaseRouteAttribute(): string
    {
        return 'doc.tahap-1-document';
    }

    public function documentCategory()
    {
        return $this->belongsTo(\App\Models\Lib\DocumentCategory::class, 'document_category_id');
    }

    public function getDocumentCategoryAttribute()
    {
        $cat = $this->getRelationValue('documentCategory');
        if ($cat) {
            $cat = clone $cat;
            $cat->base_route = 'doc.tahap-1-document';
            $cat->route = 'doc.tahap-1-document.create';
            $cat->model_class = self::class;
        }
        return $cat;
    }

    public function createdByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id', 'id');
    }

    public function isEditable()
    {
        return in_array($this->status_id, [1, 2, 4]);
    }

    public function officers()
    {
        return $this->hasMany(Tahap1DocumentOfficer::class, 'tahap_1_document_id');
    }

    public function attachments()
    {
        return $this->hasMany(Tahap1DocumentAttachment::class, 'tahap_1_document_id');
    }

    public function attachment()
    {
        return $this->hasOne(Tahap1DocumentAttachment::class, 'tahap_1_document_id');
    }

    public function suspects()
    {
        return $this->belongsToMany(
            \App\Models\Suspect::class,
            'pivot.tahap_1_document_suspect',
            'tahap_1_document_id',
            'suspect_id'
        )->withTimestamps();
    }

    public function suratPerintahPenyidikan()
    {
        return $this->belongsTo(\App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::class, 'surat_perintah_penyidikan_id');
    }

    public function suratPemberitahuanDimulainyaPenyidikan()
    {
        return $this->belongsTo(\App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::class, 'surat_pemberitahuan_dimulainya_penyidikan_id');
    }

    public function suratKetetapanTentangPenetapanTersangka()
    {
        return $this->belongsTo(\App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument::class, 'surat_ketetapan_penetapan_tersangka_id');
    }
}
