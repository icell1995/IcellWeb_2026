<?php

namespace App\Models\Dir;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostgresqlSavedQuery extends Model
{
    use HasFactory;

    protected $table = 'public.dir_postgresql_saved_queries';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];
}
