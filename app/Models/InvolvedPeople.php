<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvolvedPeople extends Model
{
    use HasFactory;

    protected $table = 'public.involved_peoples';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];



    public function scopeWithRelated($query)
    {
        return $query->with([
            'accident',
            'identityType',
            'education',
            'job',
            'gender',
            'religion',
            'maritalStatus',
            'country',
            'province',
            'regency',
            'district',
            'village', 
        ]);
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class);
    }

    public function identityType()
    {
        return $this->belongsTo('App\Models\Lib\IdentityType', 'identity_type_id', 'id');
    }

    public function education()
    {
        return $this->belongsTo('App\Models\Lib\Education', 'education_id', 'id');
    }

    public function job()
    {
        return $this->belongsTo('App\Models\Lib\Job', 'job_id', 'id');
    }
   
    public function gender()
    {
        return $this->belongsTo('App\Models\Lib\Gender', 'gender_id', 'id');
    }

    public function religion()
    {
        return $this->belongsTo('App\Models\Lib\Religion', 'religion_id', 'id');
    }

    public function maritalStatus()
    {
        return $this->belongsTo('App\Models\Lib\MaritalStatus', 'marital_status_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Lib\Location', 'country_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Lib\Location', 'province_id', 'id');
    }

    public function regency()
    {
        return $this->belongsTo('App\Models\Lib\Location', 'regency_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo('App\Models\Lib\Location', 'district_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo('App\Models\Lib\Location', 'village_id', 'id');
    }
}
