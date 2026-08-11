<?php

namespace App\Models\Letters\InvestigationOrderLetter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class InvestigationOrderLetter extends Model
{
    use HasFactory;

    protected $table = 'legacy.investigation_order_letters';
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
        return $this->hasOne('App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetterDetail', 'investigation_order_letter_id', 'id');
    }

    public function officers()
    {
        return $this->belongsToMany('App\Models\Officer', 'legacy.investigation_order_letter_officer', 'investigation_order_letter_id', 'officer_id');
    }

    public function leaderOfficers()
    {
        return $this->belongsToMany('App\Models\Officer', 'legacy.investigation_order_letter_leader_officer', 'investigation_order_letter_id', 'officer_id');
    }

    // public function signatoryOfficer()
    // {
    //     return $this->belongsToMany('App\Models\Officer', 'legacy.investigation_order_letter_signatory_officer', 'investigation_order_letter_id', 'officer_id');
    // }

    public function authorizedSignatories()
    {
        return $this->belongsToMany('App\Models\Peoples\AuthorizedSignatory', 'legacy.authorized_signatory_investigation_order_letter', 'investigation_order_letter_id', 'authorized_signatory_id');
    }

    public function laws()
    {
        return $this->belongsToMany('App\Models\Meta\Legals\Law', 'legacy.investigation_order_letter_law', 'investigation_order_letter_id', 'law_id');
    }

    public function accident(){
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id');
    }
}
