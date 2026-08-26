<?php

namespace App\Models\Docs\SpdpPusiknasDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lib\EnumBaseModel;

class SpdpPusiknasDocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.spdp_pusiknas_document_officers';

    protected $fillable = [
        'spdp_pusiknas_document_id',
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
            $model->sort = self::where('spdp_pusiknas_document_id', $model->spdp_pusiknas_document_id)->max('sort') + 1;
        });
    }

    public function spdpPusiknasDocument()
    {
        return $this->belongsTo(SpdpPusiknasDocument::class, 'spdp_pusiknas_document_id');
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
