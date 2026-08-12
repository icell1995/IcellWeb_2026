<?php

namespace App\Models\Geography;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $table = 'province';
    protected $primaryKey = 'id';

    protected $guarded = [];
}
