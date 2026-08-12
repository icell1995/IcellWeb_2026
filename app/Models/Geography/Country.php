<?php

namespace App\Models\Geography;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $table = 'country';
    protected $primaryKey = 'id';

    protected $guarded = [];
}
