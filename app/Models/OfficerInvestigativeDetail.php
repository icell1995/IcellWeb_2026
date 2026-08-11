<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerInvestigativeDetail extends Model
{
    use HasFactory;

    protected $table = 'officer_investigative_details';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'id');
    }
}
