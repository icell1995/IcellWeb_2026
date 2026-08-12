<?php

namespace App\Models\Doc\Tahap1Document;

use App\Models\Accident;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Lib\Prosecutor;
use App\Models\Suspect;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tahap1Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.tahap_1_documents';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'accident_id',
        'document_number',
        'document_date',
        'klasifikasi',
        'lampiran',
        'perihal',
        'prosecutor_id',
        'location_city',
        'rujukan_uu_others',
        'surat_perintah_penyidikan_id',
        'surat_pemberitahuan_dimulainya_penyidikan_id',
        'surat_ketetapan_penetapan_tersangka_id',
        'berkas_perkara_number',
        'berkas_perkara_date',
        'berkas_perkara_rangkap',
        'dugaan_tindak_pidana',
        'pasal_disangkakan',
        'penahanan_rutan',
        'penahanan_cabang',
        'penahanan_start_date',
        'penahanan_end_date',
        'surat_perintah_penahanan_number',
        'surat_perintah_penahanan_date',
        'surat_perpanjangan_penahanan_number',
        'surat_perpanjangan_penahanan_date',
        'surat_perpanjangan_penahanan_court_number',
        'surat_perpanjangan_penahanan_court_date',
        'barang_bukti_storage',
        'investigator_pangkat_nama',
        'investigator_hp',
        'tembusan',
        'barang_bukti',
        'jumlah_bb',
        'penahanan_status',
        'surat_penangguhan_penahanan_number',
        'surat_penangguhan_penahanan_date',
        'is_active',
        'is_legacy',
        'status_id',
        'document_category_id',
        'created_by_user_id',
        'updated_by_user_id',
        'deleted_by_user_id',
        'messages',
        'timestamps',
        'released_at',
        'last_synced_at',
        'approved_at',
        'rejected_at',
        'ip_addresses',
    ];

    protected $casts = [
        'document_date' => 'datetime',
        'berkas_perkara_date' => 'date',
        'penahanan_start_date' => 'date',
        'penahanan_end_date' => 'date',
        'surat_perintah_penahanan_date' => 'date',
        'surat_perpanjangan_penahanan_date' => 'date',
        'surat_perpanjangan_penahanan_court_date' => 'date',
        'surat_penangguhan_penahanan_date' => 'date',
        'tembusan' => 'json',
        'barang_bukti' => 'json',
        'messages' => 'json',
        'timestamps' => 'json',
        'ip_addresses' => 'json',
        'is_active' => 'boolean',
        'is_legacy' => 'boolean',
        'released_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id');
    }

    public function prosecutor()
    {
        return $this->belongsTo(Prosecutor::class, 'prosecutor_id');
    }

    public function suratPerintahPenyidikan()
    {
        return $this->belongsTo(SuratPerintahPenyidikanDocument::class, 'surat_perintah_penyidikan_id');
    }

    public function suratPemberitahuanDimulainyaPenyidikan()
    {
        return $this->belongsTo(SuratPemberitahuanDimulainyaPenyidikanDocument::class, 'surat_pemberitahuan_dimulainya_penyidikan_id');
    }

    public function suratKetetapanTentangPenetapanTersangka()
    {
        return $this->belongsTo(SuratKetetapanTentangPenetapanTersangkaDocument::class, 'surat_ketetapan_penetapan_tersangka_id');
    }

    public function officers()
    {
        return $this->hasMany(Tahap1DocumentOfficer::class, 'tahap_1_document_id');
    }

    public function attachments()
    {
        return $this->hasMany(Tahap1DocumentAttachment::class, 'tahap_1_document_id');
    }

    public function attachment()
    {
        // For polymorphic or single relation backward compatibility in DocumentActionController
        return $this->hasOne(Tahap1DocumentAttachment::class, 'tahap_1_document_id');
    }

    public function documentCategory()
    {
        return $this->belongsTo(\App\Models\Lib\DocumentCategory::class, 'document_category_id');
    }

    public function suspects()
    {
        return $this->belongsToMany(Suspect::class, 'pivot.tahap_1_document_suspect', 'tahap_1_document_id', 'suspect_id')
            ->using(Tahap1DocumentSuspectPivot::class)
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function deletedByUser()
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\Opt\Status::class, 'status_id');
    }

    /**
     * Check if document is editable
     * Status: 1 = DRAFT, 2 = DIBUAT, 4 = REVISI
     */
    public function isEditable()
    {
        return in_array($this->status_id, ['1', '2', '4']) || empty($this->status_id);
    }

    /**
     * Check if document can be submitted
     * Status: 2 = DIBUAT
     */
    public function canBeSubmitted()
    {
        return $this->status_id === '2';
    }

    /**
     * Check if document can be approved
     * Status: 3 = MENUNGGU PERSETUJUAN
     */
    public function canBeApproved()
    {
        return $this->status_id === '3';
    }
}
