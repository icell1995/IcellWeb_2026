<?php

namespace App\Models\Doc\DaftarPencarianBarangDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

use App\Models\Accident;
use App\Models\CaseVehicle;
use App\Models\Officer;
use App\Models\Doc\DocumentCategory;

class DaftarPencarianBarangDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.daftar_pencarian_barang_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id'                        => 'string',
        'accident_id'               => 'string',
        'status_id'                 => 'string',
        'document_category_id'      => 'string',
        'case_vehicle_id'           => 'string',

        'daerah_police_full_name'   => 'string',
        'resor_police_full_name'    => 'string',
        'resor_police_address'      => 'string',

        'document_number'           => 'string',
        'accident_number'           => 'string',
        'accident_date'             => 'string',
        'court_permit_text'         => 'string',

        'dugaan_tindak_pidana'      => 'string',
        'pasal_diduga'              => 'string',
        'uu_nomor'                  => 'string',
        'uu_tahun'                  => 'string',
        'uu_tentang'                => 'string',
        'tempat_kejadian'           => 'string',
        'waktu_kejadian'            => 'string',
        'contact_officer_id'        => 'string',
        'contact_officer_role'      => 'string',
        'contact_officer_name'      => 'string',
        'contact_officer_phone'     => 'string',

        'item_name'                 => 'string',
        'quantity'                  => 'string',
        'registration_number'       => 'string',
        'cylinder_capacity'         => 'string',
        'fuel_type'                 => 'string',
        'tnkb_color'                => 'string',
        'registration_year'         => 'string',
        'bpkb_number'               => 'string',
        'special_signs_other'       => 'string',

        'item_type'                 => 'string',
        'item_category'             => 'string',
        'item_model'                => 'string',
        'physical_features_other'   => 'string',

        'item_color'                => 'string',
        'manufacture_year'          => 'string',
        'estimated_price'           => 'string',
        'trade_mark'                => 'string',
        'engine_frame_number'       => 'string',
        'manufacture_place'         => 'string',
        'loss_chronology'           => 'string',
        'important_notes'           => 'string',

        'kepada_3'                  => 'string',

        'document_location'         => 'string',
        'document_date'             => 'string',
        'signatory_id'              => 'string',
        'signatory_head_text'       => 'string',
        'signatory_position_name'   => 'string',
        'signatory_name'            => 'string',
        'signatory_rank_name'       => 'string',
        'signatory_register_number' => 'string',

        'created_by_user_id'        => 'integer',
        'updated_by_user_id'        => 'integer',
        'deleted_by_user_id'        => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
            if (empty($model->status_id)) {
                $model->status_id = '2';
            }
            if (empty($model->document_category_id)) {
                $model->document_category_id = '0705';
            }
        });

        self::observe(UserActionObserver::class);
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id', 'id');
    }

    public function caseVehicle()
    {
        return $this->belongsTo(CaseVehicle::class, 'case_vehicle_id', 'id');
    }

    public function contactOfficer()
    {
        return $this->belongsTo(Officer::class, 'contact_officer_id', 'id');
    }

    public function signatoryOfficer()
    {
        return $this->belongsTo(Officer::class, 'signatory_id', 'id');
    }

    public function documentCategory()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id', 'id');
    }
}
