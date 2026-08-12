<?php

namespace App\Models\Doc\Tahap1Document;

use App\Models\Officer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tahap1DocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.tahap_1_document_officers';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tahap_1_document_id',
        'officer_id',
        'register_number',
        'full_name',
        'rank',
        'position',
        'police_name',
        'class',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function document()
    {
        return $this->belongsTo(Tahap1Document::class, 'tahap_1_document_id');
    }

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id');
    }
}
