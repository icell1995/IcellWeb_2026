<?php

namespace App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\ReturnDocuments;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class SuratKetetapanPenghentianPenyidikanDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_ketetapan_penghentian_penyidikan_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id'           => 'string',
        'kode_alasan'  => 'json',
        'messages'     => 'json',
        'timestamps'   => 'json',
        'ip_addresses' => 'json',
    ];

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
            if ($model->accident) {
                $model->accident->update([
                    'last_update' => Carbon::now(),
                    'category'    => '',
                    'tipe_update' => 'MEMBUAT',
                ]);
            }
        });

        self::updated(function ($model) {
            if ($model->accident) {
                $model->accident->update([
                    'last_update' => Carbon::now(),
                    'category'    => '',
                    'tipe_update' => 'MENGUBAH',
                ]);
            }
        });

        self::deleted(function ($model) {
            if ($model->accident) {
                $model->accident->update([
                    'last_update' => Carbon::now(),
                    'category'    => '',
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
            'documentClassification',
            'suratPerintahPenyidikanDocument',
            'suratPemberitahuanDimulainyaPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument',
            'suratPerintahPenghentianPenyidikanDocument',
            'laporanHasilGelarPerkaraDocument',
            'prosecutor',
            'court',
            'suspects',
            'suratKetetapanPenghentianPenyidikanDocumentAttachment',
            'suratKetetapanPenghentianPenyidikanDocumentOfficers',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }

    public function isEditable(): bool
    {
        return in_array($this->status_id, [1, 2, 4]);
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id')->with(['police']);
    }

    public function documentClassification()
    {
        return $this->belongsTo('App\Models\Lib\DocumentClassification', 'document_classification_id');
    }

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'surat_perintah_penyidikan_document_id');
    }

    public function suratPemberitahuanDimulainyaPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument', 'surat_pemberitahuan_dimulainya_penyidikan_document_id');
    }

    public function suratKetetapanTentangPenetapanTersangkaDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument', 'surat_ketetapan_tentang_penetapan_tersangka_document_id');
    }

    public function suratPerintahPenghentianPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocument', 'surat_perintah_penghentian_penyidikan_document_id');
    }

    public function laporanHasilGelarPerkaraDocument()
    {
        return $this->belongsTo('App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument', 'laporan_hasil_gelar_perkara_document_id');
    }

    public function prosecutor()
    {
        return $this->belongsTo('App\Models\Lib\Prosecutor', 'prosecutor_id');
    }

    public function court()
    {
        return $this->belongsTo('App\Models\Lib\Court', 'court_id');
    }

    public function suratKetetapanPenghentianPenyidikanDocumentAttachment()
    {
        return $this->hasOne(SuratKetetapanPenghentianPenyidikanDocumentAttachment::class, 'surat_ketetapan_penghentian_penyidikan_document_id', 'id');
    }

    public function suratKetetapanPenghentianPenyidikanDocumentOfficers()
    {
        return $this->hasMany(SuratKetetapanPenghentianPenyidikanDocumentOfficer::class, 'surat_ketetapan_penghentian_penyidikan_document_id', 'id');
    }

    public function signatory()
    {
        return $this->hasOne(SuratKetetapanPenghentianPenyidikanDocumentOfficer::class, 'surat_ketetapan_penghentian_penyidikan_document_id', 'id')
            ->where('class', 'SIGNATORY');
    }

    public function suspects()
    {
        return $this->belongsToMany('App\Models\Suspect', 'pivot.surat_ketetapan_penghentian_penyidikan_document_suspect', 'surat_ketetapan_penghentian_penyidikan_document_id', 'suspect_id');
    }

    public function attachment()
    {
        return $this->hasOne(SuratKetetapanPenghentianPenyidikanDocumentAttachment::class, 'surat_ketetapan_penghentian_penyidikan_document_id', 'id');
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

    public function returnDocuments(): MorphMany
    {
        return $this->morphMany(ReturnDocuments::class, 'documentable');
    }
}
