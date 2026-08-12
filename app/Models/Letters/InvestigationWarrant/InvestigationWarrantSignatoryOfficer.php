<?php

namespace App\Models\Letters\InvestigationWarrant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationWarrantSignatoryOfficer extends Model
{
    use HasFactory;

    protected $table = 'legacy.investigation_warrant_signatory_officer';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function investigationWarrant()
    {
        return $this->belongsTo(InvestigationWarrant::class, 'investigation_warrant_id', 'id');
    }

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'id');
    }
}
