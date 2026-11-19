<?php

namespace App\Models\Doc\SuratPenunjukanPenasihatHukumDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

use App\Models\Accident;
use App\Models\Suspect;
use App\Models\Officer;

class SuratPenunjukanPenasihatHukumDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_penunjukan_penasihat_hukum_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id'                                    => 'string',
        'accident_id'                           => 'string',
        'status_id'                             => 'string',
        'document_category_id'                  => 'string',

        // Kop
        'daerah_police_full_name'               => 'string',
        'resor_police_full_name'                => 'string',
        'resor_police_address'                  => 'string',

        // Metadata & Tujuan
        'document_number'                       => 'string',
        'document_location'                     => 'string',
        'document_date'                         => 'string',
        'document_classification_name'          => 'string',
        'appendix'                              => 'string',
        'target_legal_aid_name'                 => 'string',
        'target_legal_aid_location'             => 'string',

        // Rujukan
        'dasar_kuhap'                           => 'string',
        'undang_undang_lain'                    => 'string',
        'accident_number'                       => 'string',
        'accident_date'                         => 'string',
        'surat_perintah_penyidikan_document_number' => 'string',
        'surat_perintah_penyidikan_document_date'   => 'string',
        'surat_keterangan_tidak_mampu_instansi'     => 'string',
        'surat_keterangan_tidak_mampu_number'       => 'string',
        'surat_keterangan_tidak_mampu_date'         => 'string',
        'surat_keterangan_tidak_mampu_person_name'  => 'string',

        // Tersangka
        'suspect_id'                            => 'string',
        'suspect_name'                          => 'string',
        'suspect_identity_number'               => 'string',
        'suspect_nationality'                   => 'string',
        'suspect_gender_name'                   => 'string',
        'suspect_birth_place'                   => 'string',
        'suspect_birth_date'                    => 'string',
        'suspect_job_name'                      => 'string',
        'suspect_religion_name'                 => 'string',
        'suspect_full_address'                  => 'string',

        // Perkara
        'dugaan_tindak_pidana'                  => 'string',
        'pasal_diduga'                          => 'string',
        'uu_nomor'                              => 'string',
        'uu_tahun'                              => 'string',
        'uu_tentang'                            => 'string',
        'tempat_kejadian'                       => 'string',
        'waktu_kejadian'                        => 'string',
        'kurun_waktu'                           => 'string',

        // Jadwal & Narahubung
        'examination_day'                       => 'string',
        'examination_date'                      => 'string',
        'examination_time'                      => 'string',
        'examination_office_name'               => 'string',
        'examination_office_address'            => 'string',
        'contact_officer_id'                    => 'string',
        'contact_officer_role'                  => 'string',
        'contact_officer_name'                  => 'string',
        'contact_officer_phone'                 => 'string',

        // Tembusan
        'carbon_copy_1'                         => 'string',
        'carbon_copy_2'                         => 'string',
        'carbon_copy_3'                         => 'string',

        // TTD
        'signatory_id'                          => 'string',
        'signatory_head_text'                   => 'string',
        'signatory_position_name'               => 'string',
        'signatory_name'                        => 'string',
        'signatory_rank_name'                   => 'string',
        'signatory_register_number'             => 'string',

        'created_by_user_id'                    => 'integer',
        'updated_by_user_id'                    => 'integer',
        'deleted_by_user_id'                    => 'integer',
        'is_active'                             => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
            if (empty($model->document_category_id)) {
                $model->document_category_id = '0704';
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

