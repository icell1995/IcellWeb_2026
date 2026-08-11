<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accident extends Model
{
    use HasFactory;

    protected $table = 'accidents';
    protected $primaryKey = 'id';
    public $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];

    public function documents($documentType)
    {
        $documentClass = "App\\Models\\Doc\\{$documentType}\\{$documentType}";

        return $this->hasMany($documentClass, 'accident_id', 'id')->with(['documentCategory']);
    }

    public function accidentResolution()
    {
        return $this->hasOne('App\Models\AccidentResolution', 'accident_id', 'id');
    }

    public function police()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id');
    }

    public function polres()
    {
        return $this->belongsTo(Polres::class, 'polres_id', 'id')->with('polda');
    }

    public function polda()
    {
        return $this->belongsTo(Polda::class);
    }

    public function ref()
    {
        return $this->belongsTo(Ref::class, 'selra_flag');
    }

    public function officer_surat_tugas()
    {
        return $this->belongsToMany(Officer::class, 'surat_tugas')->withTimestamps();
    }

    public function officer_surat_penyelidikan()
    {
        return $this->belongsToMany(Officer::class, 'surat_penyelidikan')->withTimestamps();
    }

    public function officer_surat_penyidikan()
    {
        return $this->belongsToMany(Officer::class, 'surat_penyidikan')->withTimestamps();
    }

    public function officer_surat_penyitaan()
    {
        return $this->belongsToMany(Officer::class, 'surat_penyitaan')->withTimestamps();
    }

    public function officer_surat_penyegelan()
    {
        return $this->belongsToMany(Officer::class, 'surat_penyegelan')->withTimestamps();
    }

    // public function investigationOrderLetter()
    // {
    //     return $this->hasOne(InvestigationOrderLetter::class, 'accident_id');
    // }

    // public function investigationOrderLetters()
    // {
    //     return $this->hasMany('App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter', 'accident_id', 'id');
    // }

    // public function investigationWarrant()
    // {
    //     return $this->hasOne('App\Models\Letters\InvestigationWarrant\InvestigationWarrant', 'accident_id');
    // }

    public function suratPerintahPenyelidikanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPerintahPenyidikanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPerintahTugasDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function laporanHasilGelarPerkaraDocuments()
    {
        return $this->hasMany('App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'caseDegreeType',
                'attachment'
            ]);
    }

    public function suratKetetapanTentangPenetapanTersangkaDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPemberitahuanDimulainyaPenyidikanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPemberitahuanPerkembanganHasilPenyidikanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPemberitahuanPerkembanganHasilPenyidikanDocument\SuratPemberitahuanPerkembanganHasilPenyidikanDocument', 'accident_id', 'id');
    }

    public function suspect()
    {
        return $this->hasMany(Suspect::class, 'accident_id', 'id');
    }

    public function suspects()
    {
        return $this->hasMany(Suspect::class, 'accident_id', 'id');
    }

    public function reportedPersons()
    {
        return $this->hasMany(ReportedPerson::class, 'accident_id', 'id');
    }

    public function caseVehicle()
    {
        return $this->hasMany('App\Models\CaseVehicle', 'accident_id', 'id');
    }
}
