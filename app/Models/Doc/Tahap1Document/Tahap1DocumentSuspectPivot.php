<?php

namespace App\Models\Doc\Tahap1Document;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Tahap1DocumentSuspectPivot extends Pivot
{
    use HasUuids;

    protected $table = 'pivot.tahap_1_document_suspect';
    public $incrementing = false;
    protected $keyType = 'string';
}
