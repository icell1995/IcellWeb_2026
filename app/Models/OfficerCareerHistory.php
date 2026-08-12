<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerCareerHistory extends Model
{
    use HasFactory;

    protected $table = 'officer_career_histories';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'id');
    }

    public function policeDivision()
    {
        return $this->belongsTo('App\Models\Lib\PoliceDivision', 'police_division_id', 'id');
    }
}
