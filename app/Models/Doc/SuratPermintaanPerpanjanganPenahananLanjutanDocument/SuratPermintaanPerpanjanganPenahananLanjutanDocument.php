<?php

namespace App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument;

use App\Observers\UserActionObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPermintaanPerpanjanganPenahananLanjutanDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_permintaan_perpanjangan_penahanan_lanjutan_documents';

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
            $model->document_category_id = '0603';
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
        return $this->hasMany(SuratPermintaanPerpanjanganPenahananLanjutanDocumentOfficer::class, 'document_id', 'id');
    }

    public function suspects()
    {
        // Pakai belongsToMany langsung menembak ke table Pivot Tersangka
        return $this->belongsToMany('App\Models\Suspect', 'doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_suspects', 'document_id', 'suspect_id');
    }

    public function attachment()
    {
        return $this->hasOne(SuratPermintaanPerpanjanganPenahananLanjutanDocumentAttachment::class, 'document_id', 'id');
    }

    public function laws()
    {
        return $this->hasMany(SuratPermintaanPerpanjanganPenahananLanjutanDocumentLaw::class, 'document_id', 'id');
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Opt\Status', 'status_id', 'id');
    }

    public function kejaksaan()
    {
        return $this->belongsTo('App\Models\Lib\Prosecutor', 'kejaksaan_id', 'id');
    }

    public function contactOfficer()
    {
        return $this->belongsTo('App\Models\Officer', 'contact_officer_id', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo('App\Models\User', 'updated_by_user_id', 'id');
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
