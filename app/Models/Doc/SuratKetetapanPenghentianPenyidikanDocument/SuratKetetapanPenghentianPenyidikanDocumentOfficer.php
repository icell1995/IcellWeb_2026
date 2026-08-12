<?php

namespace App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKetetapanPenghentianPenyidikanDocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_ketetapan_penghentian_penyidikan_document_officers';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'rank'              => 'array',
        'position'          => 'array',
        'headquarter_police'=> 'array',
    ];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'status' => [
                'PRESENT'  => 'PRESENT',
                'PAST'     => 'PAST',
                'EXTERNAL' => 'EXTERNAL',
            ],
            'class' => [
                'MEMBER'    => 'MEMBER',
                'LEADER'    => 'LEADER',
                'SIGNATORY' => 'SIGNATORY',
            ],
            'flag' => [
                'INTERNAL' => 'INTERNAL',
                'MOVED'    => 'MOVED',
                'EXTERNAL' => 'EXTERNAL',
            ],
            'insert_method' => [
                'MANUAL' => 'MANUAL',
                'IMPORT' => 'IMPORT',
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

    public function scopeWithRelated($query)
    {
        return $query->with([
            'suratKetetapanPenghentianPenyidikanDocument',
        ]);
    }

    /**
     * Relations
     */
    public function suratKetetapanPenghentianPenyidikanDocument()
    {
        return $this->belongsTo(
            'App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument',
            'surat_ketetapan_penghentian_penyidikan_document_id',
            'id'
        );
    }
}
