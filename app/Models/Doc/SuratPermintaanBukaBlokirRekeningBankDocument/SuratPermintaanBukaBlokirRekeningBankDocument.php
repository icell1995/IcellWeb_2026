<?php

namespace App\Models\Doc\SuratPermintaanBukaBlokirRekeningBankDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

use App\Models\Accident;
use App\Models\Suspect;
use App\Models\Officer;

class SuratPermintaanBukaBlokirRekeningBankDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_permintaan_buka_blokir_rekening_bank_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id'                                        => 'string',
        'accident_id'                               => 'string',
        'status_id'                                 => 'string',
        'document_category_id'                      => 'string',

        // Kop
        'daerah_police_full_name'                   => 'string',
        'resor_police_full_name'                    => 'string',
        'resor_police_address'                      => 'string',

        // Metadata & Bank Tujuan
        'document_number'                           => 'string',
        'document_location'                         => 'string',
        'document_date'                             => 'string',
        'document_classification_name'              => 'string',
        'appendix'                                  => 'string',
        'target_bank_name'                          => 'string',
        'target_bank_location'                      => 'string',

        // Dasar Rujukan
        'dasar_kuhap'                               => 'string',
        'undang_undang_lain'                        => 'string',
        'accident_number'                           => 'string',
        'accident_date'                             => 'string',
        'surat_perintah_penyidikan_document_number' => 'string',
        'surat_perintah_penyidikan_document_date'   => 'string',
        'surat_pemblokiran_nomor'                   => 'string',
        'surat_pemblokiran_tanggal'                 => 'string',
        'nama_pengadilan_negeri'                    => 'string',
        'penetapan_court_number'                    => 'string',
        'penetapan_court_date'                      => 'string',
        'surat_perintah_kepala_instansi'            => 'string',
        'surat_perintah_kepala_nomor'               => 'string',

        // Informasi Perkara & Pihak Bank
        'target_bank_leader_title'                  => 'string',
        'satker_penyidik'                           => 'string',
        'dugaan_tindak_pidana'                      => 'string',
        'pasal_diduga'                              => 'string',
        'uu_nomor'                                  => 'string',
        'uu_tahun'                                  => 'string',
        'uu_tentang'                                => 'string',
        'tempat_kejadian'                           => 'string',
        'waktu_kejadian'                            => 'string',

        // Identitas Tersangka
        'suspect_id'                                => 'string',
        'suspect_name'                              => 'string',
        'suspect_identity_number'                   => 'string',
        'suspect_nationality'                       => 'string',
        'suspect_gender_name'                       => 'string',
        'suspect_birth_place'                       => 'string',
        'suspect_birth_date'                        => 'string',
        'suspect_job_name'                          => 'string',
        'suspect_religion_name'                     => 'string',
        'suspect_full_address'                      => 'string',

        // Rekening & Sitaan
        'account_number_1'                          => 'string',
        'account_holder_name_1'                     => 'string',
        'account_owner_type_1'                      => 'string',
        'account_number_2_line'                     => 'string',
        'seizure_amount'                            => 'string',
        'escrow_bank_name'                          => 'string',
        'escrow_account_number'                     => 'string',
        'escrow_account_holder_name'                => 'string',
        'escrow_account_owner_type'                 => 'string',

        // Narahubung
        'contact_officer_id'                        => 'string',
        'contact_officer_role'                      => 'string',
        'contact_officer_name'                      => 'string',
        'contact_officer_phone'                     => 'string',

        // Tembusan
        'carbon_copy_2'                             => 'string',

        // TTD
        'signatory_id'                              => 'string',
        'signatory_head_text'                       => 'string',
        'signatory_position_name'                   => 'string',
        'signatory_name'                            => 'string',
        'signatory_rank_name'                       => 'string',
        'signatory_register_number'                 => 'string',

        'created_by_user_id'                        => 'integer',
        'updated_by_user_id'                        => 'integer',
        'deleted_by_user_id'                        => 'integer',
        'is_active'                                 => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
            if (empty($model->document_category_id)) {
                $model->document_category_id = '0708';
            }
            if (empty($model->status_id)) {
                $model->status_id = '2';
            }
        });

        self::observe(new UserActionObserver);
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id');
    }

    public function suspect()
    {
        return $this->belongsTo(Suspect::class, 'suspect_id');
    }

    public function contactOfficer()
    {
        return $this->belongsTo(Officer::class, 'contact_officer_id');
    }

    public function signatoryOfficer()
    {
        return $this->belongsTo(Officer::class, 'signatory_id');
    }
}

