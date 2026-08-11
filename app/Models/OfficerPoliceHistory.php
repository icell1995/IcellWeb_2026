<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerPoliceHistory extends Model
{
    use HasFactory;

    protected $table = 'public.officer_police_histories';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'id');
    }

    public function police()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Lib\Position', 'position_id', 'id');
    }

    public function rank()
    {
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id', 'id');
    }
}
