<?php

namespace App\Models\Doc\SuratPerintahPenangkapanDocument;

use App\Models\ReturnDocuments;
use App\Observers\UserActionObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPerintahPenangkapanDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_perintah_penangkapan_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'messages' => 'json',
        'timestamps' => 'json',
        'ip_addresses' => 'json',
        'payload' => 'json',
        'document_date' => 'date',
        'valid_until_date' => 'date',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
            $model->status_id = $model->status_id ?? '2';
            $model->document_category_id = $model->document_category_id ?? '0301';
        });

        self::created(function ($model) {
            $accident = $model->accident;
            if ($accident) {
                $accident->update([
                    'last_update' => Carbon::now(),
                    'category' => '',
                    'tipe_update' => 'MEMBUAT',
                ]);
            }
        });

        self::updated(function ($model) {
            $accident = $model->accident;
            if ($accident) {
                $accident->update([
                    'last_update' => Carbon::now(),
                    'category' => '',
                    'tipe_update' => 'MENGUBAH',
                ]);
            }
        });

        self::deleted(function ($model) {
            $accident = $model->accident;
            if ($accident) {
                $accident->update([
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
            'attachment',
            'suspect',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id');
    }

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }

    public function suratPerintahTugasDocument()
    {
        return $this->belongsTo(
            'App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument',
            'surat_perintah_tugas_document_id',
            'id'
        );
    }

    public function attachment()
    {
        return $this->hasOne(
            SuratPerintahPenangkapanDocumentAttachment::class,
            'surat_perintah_penangkapan_document_id',
            'id'
        );
    }

    public function officers()
    {
        return $this->hasMany(
            SuratPerintahPenangkapanDocumentOfficer::class,
            'surat_perintah_penangkapan_document_id',
            'id'
        );
    }

    public function signatory()
    {
        return $this->hasOne(
            SuratPerintahPenangkapanDocumentOfficer::class,
            'surat_perintah_penangkapan_document_id',
            'id'
        )->where('class', SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'SIGNATORY'))
            ->orderBy('sort');
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
