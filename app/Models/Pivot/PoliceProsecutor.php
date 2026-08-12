<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PoliceProsecutor extends Pivot
{
    use HasFactory;

    protected $table = 'pivot.police_prosecutor';
    protected $fillable = [
        'police_id',
        'prosecutor_id',
    ];

    public function police()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id');
    }

    public function prosecutor()
    {
        return $this->belongsTo('App\Models\Lib\Prosecutor', 'prosecutor_id', 'id');
    }
}
