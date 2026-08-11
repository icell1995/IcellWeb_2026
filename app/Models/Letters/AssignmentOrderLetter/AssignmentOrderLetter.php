<?php

namespace App\Models\Letters\AssignmentOrderLetter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class AssignmentOrderLetter extends Model
{
    use HasFactory;

    protected $table = 'legacy.springas';
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

    public function officer(){
        return $this->belongsToMany('App\Models\Officer','legacy.officer_springas','sprint_gas_id','officer_id');
    }

    public function accident(){
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id');
    }

    public function authorizedSignatory()
    {
        return $this->belongsTo('App\Models\Peoples\AuthorizedSignatory', 'pejabat_penandatangan', 'id');
    }

    public function leaderOfficer()
    {
        return $this->belongsTo('App\Models\Officer', 'ketua_tim', 'id');
    }
}
