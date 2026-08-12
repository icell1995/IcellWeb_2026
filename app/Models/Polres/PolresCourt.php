<?php

namespace App\Models\Polres;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolresCourt extends Model
{
    use HasFactory;

    protected $table = 'polres_court';
    protected $primaryKey = 'id';

    protected $guarded = [];
}
