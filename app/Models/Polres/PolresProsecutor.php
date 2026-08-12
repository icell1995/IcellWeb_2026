<?php

namespace App\Models\Polres;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolresProsecutor extends Model
{
    use HasFactory;

    protected $table = 'polres_prosecutor';
    protected $primaryKey = 'id';
    
    protected $guarded = [];
}
