<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleAssociatedSuspect extends Model
{
    use HasFactory;

    protected $table = 'vehicle_associated_suspects';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public function scopeWithRelated($query){
        return $query->with([
            'vehicleType',
            'identityType',
            'accidentType',
            'accidentCause',
            'drivingLicenseType',
        ]);
    }

    public function accident(){
        return $this->belongsTo(Accident::class, 'accident_id', 'id');
    }

    public function suspect(){
        return $this->belongsTo(Suspect::class, 'suspect_id', 'id');
    }

    public function accidentType(){
        return $this->belongsTo('App\Models\Lib\AccidentType', 'accident_type_id', 'id');
    }

    public function vehicleType(){
        return $this->belongsTo('App\Models\Lib\VehicleType', 'vehicle_type_id', 'id');
    }

    public function identityType(){
        return $this->belongsTo('App\Models\Lib\IdentityType', 'identity_type_id', 'id');
    }

    public function accidentCause(){
        return $this->belongsTo('App\Models\Lib\AccidentCause', 'accident_cause_id', 'id');
    }

    public function drivingLicenseType(){
        return $this->belongsTo('App\Models\Lib\DrivingLicenseType', 'driving_license_type_id', 'id');
    }
}
