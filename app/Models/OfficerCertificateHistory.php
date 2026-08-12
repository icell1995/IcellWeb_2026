<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerCertificateHistory extends Model
{
    use HasFactory;

    protected $table = 'officer_certificate_histories';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function scopeWithRelated($query)
    {
        return $query->with([
            'certificateType',
        ]);
    }

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'id');
    }

    public function certificateType()
    {
        return $this->belongsTo('App\Models\Lib\CertificateType', 'certificate_type_id', 'id');
    }
}
