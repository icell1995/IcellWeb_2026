<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiIrsmsKorlantasPostStgDorsAccident extends Model
{
    use HasFactory;

    protected $table = 'public.log_api_irsms_korlantas_post_stg_dors_accidents';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'data' => 'json',
    ];
}
