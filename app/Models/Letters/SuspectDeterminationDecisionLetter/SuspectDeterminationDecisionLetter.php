<?php

namespace App\Models\Letters\SuspectDeterminationDecisionLetter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid; 

class SuspectDeterminationDecisionLetter extends Model
{
    use HasFactory;

    protected $table = 'suspect_determination_decision_letters';

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

    public function accident(){
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id');
    }

    public function authorizedSignatory()
    {
        return $this->belongsTo('App\Models\Peoples\AuthorizedSignatory', 'signing_officials', 'id');
    }
}
