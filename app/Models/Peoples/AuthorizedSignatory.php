<?php

namespace App\Models\Peoples;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class AuthorizedSignatory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'authorized_signatories';
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

    // cast
    protected $casts = [
        'id' => 'string',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

    public function investigationOrderLetters()
    {
        return $this->belongsToMany('App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter', 'legacy.authorized_signatory_investigation_order_letter', 'authorized_signatory_id', 'investigation_order_letter_id');
    }

    public function investigationWarrants()
    {
        return $this->belongsToMany('App\Models\Letters\InvestigationWarrant\InvestigationWarrant', 'legacy.authorized_signatory_investigation_warrant', 'authorized_signatory_id', 'investigation_warrant_id');
    }

    public function polres()
    {
        return $this->belongsTo('App\Models\Polres', 'polres_id', 'id');
    }

}
