<?php

namespace App\Models\Letters\InvestigationWarrant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class InvestigationWarrant extends Model
{
    use HasFactory;

    protected $table = 'legacy.investigation_warrants';
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

     // cast
    protected $casts = [
        'id' => 'string',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

    public function details()
    {
        return $this->hasOne('App\Models\Letters\InvestigationWarrant\InvestigationWarrantDetail', 'investigation_warrant_id', 'id');
    }

    public function officers()
    {
        return $this->belongsToMany('App\Models\Officer', 'legacy.investigation_warrant_officer', 'investigation_warrant_id', 'officer_id');
    }

    public function leaderOfficers()
    {
        return $this->belongsToMany('App\Models\Officer', 'legacy.investigation_warrant_leader_officer', 'investigation_warrant_id', 'officer_id');
    }

    // public function signatoryOfficer()
    // {
    //     return $this->belongsToMany('App\Models\Officer', 'legacy.investigation_warrant_signatory_officer', 'investigation_warrant_id', 'officer_id');
    // }

    public function authorizedSignatories()
    {
        return $this->belongsToMany('App\Models\Peoples\AuthorizedSignatory', 'legacy.authorized_signatory_investigation_warrant', 'investigation_warrant_id', 'authorized_signatory_id');
    }

    public function laws()
    {
        return $this->belongsToMany('App\Models\Meta\Legals\Law', 'legacy.investigation_warrant_law', 'investigation_warrant_id', 'law_id');
    }

    public function accident(){
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id');
    }
}
