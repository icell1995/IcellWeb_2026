<?php

namespace App\Models\Letters\InvestigationWarrant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizedSignatoryInvestigationWarrant extends Model
{
    use HasFactory;

    protected $table = 'legacy.authorized_signatory_investigation_warrant';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function authorizedSignatory()
    {
        return $this->belongsTo('App\Models\Peoples\AuthorizedSignatory', 'authorized_signatory_id', 'id');
    }

    public function investigationWarrant()
    {
        return $this->belongsTo('App\Models\Letters\InvestigationWarrant\InvestigationWarrant', 'investigation_warrant_id', 'id');
    }
}
