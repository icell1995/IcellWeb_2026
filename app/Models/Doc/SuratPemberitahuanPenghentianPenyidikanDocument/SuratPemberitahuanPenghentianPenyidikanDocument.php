<?php

namespace App\Models\Doc\SuratPemberitahuanPenghentianPenyidikanDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\ReturnDocuments;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class SuratPemberitahuanPenghentianPenyidikanDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table="doc.surat_pemberitahuan_penghentian_penyidikan_documents";

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

     // cast
    protected $casts = [
        'id' => 'string',
        'carbon_copies' => 'json',
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
            $model->document_category_id = '0216';
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

    public function scopeWithRelated($query){
        return $query->with([
            'accident',
            'documentCategory',
            'prosecutor',
            'court',
            'documentClassification',
            'suratPerintahPenyidikanDocument',
            'suratPerintahTugasDocument',
            'suspects',
            'suratPemberitahuanPenghentianPenyidikanDocumentAttachment',
            'suratPemberitahuanPenghentianPenyidikanDocumentOfficers',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }

    /**
     * Dokumen dapat diedit jika statusnya Draft (1), Dibuat (2), atau Dikembalikan (4).
     */
    public function isEditable(): bool
    {
        return in_array($this->status_id, [1, 2, 4]);
    }

    public function documentCategory(){
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id')->with(['police']);
    }

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'surat_perintah_penyidikan_document_id');
    }

    public function suratPerintahTugasDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument', 'surat_perintah_tugas_document_id');
    }

    public function prosecutor()
    {
        return $this->belongsTo('App\Models\Lib\Prosecutor', 'prosecutor_id');
    }

    public function court()
    {
        return $this->belongsTo('App\Models\Lib\Court', 'court_id');
    }

    public function documentClassification()
    {
        return $this->belongsTo('App\Models\Lib\DocumentClassification', 'document_classification_id');
    }

    public function suratPemberitahuanPenghentianPenyidikanDocumentAttachment()
    {
        return $this->hasOne('App\Models\Doc\SuratPemberitahuanPenghentianPenyidikanDocument\SuratPemberitahuanPenghentianPenyidikanDocumentAttachment', 'surat_pemberitahuan_penghentian_penyidikan_document_id', 'id');
    }

    public function suratPemberitahuanPenghentianPenyidikanDocumentOfficers()
    {
        return $this->hasMany('App\Models\Doc\SuratPemberitahuanPenghentianPenyidikanDocument\SuratPemberitahuanPenghentianPenyidikanDocumentOfficer', 'surat_pemberitahuan_penghentian_penyidikan_document_id', 'id');
    }

    public function suspects(){
        return $this->belongsToMany('App\Models\Suspect', 'pivot.surat_pemberitahuan_penghentian_penyidikan_document_suspect', 'surat_pemberitahuan_penghentian_penyidikan_document_id', 'suspect_id')
            ->withRelated();
    }
   
    public function reportedPersons(){
        return $this->belongsToMany('App\Models\ReportedPerson', 'pivot.surat_pemberitahuan_penghentian_penyidikan_doc_reported_person', 'surat_pemberitahuan_penghentian_penyidikan_document_id', 'reported_person_id')
            ->withRelated();
    }

    public function informants()
    {
        return $this->belongsToMany('App\Models\Informant', 'pivot.surat_pemberitahuan_penghentian_penyidikan_document_informant', 'surat_pemberitahuan_penghentian_penyidikan_document_id', 'informant_id');
    }

    public function attachment()
    {
        return $this->hasOne('App\Models\Doc\SuratPemberitahuanPenghentianPenyidikanDocument\SuratPemberitahuanPenghentianPenyidikanDocumentAttachment', 'surat_pemberitahuan_penghentian_penyidikan_document_id', 'id');
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
