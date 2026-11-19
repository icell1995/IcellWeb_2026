<?php

namespace App\Models\Doc\SuratKuasaDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class SuratKuasaDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_kuasa_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id'                           => 'string',
        'accident_id'                  => 'string',
        'nomor'                        => 'string',
        'document_number'              => 'string',
        'tanggal'                      => 'date',
        'document_date'                => 'date',
        'tempat_surat'                 => 'string',
        'judul_surat'                  => 'string',

        // Pemberi Kuasa
        'pemberi_nama'                 => 'string',
        'pemberi_no_identitas'         => 'string',
        'pemberi_kewarganegaraan'      => 'string',
        'pemberi_jenis_kelamin'        => 'string',
        'pemberi_ttl'                  => 'string',
        'pemberi_umur'                 => 'string',
        'pemberi_pekerjaan'            => 'string',
        'pemberi_agama'                => 'string',
        'pemberi_alamat'               => 'string',
        'pemberi_nohp'                 => 'string',
        'pemberi_telp'                 => 'string',
        'pemberi_email'                => 'string',
        'pemberi_medsos_platform'      => 'string',
        'pemberi_medsos_akun'          => 'string',
        'pemberi_kuasa_nama'           => 'string',
        'pemberi_kuasa_nik'            => 'string',
        'pemberi_kuasa_tempat_lahir'   => 'string',
        'pemberi_kuasa_tanggal_lahir'  => 'date',
        'pemberi_kuasa_umur'           => 'string',
        'pemberi_kuasa_jenis_kelamin'  => 'string',
        'pemberi_kuasa_agama'          => 'string',
        'pemberi_kuasa_pekerjaan'      => 'string',
        'pemberi_kuasa_kewarganegaraan'=> 'string',
        'pemberi_kuasa_alamat'         => 'string',
        'pemberi_kuasa_telepon'        => 'string',
        'pemberi_kuasa_kedudukan'      => 'string',

        // Penerima Kuasa
        'penerima_nama'                => 'string',
        'penerima_no_identitas'        => 'string',
        'penerima_kewarganegaraan'     => 'string',
        'penerima_jenis_kelamin'       => 'string',
        'penerima_ttl'                 => 'string',
        'penerima_umur'                => 'string',
        'penerima_pekerjaan'           => 'string',
        'penerima_agama'               => 'string',
        'penerima_alamat'              => 'string',
        'penerima_nohp'                => 'string',
        'penerima_telp'                => 'string',
        'penerima_email'               => 'string',
        'penerima_medsos_platform'     => 'string',
        'penerima_medsos_akun'         => 'string',
        'penerima_kuasa_nama'          => 'string',
        'penerima_kuasa_nik'           => 'string',
        'penerima_kuasa_tempat_lahir'  => 'string',
        'penerima_kuasa_tanggal_lahir' => 'date',
        'penerima_kuasa_umur'          => 'string',
        'penerima_kuasa_jenis_kelamin' => 'string',
        'penerima_kuasa_agama'         => 'string',
        'penerima_kuasa_pekerjaan'     => 'string',
        'penerima_kuasa_kantor'        => 'string',
        'penerima_kuasa_alamat'        => 'string',
        'penerima_kuasa_telepon'       => 'string',
        'penerima_kuasa_kewarganegaraan'=> 'string',

        // Pokok Perkara & Kuasa
        'nomor_lp'                     => 'string',
        'tanggal_lp'                   => 'date',
        'pokok_perkara'                => 'string',
        'isi_kuasa'                    => 'string',
        'catatan'                      => 'string',

        // Khusus & Perkara Baru
        'tindak_pidana'                => 'string',
        'pasal_pidana'                 => 'string',
        'uu_nomor'                     => 'string',
        'uu_tahun'                     => 'string',
        'uu_tentang'                   => 'string',
        'tkp'                          => 'string',
        'waktu_kejadian'               => 'string',

        // Rujukan
        'dasar_lp_nomor'               => 'string',
        'dasar_lp_tanggal'             => 'string',
        'dasar_sp_sidik_nomor'         => 'string',
        'dasar_sp_sidik_tanggal'       => 'string',
        'dasar_sp_gas_sidik_nomor'     => 'string',
        'dasar_sp_gas_sidik_tanggal'   => 'string',

        // Surat Penetapan Penunjukan Penasehat Hukum
        'surat_penetapan_nomor'        => 'string',
        'surat_penetapan_tanggal'      => 'string',

        // Tanggal & Tempat
        'dikeluarkan_di'               => 'string',
        'tanggal_surat'                => 'string',

        // Mengetahui
        'mengetahui_signatory_id'      => 'string',
        'mengetahui_nama'              => 'string',
        'mengetahui_pangkat'           => 'string',
        'mengetahui_nrp'               => 'string',
        'mengetahui_jabatan'           => 'string',

        // Meta
        'status_id'                    => 'string',
        'document_category_id'         => 'string',
        'is_active'                    => 'boolean',
        'is_legacy'                    => 'boolean',
        'messages'                     => 'json',
        'ip_addresses'                 => 'json',
        'payload'                      => 'json',
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
                $model->document_category_id = '0703';
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
            'mengetahuiOfficer',
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

    public function mengetahuiOfficer()
    {
        return $this->belongsTo('App\Models\Officer', 'mengetahui_signatory_id', 'id');
    }

    public function getFormattedTanggalAttribute()
    {
        if (!$this->tanggal) {
            return '-';
        }
        return Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    public function getFormattedTanggalLpAttribute()
    {
        if (!$this->tanggal_lp) {
            return '-';
        }
        return Carbon::parse($this->tanggal_lp)->translatedFormat('d F Y');
    }

    public function getFormattedPemberiKuasaTanggalLahirAttribute()
    {
        if (!$this->pemberi_kuasa_tanggal_lahir) {
            return null;
        }
        return Carbon::parse($this->pemberi_kuasa_tanggal_lahir)->translatedFormat('d F Y');
    }

    public function getFormattedPenerimaKuasaTanggalLahirAttribute()
    {
        if (!$this->penerima_kuasa_tanggal_lahir) {
            return null;
        }
        return Carbon::parse($this->penerima_kuasa_tanggal_lahir)->translatedFormat('d F Y');
    }
}
