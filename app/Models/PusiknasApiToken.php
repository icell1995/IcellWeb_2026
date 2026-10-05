<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PusiknasApiToken extends Model
{
    use HasFactory;

    protected $table = 'pusiknas_api_tokens';

    protected $fillable = [
        'token',
        'username',
        'ip_address',
        'expires_at',
        'last_used_ip',
        'last_used_at',
    ];

    protected $casts = [
        'expires_at'   => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Scope query to only include active (not expired) tokens.
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>=', now());
    }

    /**
     * Determine if the token is still valid.
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture();
    }
}
