<?php

namespace App\Models\Docs\Sp3PusiknasDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UuidTrait;

class Sp3PusiknasDocument extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $table = 'doc.sp3_pusiknas_documents';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'accident_id',
        'surat_pemberitahuan_dimulainya_penyidikan_document_id',
        'document_number',
        'document_date',
        'no_spdp',
        'kode_alasan',
        'messages',
        'status_id',
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
        'kode_alasan' => 'array',
        'messages' => 'array',
        'timestamps_log' => 'array',
        'ip_addresses' => 'array',
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

    public function spdpDocument()
    {
        return $this->belongsTo(\App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::class, 'surat_pemberitahuan_dimulainya_penyidikan_document_id');
    }

    public function officers()
    {
        return $this->hasMany(Sp3PusiknasDocumentOfficer::class, 'sp3_pusiknas_document_id');
    }

    public function attachments()
    {
        return $this->hasMany(Sp3PusiknasDocumentAttachment::class, 'sp3_pusiknas_document_id');
    }

    public function suspects()
    {
        return $this->belongsToMany(\App\Models\Suspect::class, 'pivot.sp3_pusiknas_document_suspect', 'sp3_pusiknas_document_id', 'suspect_id')
            ->withTimestamps();
    }

    public function reportedPersons()
    {
        return $this->belongsToMany(\App\Models\ReportedPerson::class, 'pivot.sp3_pusiknas_document_reported_person', 'sp3_pusiknas_document_id', 'reported_person_id')
            ->withTimestamps();
    }
}
