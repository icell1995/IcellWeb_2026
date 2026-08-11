<?php

namespace App\Models\SuratP21;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Uuid;

class SuratP21Tahap2 extends Model
{
    use HasFactory;

    protected $table = 'surat_p21_tahap_2';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $guarded = [
        'id'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
