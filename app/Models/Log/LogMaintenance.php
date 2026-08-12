<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LogMaintenance extends Model
{
    use HasFactory;

    protected $table = 'log_maintenances';

    protected $fillable = [
        'action',
        'duration_minutes',
        'secret',
        'ip_address',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
