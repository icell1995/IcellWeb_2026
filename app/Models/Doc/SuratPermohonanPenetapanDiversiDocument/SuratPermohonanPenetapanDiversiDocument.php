<?php

namespace App\Models\Doc\SuratPermohonanPenetapanDiversiDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class SuratPermohonanPenetapanDiversiDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_permohonan_penetapan_diversi_documents';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'payload' => 'json',
        'messages' => 'json',
        'timestamps' => 'json',
        'ip_addresses' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
            if (empty($model->status_id)) {
                $model->status_id = '2';
            }
            if (empty($model->document_category_id)) {
                $model->document_category_id = '0212';
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
            'suspect',
            'suratPermohonanPenetapanDiversiDocumentOfficers',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
            'attachment',
        ]);
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id')->with(['police', 'polres']);
    }

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }

    public function court()
    {
        return $this->belongsTo('App\Models\Lib\Court', 'court_id', 'id');
    }

    public function suratPermohonanPenetapanDiversiDocumentOfficers()
    {
        return $this->hasMany('App\Models\Doc\SuratPermohonanPenetapanDiversiDocument\SuratPermohonanPenetapanDiversiDocumentOfficer', 'surat_permohonan_penetapan_diversi_document_id', 'id');
    }

    public function signatory()
    {
        return $this->hasOne('App\Models\Doc\SuratPermohonanPenetapanDiversiDocument\SuratPermohonanPenetapanDiversiDocumentOfficer', 'surat_permohonan_penetapan_diversi_document_id', 'id')
            ->where('class', 'SIGNATORY');
    }

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'surat_perintah_penyidikan_document_id', 'id');
    }

    public function suratKesepakatanDiversiDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratKesepakatanDiversiDocument\SuratKesepakatanDiversiDocument', 'surat_kesepakatan_diversi_document_id', 'id');
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

    public function attachment()
    {
        return $this->hasOne('App\Models\Doc\SuratPermohonanPenetapanDiversiDocument\SuratPermohonanPenetapanDiversiDocumentAttachment', 'surat_permohonan_penetapan_diversi_document_id', 'id');
    }

    public function suratPermohonanPenetapanDiversiDocumentAttachment()
    {
        return $this->hasOne('App\Models\Doc\SuratPermohonanPenetapanDiversiDocument\SuratPermohonanPenetapanDiversiDocumentAttachment', 'surat_permohonan_penetapan_diversi_document_id', 'id');
    }
}
