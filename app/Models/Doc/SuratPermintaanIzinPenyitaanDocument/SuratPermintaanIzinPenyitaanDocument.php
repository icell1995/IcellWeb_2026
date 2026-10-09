<?php

namespace App\Models\Doc\SuratPermintaanIzinPenyitaanDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SuratPermintaanIzinPenyitaanDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_permintaan_izin_penyitaan_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'carbon_copies' => 'json',
        'document_date' => 'date',
        'sprindik_date' => 'date',
        'surat_perintah_penyitaan_date' => 'date',
        'spdp_date' => 'date',
        'is_active' => 'boolean',
        'messages' => 'json',
        'timestamps_log' => 'json',
        'ip_addresses' => 'json',
        'released_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

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
            'suratPerintahPenyidikanDocument',
            'court',
            'documentClassification',
            'documentCategory',
            'officers',
            'laws.crimeConstitution',
            'seizedItems',
            'attachments',
            'documentSuspects.suspect',
            'createdByUser',
            'updatedByUser',
        ]);
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id')->with(['police']);
    }

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo(
            'App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument',
            'surat_perintah_penyidikan_document_id',
            'id'
        );
    }

    public function court()
    {
        return $this->belongsTo('App\Models\Lib\Court', 'court_id', 'id');
    }

    public function documentClassification()
    {
        return $this->belongsTo('App\Models\Lib\DocumentClassification', 'document_classification_id', 'id');
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
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

    public function officers()
    {
        return $this->hasMany(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocumentOfficer',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        )->orderBy('sort');
    }

    public function leaderOfficer()
    {
        return $this->hasOne(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocumentOfficer',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        )->where('class', 'LEADER');
    }

    public function signatories()
    {
        return $this->hasMany(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocumentOfficer',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        )->where('class', 'SIGNATORY')->orderBy('sort');
    }



    public function seizedItems()
    {
        return $this->hasMany(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocumentSeizedItem',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        );
    }

    public function attachments()
    {
        return $this->hasMany(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocumentAttachment',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        );
    }

    public function attachment()
    {
        return $this->hasOne(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocumentAttachment',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        );
    }

    public function documentSuspects()
    {
        return $this->hasMany(
            'App\Models\Pivot\SuratPermintaanIzinPenyitaanDocumentSuspect',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        );
    }

    public function suspects()
    {
        return $this->belongsToMany(
            'App\Models\Suspect',
            'pivot.surat_permintaan_izin_penyitaan_document_suspect',
            'surat_permintaan_izin_penyitaan_document_id',
            'suspect_id'
        );
    }

    public function reportedPersons()
    {
        return $this->belongsToMany(
            'App\Models\ReportedPerson',
            'pivot.surat_permintaan_izin_penyitaan_doc_reported_person',
            'surat_permintaan_izin_penyitaan_document_id',
            'reported_person_id'
        )->withRelated();
    }
}
