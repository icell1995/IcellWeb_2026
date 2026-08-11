<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisGelarPerkara extends Model
{
    use HasFactory;

    protected $table='jenis_gelar_perkara';

    protected $fillable = [
        'id',
        'nama_permasalahan'
    ];
}
