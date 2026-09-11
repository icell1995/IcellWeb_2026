<?php

namespace App\Models\Doc\SuratKesepakatanDiversiDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class SuratKesepakatanDiversiDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_kesepakatan_diversi_documents';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

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
                $model->document_category_id = '0213';
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
            'suratKesepakatanDiversiDocumentOfficers',
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

    public function suratKesepakatanDiversiDocumentOfficers()
    {
        return $this->hasMany('App\Models\Doc\SuratKesepakatanDiversiDocument\SuratKesepakatanDiversiDocumentOfficer', 'surat_kesepakatan_diversi_document_id', 'id');
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
        return $this->hasOne('App\Models\Doc\SuratKesepakatanDiversiDocument\SuratKesepakatanDiversiDocumentAttachment', 'surat_kesepakatan_diversi_document_id', 'id');
    }
}
