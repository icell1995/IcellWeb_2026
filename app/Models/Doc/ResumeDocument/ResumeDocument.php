<?php

namespace App\Models\Doc\ResumeDocument;

use App\Models\Accident;
use App\Observers\UserActionObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class ResumeDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.resume_documents';

    protected $primaryKey = 'id';

    protected $keyType = 'uuid';

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'ip_addresses' => 'json',
        'timestamps' => 'json',
        'messages' => 'json',
        'dasar' => 'json',
        'perkara' => 'json',
        'fakta_tkp' => 'json',
        'fakta_korban' => 'json',
        'fakta_pemanggilan' => 'json',
        'fakta_perintah_membawa' => 'json',
        'fakta_penangkapan' => 'json',
        'fakta_penahanan' => 'json',
        'fakta_penggeledahan' => 'json',
        'fakta_penyitaan' => 'json',
        'keterangan_saksi' => 'json',
        'keterangan_ahli' => 'json',
        'keterangan_tersangka' => 'json',
        'barang_bukti' => 'json',
        'fakta_penghentian_penyidikan' => 'json',
        'fakta_penyidikan_lanjutan' => 'json',
        'analisa_kasus' => 'json',
        'analisa_yuridis' => 'json',
        'kesimpulan' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Uuid::generate();
            }
            if (!$model->status_id) {
                $model->status_id = '2'; // Dokumen Dibuat
            }
            if (!$model->document_category_id) {
                $model->document_category_id = '0801';
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

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id');
    }

    public function documentCategory()
    {
        return $this->belongsTo(\App\Models\Lib\DocumentCategory::class, 'document_category_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\Lib\Status::class, 'status_id', 'id');
    }

    public function getDocumentNumberAttribute()
    {
        return $this->nomor_surat;
    }

    public function getDocumentDateAttribute()
    {
        return $this->tanggal_surat;
    }
}
