<?php

namespace App\Models\History;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentApiSyncHistory extends Model
{
    use HasFactory;

    protected $table = 'history.document_api_sync_histories';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id');
    }
}
