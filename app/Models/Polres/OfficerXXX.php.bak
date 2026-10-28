<?php

namespace App\Models;

use App\Observers\UserActionObserver;
use App\Helpers\PeopleNameHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    use HasFactory;

    protected $table = 'public.officers';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'ip_addresses' => 'json',
    ];

    public static function boot()
    {
        parent::boot(); 
    
        self::observe(UserActionObserver::class);
    }

    public function scopeWithRelated($query)
    {
        return $query->with([
            'police',
            'rank',
            'position',
            'user',
            'identityType',
            'employmentType',
            'gender',
            'religion',
            'education',
            'officerInvestigativeDetail',
            'officerPoliceDikjurEducations',
            'officerCertificateHistories',
            'OfficerOperationControlAssistance',
            'officerCareerHistories',
        ]);
    }

    public function scopeSelectFullName($query)
    {
        return $query->select('*', PeopleNameHelper::getFullNameQueryExpression());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
  
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    public function scopeSignatory($query)
    {
        return $query->where('class', 'SIGNATORY');
    }

    public function scopeMember($query)
    {
        return $query->where('class', 'MEMBER');
    }

    public function scopeWhereHasUserActive($query)
    {
        return $query->whereHas('user', function($query2){
            $query2->active();
        });
    }

    public function police(){
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id')->with(['parent', 'children']);
    }

    public function rank(){
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id', 'id');
    }

    public function position(){
        return $this->belongsTo('App\Models\Lib\Position', 'position_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function identityType()
    {
        return $this->belongsTo('App\Models\Lib\IdentityType', 'identity_type_id', 'id');
    }

    public function employmentType()
    {
        return $this->belongsTo('App\Models\Lib\EmploymentType', 'employment_type_id', 'id');
    }

    public function gender()
    {
        return $this->belongsTo('App\Models\Lib\Gender', 'gender_id', 'id');
    }
    
    public function religion()
    {
        return $this->belongsTo('App\Models\Lib\Religion', 'religion_id', 'id');
    }
   
    public function education()
    {
        return $this->belongsTo('App\Models\Lib\Education', 'education_id', 'id');
    }
   
    public function policeDiktukEducation()
    {
        return $this->belongsTo('App\Models\Lib\PoliceDiktukEducation', 'police_diktuk_education_id', 'id');
    }

    public function officerInvestigativeDetail()
    {
        return $this->hasOne(OfficerInvestigativeDetail::class, 'officer_id', 'id');
    }

    public function officerPoliceDikjurEducations()
    {
        return $this->hasMany(OfficerPoliceDikjurEducation::class, 'officer_id', 'id')->with('policeDikjurEducationMaterial', 'policeDikjurEducationPlace');
    }

    public function officerCertificateHistories()
    {
        return $this->hasMany(OfficerCertificateHistory::class, 'officer_id', 'id');
    }

    public function OfficerOperationControlAssistance()
    {
        return $this->hasOne(OfficerOperationControlAssistance::class, 'officer_id', 'id')->with('originPolice');
    }

    public function officerCareerHistories()
    {
        return $this->hasMany(OfficerCareerHistory::class, 'officer_id', 'id')->with('policeDivision');
    }

    public function polres()
    {
        return $this->belongsTo(Polres::class);
    }

    public function polda()
    {
        return $this->belongsTo(Polda::class);
    }

    public function accident_tugas()
    {
        return $this->belongsToMany(Accident::class,'surat_tugas');
    }

    public function accident_penyidikan()
    {
        return $this->belongsToMany(Accident::class,'surat_penyidikan');
    }

    public function investigationOrderLetter(){
        return $this->belongsToMany('App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter', 'legacy.investigation_order_letter_officer', 'officer_id', 'investigation_order_letter_id');
    }

    public function investigationOrderLetterLeader()
    {
        return $this->belongsToMany('App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter', 'legacy.investigation_order_letter_leader_officer', 'officer_id', 'investigation_order_letter_id');
    }

    public function ranks()
    {
        return $this->belongsTo(Ref::class, 'rank_short_name', 'id');
    }

    public function investigationWarrant()
    {
        return $this->belongsToMany('App\Models\Letters\InvestigationWarrant\InvestigationWarrant', 'legacy.investigation_warrant_officer', 'officer_id', 'investigation_warrant_id');
    }

    public function investigationWarrantLeader()
    {
        return $this->belongsToMany('App\Models\Letters\InvestigationWarrant\InvestigationWarrant', 'legacy.investigation_warrant_leader_officer', 'officer_id', 'investigation_warrant_id');
    }

    public function assignmentOrderLetter()
    {
        return $this->belongsToMany('App\Models\Letters\AssignmentOrderLetter\AssignmentOrderLetter', 'legacy.officer_springas', 'officer_id', 'sprint_gas_id');
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
