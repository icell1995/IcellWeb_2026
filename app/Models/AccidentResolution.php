<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class AccidentResolution extends Model
{
    use HasFactory;

    protected $table = 'accident_resolutions';
    protected $guarded = ['id'];

    protected $casts = [
        'date'        => 'date',
        'uploaded_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['file_url'];

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id', 'id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file) return null;
        if (filter_var($this->file, FILTER_VALIDATE_URL)) return $this->file;
        return \Illuminate\Support\Facades\Storage::url($this->file);
    }
}
