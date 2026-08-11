<?php

namespace App\Models\Letters\InvestigationOrderLetter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationOrderLetterDetail extends Model
{
    use HasFactory;

    protected $table = 'legacy.investigation_order_letter_details';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function investigationOrderLetter()
    {
        return $this->belongsTo('App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter', 'investigation_order_letter_id', 'id');
    }
}
