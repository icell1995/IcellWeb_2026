<?php

namespace App\Models\Doc\UndanganGelarPerkaraDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class UndanganGelarPerkaraDocumentOfficer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.undangan_gelar_perkara_officers';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id'              => 'string',
        'doc_id'          => 'string',
        'officer_id'      => 'string',
        'first_name'      => 'string',
        'last_name'       => 'string',
        'first_title'     => 'string',
        'last_title'      => 'string',
        'register_number' => 'string',
        'position_id'     => 'string',
        'rank_id'         => 'string',
        'police_id'       => 'string',
        'class'           => 'string',
        'status_id'       => 'string',
        'order_number'    => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
            if (auth()->check() && empty($model->created_by_user_id)) {
                $model->created_by_user_id = auth()->id();
            }
        });

        self::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by_user_id = auth()->id();
            }
        });

        self::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by_user_id = auth()->id();
                $model->save();
            }
        });
    }

    public function document()
    {
        return $this->belongsTo(UndanganGelarPerkaraDocument::class, 'doc_id', 'id');
    }

    public function officer()
    {
        return $this->belongsTo('App\Models\Officer', 'officer_id', 'id');
    }

    public function rank()
    {
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id', 'id');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Lib\Position', 'position_id', 'id');
    }

    public function police()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id');
    }

    public function getFullNameAttribute()
    {
        $fullName = '';
        if ($this->first_title) {
            $fullName .= $this->first_title . ' ';
        }
        $fullName .= $this->first_name;
        if ($this->last_name) {
            $fullName .= ' ' . $this->last_name;
        }
        if ($this->last_title) {
            $fullName .= ', ' . $this->last_title;
        }
        return trim($fullName);
    }
}
