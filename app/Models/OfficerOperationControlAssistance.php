<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerOperationControlAssistance extends Model
{
    use HasFactory;

    protected $table = 'officer_operation_control_assistances';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'id');
    }

    public function originPolice()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'origin_police_id', 'id');
    }
}
