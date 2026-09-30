<?php

namespace App\Models\Doc\SuratPerintahPembantaranPenahananDocument;

use App\Observers\UserActionObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPerintahPembantaranPenahananDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_perintah_pembantaran_penahanan_documents';

    protected $primaryKey = 'id';

    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'tembusan' => 'json',
        'ip_addresses' => 'json',
        'timestamps' => 'json',
        'messages' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
            $model->status_id = '2'; // Dokumen Dibuat
            $model->document_category_id = '0605';
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

    // --- RELATIONS ---
    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id');
    }

    public function officers()
    {
        return $this->hasMany(SuratPerintahPembantaranPenahananDocumentOfficer::class, 'document_id', 'id');
    }

    public function suspects()
    {
        return $this->belongsToMany(
            'App\Models\Suspect',
            'doc.surat_perintah_pembantaran_penahanan_document_suspects',
            'document_id',
            'suspect_id'
        );
    }

    public function attachment()
    {
        return $this->hasOne(SuratPerintahPembantaranPenahananDocumentAttachment::class, 'document_id', 'id');
    }

    public function laws()
    {
        return $this->hasMany(SuratPerintahPembantaranPenahananDocumentLaw::class, 'document_id', 'id');
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id', 'id');
    }

    public function getSignatoryAttribute()
    {
        return $this->officers->where('class', 'like', '%SIGNATORY%')->first()
            ?: $this->officers->first();
    }

    public function getDocumentNumberAttribute()
    {
        return $this->nomor_surat;
    }

    public function setDocumentNumberAttribute($value)
    {
        $this->attributes['nomor_surat'] = $value;
    }

    public function getDocumentDateAttribute()
    {
        return $this->tanggal_surat;
    }

    public function setDocumentDateAttribute($value)
    {
        $this->attributes['tanggal_surat'] = $value;
    }
}
