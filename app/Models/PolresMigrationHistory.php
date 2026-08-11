<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolresMigrationHistory extends Model
{
    protected $table = 'polres_migration_history';
        
    public $incrementing = false; // karena tidak pakai auto-increment
    public $timestamps = true;

    protected $primaryKey = null; // jika tidak ada primary key
    protected $keyType = 'string'; // untuk UUID atau string

    protected $fillable = [
        'old_police_id',
        'new_police_id',
        'old_polda_id',
        'new_polda_id',
    ];

    use HasFactory;
}
