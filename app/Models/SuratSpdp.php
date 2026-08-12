<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class SuratSpdp extends Model
{
    use HasFactory;

    // protected $table = 'surat_spdp';
    protected $table = 'spdpp';
    protected $keyType = 'uuid';

    protected $casts = ['id'=>'string'];
    protected $guarded = [];

    public function signatory()
    {
        return $this->hasOne('App\Models\Peoples\AuthorizedSignatory', 'id', 'latter_signature');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
