<?php

namespace App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentOfficer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.berita_acara_serah_terima_tersangka_officers';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'doc_id' => 'string',
        'officer_id' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'first_title' => 'string',
        'last_title' => 'string',
        'register_number' => 'string',
        'position_id' => 'string',
        'rank_id' => 'string',
        'police_id' => 'string',
        'class' => 'string',
        'status_id' => 'string',
        'order_number' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
        });
    }

    public function document()
    {
        return $this->belongsTo('App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument', 'doc_id', 'id');
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
