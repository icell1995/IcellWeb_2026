<?php

namespace App\Models\Docs\SpdpPusiknasDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UuidTrait;

class SpdpPusiknasDocument extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $table = 'doc.spdp_pusiknas_documents';
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
        return $this->hasMany(SpdpPusiknasDocumentOfficer::class, 'spdp_pusiknas_document_id');
    }

    public function attachments()
    {
        return $this->hasMany(SpdpPusiknasDocumentAttachment::class, 'spdp_pusiknas_document_id');
    }

    public function suspects()
    {
        return $this->belongsToMany(\App\Models\Suspect::class, 'pivot.spdp_pusiknas_document_suspect', 'spdp_pusiknas_document_id', 'suspect_id')
            ->withTimestamps();
    }

    public function reportedPersons()
    {
        return $this->belongsToMany(\App\Models\ReportedPerson::class, 'pivot.spdp_pusiknas_document_reported_person', 'spdp_pusiknas_document_id', 'reported_person_id')
            ->withTimestamps();
    }
}
