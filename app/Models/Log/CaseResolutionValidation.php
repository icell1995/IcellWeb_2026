<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User; // ⬅️ tambah ini

class CaseResolutionValidation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table   = 'log_case_resolution_validations';
    protected $guarded = ['id'];

    // Cast waktu → Carbon
    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /* -------------------- Hooks (auto set *_by_name) -------------------- */
    protected static function booted()
    {
        static::creating(function (self $m) {
            // Isi approved_by_name jika ada approved_by_id tapi name kosong
            if ($m->approved_by_id && empty($m->approved_by_name)) {
                $u = User::withTrashed()->find($m->approved_by_id);
                $m->approved_by_name = $u->name
                    ?? $u->full_name
                    ?? $u->username
                    ?? $u->email
                    ?? 'Unknown';
            }

            // Isi rejected_by_name jika ada rejected_by_id tapi name kosong
            if ($m->rejected_by_id && empty($m->rejected_by_name)) {
                $u = User::withTrashed()->find($m->rejected_by_id);
                $m->rejected_by_name = $u->name
                    ?? $u->full_name
                    ?? $u->username
                    ?? $u->email
                    ?? 'Unknown';
            }
        });
    }

    /* -------------------- Relasi -------------------- */
    public function accident()
    {
        return $this->belongsTo(\App\Models\Accident::class, 'accident_id', 'id');
    }

    public function updatedStatus()
    {
        return $this->belongsTo(\App\Models\Opt\Status::class, 'updated_status_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_id', 'id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'rejected_by_id', 'id');
    }

    /* -------------------- Scopes ringkas -------------------- */
    public function scopeApproved($q) { return $q->whereNotNull('approved_at'); }
    public function scopeRejected($q) { return $q->whereNotNull('rejected_at'); }

    /* -------------------- Accessor status -------------------- */
    public function getStatusNameAttribute(): string
    {
        if ($this->approved_at) return 'approved';
        if ($this->rejected_at) return 'rejected';
        return 'pending';
    }
}
