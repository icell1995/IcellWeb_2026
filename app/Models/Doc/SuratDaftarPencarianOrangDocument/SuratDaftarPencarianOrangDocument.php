<?php

namespace App\Models\Doc\SuratDaftarPencarianOrangDocument;

use App\Models\Accident;
use App\Models\User;
use App\Observers\UserActionObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratDaftarPencarianOrangDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_daftar_pencarian_orang_documents';

    protected $primaryKey = 'id';

    protected $keyType = 'uuid';

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'tanggal_surat' => 'date',
        'rujukan_tanggal' => 'date',
        'tersangka_tanggal_lahir' => 'date',
        'ip_addresses' => 'json',
        'timestamps' => 'json',
        'messages' => 'json',
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
                $model->document_category_id = '0302'; // SURAT DAFTAR PENCARIAN ORANG
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

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}

