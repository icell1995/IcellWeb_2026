<?php

namespace App\Models\Letters\InvestigationWarrant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationWarrantLaw extends Model
{
    use HasFactory;

    protected $table = 'legacy.investigation_warrant_law';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function investigationWarrant()
    {
        return $this->belongsTo('App\Models\Letters\InvestigationWarrant\InvestigationWarrant', 'investigation_warrant_id', 'id');
    }

    public function law()
    {
        return $this->belongsTo('App\Models\Ref', 'law_id', 'id');
    }
}
