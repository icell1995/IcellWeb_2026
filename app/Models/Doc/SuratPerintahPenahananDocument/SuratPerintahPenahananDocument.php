<?php

namespace App\Models\Doc\SuratPerintahPenahananDocument;

use App\Models\Accident;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Lib\DocumentCategory;
use App\Models\Opt\Status;
use App\Models\Suspect;
use App\Models\User;
use App\Observers\UserActionObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratPerintahPenahananDocument extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = "doc.surat_perintah_penahanan_documents";

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $fillable = [
        'accident_id',
        'surat_perintah_penyidikan_document_id',
        'surat_ketetapan_penetapan_tersangka_id',
        'document_number',
        'document_date',
        'jenis_penahanan',
        'lokasi_penahanan',
        'cabang_penahanan',
        'status_id',
        'document_category_id',
        'is_active',
        'last_synced_at',
        'released_at',
        'is_legacy',
        'created_by_user_id',
        'updated_by_user_id',
        'deleted_by_user_id',
        'ip_addresses',
        'messages',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'id'              => 'string',
        'document_date'   => 'date',
        'last_synced_at'  => 'datetime',
        'released_at'     => 'datetime',
        'approved_at'     => 'datetime',
        'rejected_at'     => 'datetime',
        'is_active'       => 'boolean',
        'is_legacy'       => 'boolean',
        'ip_addresses'    => 'json',
        'messages'        => 'json',
        'timestamps'      => 'json',
    ];

    protected static function boot()
    {
        parent::boot();

        static::observe(UserActionObserver::class);

        static::creating(function ($model) {
            $model->status_id = '2';
            $model->document_category_id = '0601';
        });

        static::created(function ($model) {
            $model->updateLastAccident('MEMBUAT');
        });

        static::updated(function ($model) {
            $model->updateLastAccident('MENGUBAH');
        });

        static::deleted(function ($model) {
            $model->updateLastAccident('MENGHAPUS');
        });
    }

    private function updateLastAccident(string $tipe): void
    {
        $accident = $this->accident;
        if ($accident) {
            $accident->update([
                'last_update' => Carbon::now(),
                'category'    => '',
                'tipe_update' => $tipe,
            ]);
        }
    }

    public function scopeWithRelated($query)
    {
        return $query->with([
            'accident',
            'documentCategory',
            'suratPerintahPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument',
            'suratPerintahPenahananDocumentAttachment',
            'suspect',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }

    public function accident(): BelongsTo
    {
        return $this->belongsTo(Accident::class, 'accident_id');
    }

    public function suratPerintahPenyidikanDocument(): BelongsTo
    {
        return $this->belongsTo(
            SuratPerintahPenyidikanDocument::class,
            'surat_perintah_penyidikan_document_id'
        );
    }

    public function suratKetetapanPenetapanTersangkaDocument(): BelongsTo
    {
        return $this->belongsTo(
            SuratKetetapanTentangPenetapanTersangkaDocument::class,
            'surat_ketetapan_penetapan_tersangka_id'
        );
    }

    public function suratPerintahPenahananDocumentAttachment(){
        return $this->hasOne('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentAttachment', 'surat_perintah_penahanan_document_id', 'id');
    }

    public function attachment(){
        return $this->hasOne('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentAttachment', 'surat_perintah_penahanan_document_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'id');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id', 'id');
    }

    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id', 'id');
    }

    public function suratPerintahPenahananDocumentOfficers()
    {
        return $this->hasMany(
            SuratPerintahPenahananDocumentOfficer::class,
            'surat_perintah_penahanan_document_id'
        );
    }

    public function signatory()
    {
        return $this->hasOne(
            SuratPerintahPenahananDocumentOfficer::class,
            'surat_perintah_penahanan_document_id'
        )->where('class', SuratPerintahPenahananDocumentOfficer::getEnumOption('class', 'SIGNATORY'));
    }

    public function suspect()
    {
        return $this->belongsToMany(
            Suspect::class,
            'pivot.surat_perintah_penahanan_document_suspect',
            'surat_perintah_penahanan_document_id',
            'suspect_id'
        );
    }
}
