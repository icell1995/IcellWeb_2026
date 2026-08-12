<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerPoliceDikjurEducation extends Model
{
    use HasFactory;

    protected $table = 'officer_police_dikjur_educations';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'id');
    }

    public function policeDikjurEducationMaterial()
    {
        return $this->belongsTo('App\Models\Lib\PoliceDikjurEducationMaterial', 'police_dikjur_education_material_id', 'id');
    }

    public function policeDikjurEducationPlace()
    {
        return $this->belongsTo('App\Models\Lib\PoliceDikjurEducationPlace', 'police_dikjur_education_place_id', 'id');
    }
}
