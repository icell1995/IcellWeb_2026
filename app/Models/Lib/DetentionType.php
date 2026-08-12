<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetentionType extends Model
{
    use HasFactory;

    protected $table = 'lib.detention_type';

    protected $fillable = [
        'type_name',
    ];

}
