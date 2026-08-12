<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventGallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'event_galleries';

    protected $guarded = ['id'];

    public function eventGalleryFiles(){
        return $this->hasMany(EventGalleryFile::class, 'event_gallery_id', 'id');
    }
}
