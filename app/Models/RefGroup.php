<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefGroup extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $table = 'ref_grp';

    public function references() {

        return $this->hasMany('App\Models\Ref', 'grp_id');
    }
}
