<?php

namespace App\Models\Doc\SuratPerintahPenghentianPenyelidikanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPerintahPenghentianPenyelidikanDocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_perintah_penghentian_penyelidikan_document_attachments';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'surat_perintah_penghentian_penyelidikan_document_id',
        'name',
        'file_path',
        'file_extension',
        'file_size',
        'is_active',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

    public function document()
    {
        return $this->belongsTo(SuratPerintahPenghentianPenyelidikanDocument::class, 'surat_perintah_penghentian_penyelidikan_document_id');
    }
}
