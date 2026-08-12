<?php

namespace App\Models\Doc\PerpanjanganLanjutanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerpanjanganLanjutanDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.perpanjangan_lanjutan_document_attachments';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'type' => [
                'DOCUMENT' => 'DOCUMENT',
                'IMAGE' => 'IMAGE',
                'VIDEO' => 'VIDEO',
                'AUDIO' => 'AUDIO',
            ],
        ];

        if ($columnKey !== null && $enumPropKey !== null) {
            if (isset($enumOptions[$columnKey]) && isset($enumOptions[$columnKey][$enumPropKey])) {
                return $enumOptions[$columnKey][$enumPropKey];
            }

            return null;
        }

        return null;
    }

    public function perpanjanganLanjutanDocument()
    {
        return $this->belongsTo(
            PerpanjanganLanjutanDocument::class,
            'perpanjangan_lanjutan_document_id',
            'id'
        );
    }
}
