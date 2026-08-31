<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lib\DocumentCategory;
use App\Models\Lib\Timezone;
use App\Models\User;
use App\Models\Officer;
use App\Models\Suspect;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class BeritaAcaraPenahanan extends Model
{
    use HasFactory;

    protected $table = 'berita_acara_penahanan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'accident_id',
        'name',
        'category',
        'initial',
        'created_by',
        'status_id',
        'document_date',
        'properties',
    ];

    protected $casts = [
        'properties' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
            if (empty($model->status_id)) {
                $model->status_id = '2';
            }
        });

        self::created(function ($model) {
            $accident = $model->accident;
            if ($accident) {
                $accident->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D040102',
                    'tipe_update' => 'MEMBUAT',
                ]);
            }
        });

        self::updated(function ($model) {
            $accident = $model->accident;
            if ($accident) {
                $accident->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D040102',
                    'tipe_update' => 'MENGUBAH',
                ]);
            }
        });

        self::deleted(function ($model) {
            $accident = $model->accident;
            if ($accident) {
                $accident->update([
                    'last_update' => Carbon::now(),
                    'category' => 'D040102',
                    'tipe_update' => 'MENGHAPUS',
                ]);
            }
        });
    }

    public function documentCategory()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id', 'id')->withDefault(function() {
            return DocumentCategory::where('id', '0605')->first();
        });
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Opt\Status', 'status_id', 'id');
    }

    public function attachment()
    {
        return $this->hasOne('App\Models\Doc\BeritaAcaraPenahananDocument\BeritaAcaraPenahananDocumentAttachment', 'berita_acara_penahanan_document_id', 'id');
    }

    public function getDocumentCategoryIdAttribute()
    {
        return '0605';
    }

    public function getDocumentNumberAttribute()
    {
        return null; // Berita Acara tidak memiliki nomor surat sendiri (seperti LHGP), No Dokumen kosong dan opsi "Meminta Persetujuan"
    }

    public function getStatusIdAttribute()
    {
        return $this->attributes['status_id'] ?? ($this->properties['status_id'] ?? '2'); // Default: '2' (Dokumen Dibuat)
    }

    public function setStatusIdAttribute($value)
    {
        $this->attributes['status_id'] = (string) $value;
        $props = $this->properties ?? [];
        $props['status_id'] = (string) $value;
        $this->properties = $props;
    }

    public function getSignatoryAttribute()
    {
        return null;
    }

    public function getMessagesAttribute()
    {
        return $this->properties['messages'] ?? [];
    }

    public function setMessagesAttribute($value)
    {
        $props = $this->properties ?? [];
        $props['messages'] = $value;
        $this->properties = $props;
    }

    // Accessor attributes from properties json
    public function getPlaceAttribute() { return $this->properties['place'] ?? null; }
    public function getTimeAttribute() { return $this->properties['time'] ?? null; }
    public function getTimezoneIdAttribute() { return $this->properties['timezone_id'] ?? null; }
    public function getOfficerLeaderIdAttribute() { return $this->properties['officer_leader_id'] ?? null; }
    public function getInvestigatorRoleAttribute() { return $this->properties['investigator_role'] ?? null; }
    public function getReferenceDocumentIdAttribute() { return $this->properties['surat_perintah_penahanan_document_id'] ?? null; }
    public function getReferenceDocumentNumberAttribute() { return $this->properties['surat_perintah_penahanan_document_number'] ?? null; }
    public function getReferenceDocumentDateAttribute() { return $this->properties['surat_perintah_penahanan_date'] ?? null; }
    public function getSuspectIdAttribute() { return $this->properties['suspect_id'] ?? null; }
    public function getDetentionPlaceAttribute() { return $this->properties['detention_place'] ?? null; }
    public function getDetentionBranchAttribute() { return $this->properties['detention_branch'] ?? null; }
    public function getStartDateAttribute() { return $this->properties['start_date'] ?? null; }
    public function getEndDateAttribute() { return $this->properties['end_date'] ?? null; }
    public function getTaskAttribute() { return $this->properties['task'] ?? null; }
    public function getHealthConditionAttribute() { return $this->properties['health_condition'] ?? null; }
    public function getDocumentDateAttribute()
    {
        return $this->attributes['document_date'] ?? ($this->properties['document_date'] ?? ($this->created_at ? $this->created_at->format('Y-m-d') : date('Y-m-d')));
    }
    public function setDocumentDateAttribute($value)
    {
        $this->attributes['document_date'] = $value;
        $props = $this->properties ?? [];
        $props['document_date'] = $value;
        $this->properties = $props;
    }
    public function getCrimeArticleAttribute() { return $this->properties['crime_article'] ?? null; }
    public function getCrimeDescriptionAttribute() { return $this->properties['crime_description'] ?? null; }
    public function getInternalOfficersAttribute() { return $this->properties['internal_officers'] ?? []; }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->with(['rank']);
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id', 'id');
    }
}
