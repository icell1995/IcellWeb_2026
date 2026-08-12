<?php

namespace App\Models\Letters\InvestigationOrderLetter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizedSignatoryInvestigationOrderLetter extends Model
{
    use HasFactory;

    protected $table = 'legacy.authorized_signatory_investigation_order_letters';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function investigationOrderLetter()
    {
        return $this->belongsTo('App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter', 'investigation_order_letter_id');
    }

    public function authorizedSignatory()
    {
        return $this->belongsTo('App\Models\Peoples\AuthorizedSignatory', 'authorized_signatory_id');
    }
}
