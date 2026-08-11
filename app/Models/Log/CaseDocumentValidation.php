<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseDocumentValidation extends Model
{
    use HasFactory;

    protected $table = "log_case_document_validations";
    protected $guarded = ['id'];

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id');
    }

    public function documentCategory()
    {
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function updatedStatus()
    {
        return $this->belongsTo('App\Models\Opt\Status', 'status_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
   
    public function rejectedBy()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
