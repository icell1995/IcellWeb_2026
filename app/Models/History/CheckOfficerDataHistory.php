<?php

namespace App\Models\History;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckOfficerDataHistory extends Model
{
    use HasFactory;

    protected $table = 'history.check_officer_data_histories';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $casts = [
        'work_units' => 'json'
    ];

    public function createdByUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo('App\Models\User', 'updated_by_user_id', 'id');
    }
}
