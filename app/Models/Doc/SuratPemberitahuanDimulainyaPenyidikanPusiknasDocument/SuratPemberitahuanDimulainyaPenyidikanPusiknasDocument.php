<?php

namespace App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument extends Model
{
    use HasFactory, SoftDeletes;

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
            $model->status_id    = '2';
            $model->document_category_id = '0204'; // SPDP Pusiknas
        });
    }

    /**
     * Dokumen dapat diedit jika statusnya Draft (1), Dibuat (2), atau Dikembalikan (4).
     */
    public function isEditable(): bool
    {
        return in_array($this->status_id, [1, 2, 4]);
    }

    protected $table = 'doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'accident_id',
        'surat_perintah_penyidikan_document_id',
        'surat_perintah_tugas_document_id',
        'document_number',
        'document_date',
        'document_classification_id',
        'prosecutor_id',
        'court_id',
        'is_suspect_exists',
        'description',
        'messages',
        'appendix',
        'carbon_copies',
        'status_id',
        'document_category_id',
        'is_active',
        'is_legacy',
        'timestamps_log',
        'ip_addresses',
        'released_at',
        'approved_at',
        'rejected_at',
        'last_synced_at',
        'created_by_user_id',
        'updated_by_user_id',
        'deleted_by_user_id',
    ];

    protected $casts = [
        'messages' => 'array',
        'carbon_copies' => 'array',
        'timestamps_log' => 'array',
        'ip_addresses' => 'array',
        'is_suspect_exists' => 'boolean',
        'is_active' => 'boolean',
        'is_legacy' => 'boolean',
        'document_date' => 'date',
        'released_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function accident()
    {
        return $this->belongsTo(\App\Models\Accident::class, 'accident_id');
    }

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo(\App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::class, 'surat_perintah_penyidikan_document_id');
    }

    public function suratPerintahTugasDocument()
    {
        return $this->belongsTo(\App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument::class, 'surat_perintah_tugas_document_id');
    }

    public function prosecutor()
    {
        return $this->belongsTo(\App\Models\Lib\Prosecutor::class, 'prosecutor_id');
    }

    public function court()
    {
        return $this->belongsTo(\App\Models\Lib\Court::class, 'court_id');
    }

    public function documentClassification()
    {
        return $this->belongsTo(\App\Models\Lib\DocumentClassification::class, 'document_classification_id');
    }

    public function officers()
    {
        return $this->hasMany(SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer::class, 'surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id');
    }

    public function suratPemberitahuanDimulainyaPenyidikanDocumentOfficers()
    {
        return $this->officers();
    }

    public function suratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficers()
    {
        return $this->officers();
    }

    public function attachments()
    {
        return $this->hasMany(SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentAttachment::class, 'surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id');
    }

    public function suspects()
    {
        return $this->belongsToMany(\App\Models\Suspect::class, 'pivot.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_suspect', 'surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id', 'suspect_id')
            ->withTimestamps();
    }

    public function reportedPersons()
    {
        return $this->belongsToMany(\App\Models\ReportedPerson::class, 'pivot.spdp_pusiknas_document_reported_person', 'spdp_pusiknas_document_id', 'reported_person_id')
            ->withTimestamps();
    }

    public function getBaseRouteAttribute(): string
    {
        return 'doc.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document';
    }

    public function documentCategory()
    {
        return $this->belongsTo(\App\Models\Lib\DocumentCategory::class, 'document_category_id', 'id');
    }

    public function getDocumentCategoryAttribute()
    {
        $cat = $this->getRelationValue('documentCategory');
        if ($cat) {
            $cat = clone $cat;
            $cat->base_route = 'doc.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document';
            $cat->route = 'doc.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document.create';
            $cat->model_class = self::class;
        }
        return $cat;
    }

    public function attachment()
    {
        return $this->hasOne(SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentAttachment::class, 'surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\Opt\Status::class, 'status_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by_user_id');
    }

    public function deletedByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by_user_id');
    }

    public function scopeWithRelated($query)
    {
        return $query->with([
            'accident',
            'documentCategory',
            'prosecutor',
            'court',
            'documentClassification',
            'suratPerintahPenyidikanDocument',
            'suratPerintahTugasDocument',
            'suspects',
            'attachments',
            'officers',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }
}
