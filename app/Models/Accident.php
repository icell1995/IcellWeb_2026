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

    public function suratPermintaanIzinPenyitaanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratLaporanPersetujuanPenyitaanDocuments()
    {
        return $this->hasMany(
            \App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument\SuratLaporanPersetujuanPenyitaanDocument::class,
            'accident_id',
            'id'
        );
    }

    public function suratPemberitahuanPerkembanganHasilPenyidikanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPemberitahuanPerkembanganHasilPenyidikanDocument\SuratPemberitahuanPerkembanganHasilPenyidikanDocument', 'accident_id', 'id');
    }

    public function suratPermohonanPenetapanDiversiDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPermohonanPenetapanDiversiDocument\SuratPermohonanPenetapanDiversiDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPemberitahuanUpayaDiversiDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPemberitahuanUpayaDiversiDocument\SuratPemberitahuanUpayaDiversiDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'suspect',
                'attachment',
                'createdByUser',
            ]);
    }

    public function beritaAcaraPenahananDocuments()
    {
        return $this->hasMany('App\Models\BeritaAcaraPenahanan', 'accident_id', 'id')
            ->with(['documentCategory', 'attachment']);
    }

    public function beritaAcaraPenahanans()
    {
        return $this->beritaAcaraPenahananDocuments();
    }

    public function suratPermintaanPenggeledahanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPermintaanPenggeledahanDocument\SuratPermintaanPenggeledahanDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratGunaMemperolehPersetujuanPenggeledahanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratGunaMemperolehPersetujuanPenggeledahanDocument\SuratGunaMemperolehPersetujuanPenggeledahanDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPerintahPenahananDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPerintahPenangguhanPenahananDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahPenangguhanPenahananDocument\SuratPerintahPenangguhanPenahananDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPerintahPencabutanPenangguhanPenahananDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahPencabutanPenangguhanPenahananDocument\SuratPerintahPencabutanPenangguhanPenahananDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPermohonanPerpanjanganPenahananKejaksaanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment'
            ]);
    }

    public function suratPermintaanPerpanjanganPenahananLanjutanDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument\SuratPermintaanPerpanjanganPenahananLanjutanDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment',
                'createdByUser',
            ]);
    }

    public function suratPermintaanPerpanjanganPenahananLanjutanKeduaDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment',
                'createdByUser',
            ]);
    }

    public function suratPerintahPembantaranPenahananDocuments()
    {
        return $this->hasMany('App\Models\Doc\SuratPerintahPembantaranPenahananDocument\SuratPerintahPembantaranPenahananDocument', 'accident_id', 'id')
            ->with([
                'documentCategory',
                'attachment',
                'createdByUser',
            ]);
    }

    public function suspect()
    {
        return $this->hasMany(Suspect::class, 'accident_id', 'id');
    }

    public function suratPerintahPenghentianPenyidikanDocuments()
    {
        return $this->hasMany(\App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocument::class, 'accident_id', 'id')->where('is_active', true);
    }

    public function suratKetetapanPenghentianPenyidikanDocuments()
    {
        return $this->hasMany(\App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument::class, 'accident_id', 'id')->where('is_active', true);
    }

    public function suratPemberitahuanPenghentianPenyidikanDocuments()
    {
        return $this->hasMany(\App\Models\Doc\SuratPemberitahuanPenghentianPenyidikanDocument\SuratPemberitahuanPenghentianPenyidikanDocument::class, 'accident_id', 'id');
    }

    public function tahap1Documents()
    {
        return $this->hasMany(\App\Models\Doc\Tahap1Document\Tahap1Document::class)->where('is_active', true)->where('is_legacy', false);
    }

    public function tahap2Documents()
    {
        return $this->hasMany(\App\Models\Doc\Tahap2Document\Tahap2Document::class)->where('is_active', true)->where('is_legacy', false);
    }

    public function suratPemberitahuanDimulainyaPenyidikanPusiknasDocuments()
    {
        return $this->hasMany(\App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::class)->where('is_active', true)->where('is_legacy', false);
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

    public function involvedPeoples()
    {
        return $this->hasMany(InvolvedPeople::class, 'accident_id', 'id');
    }
}
