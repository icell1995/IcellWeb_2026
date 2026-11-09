<?php

namespace App\Models\Doc\SuratPanggilanTersangkaDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Accident;
use Uuid;

class SuratPanggilanTersangkaDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_panggilan_tersangka_documents';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'document_date' => 'date',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
        });
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id');
    }
}
