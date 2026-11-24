<?php

namespace App\Models\Letters\InvestigationOrderLetter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationOrderLetterSignatoryOfficer extends Model
{
    use HasFactory;

    protected $table = 'legacy.investigation_order_letter_signatory_officer';
    protected $primaryKey = 'id';

    protected $guarded = [];

    public function investigationOrderLetter()
    {
        return $this->belongsTo(InvestigationOrderLetter::class, 'investigation_order_letter_id', 'id');
    }

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'id');
    }
}
