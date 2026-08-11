<?php

namespace App\Models\Letters\InvestigationOrderLetter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationOrderLetterLaw extends Model
{
    use HasFactory;

    protected $table = 'legacy.investigation_order_letter_law';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function investigationOrderLetter()
    {
        return $this->belongsTo(InvestigationOrderLetter::class);
    }

    public function law()
    {
        return $this->belongsTo(Law::class);
    }
}
