<?php

namespace App\Models\Doc\LaporanHasilGelarPerkaraDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

use App\Models\ReturnDocuments;

class LaporanHasilGelarPerkaraDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table="doc.laporan_hasil_gelar_perkara_documents";

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

     // cast
    protected $casts = [
        'id' => 'string',
        'messages' => 'json',
        'timestamps' => 'json',
        'ip_addresses' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
            $model->status_id = '2';
            $model->document_category_id = '0706';
        });

        self::created(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => '',
                'tipe_update' => 'MEMBUAT',
            ]);
        });

        self::updated(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => '',
                'tipe_update' => 'MENGUBAH',
            ]);
        });

        self::deleted(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => '',
                'tipe_update' => 'MENGHAPUS',
            ]);
        });
    }

    public function scopeWithRelated($query){
        return $query->with([
            'accident',
            'documentCategory',
            'caseDegreeType',
            'timezone',
            'suratPerintahPenyidikanDocument',
            'laporanHasilGelarPerkaraDocumentAttachment',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }

    public function documentCategory(){
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id')->with(['police']);
    }

    public function caseDegreeType()
    {
        return $this->belongsTo('App\Models\Lib\CaseDegreeType', 'case_degree_type_id');
    }

    public function timezone()
    {
        return $this->belongsTo('App\Models\Lib\Timezone', 'timezone_id');
    }

    public function laporanHasilGelarPerkaraDocumentOfficers(){
        return $this->hasMany('App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocumentOfficer', 'laporan_hasil_gelar_perkara_document_id', 'id');
    }

    public function laporanHasilGelarPerkaraDocumentAttachment(){
        return $this->hasOne('App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocumentAttachment', 'laporan_hasil_gelar_perkara_document_id', 'id');
    }

    public function laporanHasilGelarPerkaraDocumentFiles(){
        return $this->hasMany('App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocumentFile', 'laporan_hasil_gelar_perkara_document_id', 'id');
    }

    public function suratPerintahPenyidikanDocument(){
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'surat_perintah_penyidikan_document_id', 'id');
    }

    public function suratPerintahTugasHukumDocument(){
        return $this->belongsTo('App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument', 'surat_perintah_tugas_document_id', 'id');
    }

    public function suspects(){
        return $this->belongsToMany('App\Models\Suspect', 'pivot.laporan_hasil_gelar_perkara_document_suspect', 'laporan_hasil_gelar_perkara_document_id', 'suspect_id')
            ->withRelated();
    }

    public function attachment(){
        return $this->hasOne('App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocumentAttachment', 'laporan_hasil_gelar_perkara_document_id', 'id');
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo('App\Models\User', 'updated_by_user_id', 'id');
    }

    public function deletedByUser()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by_user_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Opt\Status', 'status_id', 'id');
    }

    public function returnDocuments(): MorphMany
    {
        return $this->morphMany(ReturnDocuments::class, 'documentable');
    }
}
