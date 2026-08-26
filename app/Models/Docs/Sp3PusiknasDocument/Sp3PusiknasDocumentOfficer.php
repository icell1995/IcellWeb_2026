<?php

namespace App\Models\Docs\Sp3PusiknasDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Sp3PusiknasDocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.sp3_pusiknas_document_officers';

    protected $fillable = [
        'sp3_pusiknas_document_id',
        'sort',
        'register_number',
        'first_title',
        'first_name',
        'last_name',
        'last_title',
        'rank_id',
        'position_id',
        'phone_number',
        'email',
        'information',
        'police_id',
        'status',
        'class',
        'flag',
        'insert_method',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->sort = self::where('sp3_pusiknas_document_id', $model->sp3_pusiknas_document_id)->max('sort') + 1;
        });
    }

    public function sp3PusiknasDocument()
    {
        return $this->belongsTo(Sp3PusiknasDocument::class, 'sp3_pusiknas_document_id');
    }

    public function rank()
    {
        return $this->belongsTo(\App\Models\Lib\Rank::class, 'rank_id');
    }

    public function position()
    {
        return $this->belongsTo(\App\Models\Lib\Position::class, 'position_id');
    }

    public function police()
    {
        return $this->belongsTo(\App\Models\Lib\Police::class, 'police_id');
    }
}
