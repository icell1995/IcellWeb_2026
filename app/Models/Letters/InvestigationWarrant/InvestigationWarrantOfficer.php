<?php

namespace App\Models\Letters\InvestigationWarrant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationWarrantOfficer extends Model
{
    use HasFactory;

    protected $table = 'legacy.investigation_warrant_officer';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function investigationWarrant()
    {
        return $this->belongsTo('App\Models\Letters\InvestigationWarrant\InvestigationWarrant', 'investigation_warrant_id');
    }

    public function officer()
    {
        return $this->belongsTo('App\Models\Officer', 'officer_id');
    }
}
