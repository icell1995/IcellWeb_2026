<?php

namespace App\Models\Doc\SuratPermintaanBlokirRekeningBankDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class SuratPermintaanBlokirRekeningBankDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_permintaan_blokir_rekening_bank_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id'                                    => 'string',
        'accident_id'                           => 'string',
        'surat_perintah_penyidikan_document_id' => 'string',
        'suspect_id'                            => 'string',
        'nomor'                                 => 'string',
        'document_number'                       => 'string',
        'tanggal'                               => 'date',
        'document_date'                         => 'date',
        'sifat'                                 => 'string',
        'lampiran'                              => 'string',
        'perihal'                               => 'string',
        'tempat_surat'                          => 'string',
        'bank_tujuan'                           => 'string',
        'tempat_tujuan'                         => 'string',
        'dasar_kuhp'                            => 'string',
        'dasar_kuhap'                           => 'string',
        'dasar_polri'                           => 'string',
        'dasar_uu_pidana'                       => 'string',
        'dasar_lp_nomor'                        => 'string',
        'dasar_lp_tanggal'                      => 'string',
        'dasar_sp_sidik_nomor'                  => 'string',
        'dasar_sp_sidik_tanggal'                => 'string',
        'satker'                                => 'string',
        'tindak_pidana'                         => 'string',
        'pasal_pidana'                          => 'string',
        'uu_nomor'                              => 'string',
        'uu_tahun'                              => 'string',
        'uu_tentang'                            => 'string',
        'tkp'                                   => 'string',
        'waktu_kejadian'                        => 'string',
        'tersangka_nama'                        => 'string',
        'tersangka_no_identitas'                => 'string',
        'tersangka_kewarganegaraan'             => 'string',
        'tersangka_jenis_kelamin'               => 'string',
        'tersangka_ttl'                         => 'string',
        'tersangka_pekerjaan'                   => 'string',
        'tersangka_agama'                       => 'string',
        'tersangka_alamat'                      => 'string',
        'objek_blokir'                          => 'string',
        'blokir_hari_tanggal'                   => 'string',
        'blokir_waktu'                          => 'string',
        'blokir_tempat'                         => 'string',
        'penyidik_id'                           => 'string',
        'penyidik_sebutan'                      => 'string',
        'penyidik_nama'                         => 'string',
        'penyidik_nohp'                         => 'string',
        'tembusan_1'                            => 'string',
        'tembusan_2'                            => 'string',
        'tembusan_3'                            => 'string',
        'tembusan_4'                            => 'string',
        'tembusan_5'                            => 'string',
        'tembusan'                              => 'json',
        'signatory_id'                          => 'string',
        'signatory_head_text'                   => 'string',
        'signatory_position'                    => 'string',
        'signatory_name'                        => 'string',
        'signatory_rank'                        => 'string',
        'signatory_nrp'                         => 'string',
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
                $model->document_category_id = '0707';
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
            'suspect',
            'officers',
            'signatoryOfficer',
            'investigatorOfficer',
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

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }

    public function officers()
    {
        return $this->hasMany('App\Models\Doc\SuratPermintaanBlokirRekeningBankDocument\SuratPermintaanBlokirRekeningBankDocumentOfficer', 'doc_id', 'id')
            ->orderBy('order_number', 'asc');
    }

    public function signatoryOfficer()
    {
        return $this->belongsTo('App\Models\Officer', 'signatory_id', 'id');
    }

    public function investigatorOfficer()
    {
        return $this->belongsTo('App\Models\Officer', 'penyidik_id', 'id');
    }

    public function getFormattedDocumentDateAttribute()
    {
        if (!$this->tanggal) {
            return '-';
        }
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }
}

