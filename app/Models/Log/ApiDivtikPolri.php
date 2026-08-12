<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiDivtikPolri extends Model
{
    use HasFactory;

    protected $table = 'log_api_divtik_polri';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];
}
