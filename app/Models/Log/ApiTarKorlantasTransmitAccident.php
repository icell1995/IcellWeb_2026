<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiTarKorlantasTransmitAccident extends Model
{
    use HasFactory;

    protected $table = 'log_api_tar_korlantas_transmit_accidents';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id');
    }
}
