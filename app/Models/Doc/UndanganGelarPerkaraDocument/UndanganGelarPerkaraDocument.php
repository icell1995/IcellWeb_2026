<?php

namespace App\Models\Doc\UndanganGelarPerkaraDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class UndanganGelarPerkaraDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.undangan_gelar_perkara_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id'                                    => 'string',
        'accident_id'                           => 'string',
        'surat_perintah_penyelidikan_document_id'=> 'string',
        'surat_perintah_penyidikan_document_id' => 'string',
        'nomor'                                 => 'string',
        'document_number'                       => 'string',
        'tanggal'                               => 'date',
        'document_date'                         => 'date',
        'sifat'                                 => 'string',
        'lampiran'                              => 'string',
        'perihal'                               => 'string',
        'tempat_surat'                          => 'string',
        'dasar_hukum'                           => 'string',
        'nomor_lp'                              => 'string',
        'tanggal_lp'                            => 'date',
        'nomor_sprindik'                        => 'string',
        'tanggal_sprindik'                      => 'date',
        'hari_pelaksanaan'                      => 'string',
        'tanggal_pelaksanaan'                   => 'date',
        'jam_pelaksanaan'                       => 'string',
        'tempat_pelaksanaan'                    => 'string',
        'agenda'                                => 'string',
        'tujuan_undangan'                       => 'string',
        'signatory_id'                          => 'string',
        'signatory_head_text'                   => 'string',
        'signatory_position'                    => 'string',
        'signatory_name'                        => 'string',
        'signatory_rank'                        => 'string',
        'signatory_nrp'                         => 'string',
        'tembusan'                              => 'json',
        'catatan'                               => 'string',
        'messages'                              => 'json',
        'ip_addresses'                          => 'json',
        'payload'                               => 'json',
        'status_id'                             => 'string',
        'document_category_id'                  => 'string',
        'is_active'                             => 'boolean',
        'is_legacy'                             => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
            if (empty($model->status_id)) {
                $model->status_id = '2';
            }
            if (empty($model->document_category_id)) {
                $model->document_category_id = '0701';
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
            'suratPerintahPenyidikanDocument',
            'suratPerintahPenyelidikanDocument',
            'officers',
            'signatoryOfficer',
        ]);
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id');
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'surat_perintah_penyidikan_document_id', 'id');
    }

    public function suratPerintahPenyelidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument', 'surat_perintah_penyelidikan_document_id', 'id');
    }

    public function officers()
    {
        return $this->hasMany('App\Models\Doc\UndanganGelarPerkaraDocument\UndanganGelarPerkaraDocumentOfficer', 'doc_id', 'id')
            ->orderBy('order_number', 'asc');
    }

    public function invitees()
    {
        return $this->hasMany('App\Models\Doc\UndanganGelarPerkaraDocument\UndanganGelarPerkaraDocumentOfficer', 'doc_id', 'id')
            ->where('class', 'INVITEE')
            ->orderBy('order_number', 'asc');
    }

    public function signatoryOfficer()
    {
        return $this->belongsTo('App\Models\Officer', 'signatory_id', 'id');
    }

    public function getFormattedDocumentDateAttribute()
    {
        if (!$this->tanggal) {
            return '-';
        }
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    public function getFormattedTanggalPelaksanaanAttribute()
    {
        if (!$this->tanggal_pelaksanaan) {
            return '-';
        }
        return Carbon::parse($this->tanggal_pelaksanaan)->translatedFormat('d F Y');
    }
}
