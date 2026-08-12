<?php

namespace App\Models\Meta\Legals;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Law extends Model
{
    use HasFactory;

    protected $table = 'laws';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function investigationOrderLetters()
    {
        return $this->belongsToMany('App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter', 'legacy.investigation_order_letter_law', 'law_id', 'investigation_order_letter_id');
    }

    public function investigationWarrants()
    {
        return $this->belongsToMany('App\Models\Letters\InvestigationWarrant\InvestigationWarrant', 'legacy.investigation_warrant_law', 'law_id', 'investigation_warrant_id');
    }
}
