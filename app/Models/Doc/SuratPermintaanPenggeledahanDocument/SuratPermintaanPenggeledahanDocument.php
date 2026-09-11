<?php

namespace App\Models\Doc\SuratPermintaanPenggeledahanDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class SuratPermintaanPenggeledahanDocument extends Model
{
     use HasFactory, SoftDeletes;

    protected $table='doc.surat_permintaan_penggeledahan_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

     // cast
    protected $casts = [
        'id' => 'string',
        'daftar_penggeledahan' => 'json',
        'related_property' => 'json',
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
            $model->status_id = '2';
            $model->document_category_id = '0404';
        });

        self::created(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => null,
                'tipe_update' => null,
            ]);
        });

        self::updated(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => null,
                'tipe_update' => null,
            ]);
        });

        self::deleted(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => null,
                'tipe_update' => null,
            ]);
        });
    }

    public function scopeWithRelated($query){
        return $query->with([
            'accident',
            'documentCategory',
            'suratPermintaanPenggeledahanDocumentAttachment',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status'
        ]);
    }

    public function documentCategory(){
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id')->with(['police']);
    }

    public function suratPemberitahuanDimulainyaPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument', 'surat_pemberitahuan_dimulainya_penyidikan_document_id', 'id');
    }

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'no_sprindik', 'id');
    }

    public function officers()
    {
        return $this->hasMany('App\Models\Doc\SuratPermintaanPenggeledahanDocument\SuratPermintaanPenggeledahanDocumentOfficer', 'surat_permintaan_penggeledahan_document_id', 'id');
    }

    public function suratPerintahTugasDocumentAttachment()
    {
        return $this->hasOne('App\Models\Doc\SuratPermintaanPenggeledahanDocument\SuratPermintaanPenggeledahanDocumentAttachment', 'surat_permintaan_penggeledahan_document_id', 'id');
    }
  
    public function attachment()
    {
        return $this->hasOne('App\Models\Doc\SuratPermintaanPenggeledahanDocument\SuratPermintaanPenggeledahanDocumentAttachment', 'surat_permintaan_penggeledahan_document_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id', 'id');
    }

    public function authorizedSignatory()
    {
        return $this->belongsTo('App\Models\Officer', 'signatory_id', 'id');
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

    public function suspects()
    {
        return $this->belongsToMany(\App\Models\Suspect::class, 'pivot.surat_permintaan_penggeledahan_document_suspect', 'surat_permintaan_penggeledahan_document_id', 'suspect_id')
            ->withTimestamps();
    }

    public function reportedPersons()
    {
        return $this->belongsToMany(\App\Models\ReportedPerson::class, 'pivot.surat_permintaan_penggeledahan_document_reported_person', 'surat_permintaan_penggeledahan_document_id', 'reported_person_id')
            ->withTimestamps();
    }

    public function returnDocuments(): MorphMany
    {
        return $this->morphMany(ReturnDocuments::class, 'documentable');
    }
}
