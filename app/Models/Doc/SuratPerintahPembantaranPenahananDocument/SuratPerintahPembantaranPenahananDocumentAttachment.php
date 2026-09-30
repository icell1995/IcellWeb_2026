<?php

namespace App\Models\Doc\SuratPerintahPembantaranPenahananDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratPerintahPembantaranPenahananDocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_perintah_pembantaran_penahanan_document_attachments';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'ip_addresses' => 'json',
        'timestamps' => 'json',
    ];

    public function document()
    {
        return $this->belongsTo(SuratPerintahPembantaranPenahananDocument::class, 'document_id', 'id');
    }
}
