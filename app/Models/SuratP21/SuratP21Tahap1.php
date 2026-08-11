<?php

namespace App\Models\SuratP21;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Uuid;

class SuratP21Tahap1 extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'surat_p21_tahap_1';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

}
