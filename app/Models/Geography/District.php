<?php

namespace App\Models\Geography;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'district';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'regency_id',
        'state',
        'sort',
        'timezone',
    ];
}
