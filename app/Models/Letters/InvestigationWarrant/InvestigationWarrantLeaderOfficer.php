<?php

namespace App\Models\Letters\InvestigationWarrant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationWarrantLeaderOfficer extends Model
{
    use HasFactory;

    protected $table = 'legacy.investigation_warrant_leader_officer';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function investigationWarrant()
    {
        return $this->belongsTo('App\Models\Letters\InvestigationWarrant\InvestigationWarrant', 'investigation_warrant_id', 'id');
    }

    public function officer()
    {
        return $this->belongsTo('App\Models\Officer', 'officer_id', 'id');
    }
}
