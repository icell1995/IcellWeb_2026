<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Polda;
use App\Models\Polres;
use App\Models\User;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'polda_id',
        'polres_id',
        'status',
        'assigned_to',
        'created_by',
        'deskripsi_permasalahan',
        'deskripsi_solusi',
        'kategori',
    ];

    /**
     * Generate a sequential ticket_number based on inserted id.
     * This uses the created event so we have the id. Format: TCK000001
     */
    protected static function booted()
    {
        static::created(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TCK' . str_pad($ticket->id, 6, '0', STR_PAD_LEFT);
                $ticket->saveQuietly();
            }
        });
    }

    // Relationships can be added later (User, Polda, Polres)
    public function polda()
    {
        return $this->belongsTo(Polda::class, 'polda_id', 'id');
    }

    public function polres()
    {
        return $this->belongsTo(Polres::class, 'polres_id', 'id');
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id')->selectFullNameExpression();
    }
}
