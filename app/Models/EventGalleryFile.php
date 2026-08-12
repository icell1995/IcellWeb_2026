<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventGalleryFile extends Model
{
    use HasFactory;

    protected $table = 'event_gallery_files';
    
    protected $guarded = ['id'];

    public function eventGallery(){
        return $this->belongsTo(EventGallery::class, 'event_gallery_id', 'id');
    }
}
