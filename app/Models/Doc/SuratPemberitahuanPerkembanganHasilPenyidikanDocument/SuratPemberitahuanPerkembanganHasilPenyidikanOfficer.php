<?php

namespace App\Models\Doc\SuratPemberitahuanPerkembanganHasilPenyidikanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPemberitahuanPerkembanganHasilPenyidikanOfficer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_officers';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'sp2hp_document_id',
        'officer_id',
        'register_number',
        'name',
        'rank_id',
        'position_id',
        'phone_number',
        'email',
        'police_id',
        'sort_order',
        'class',
        'status',
        'flag',
        'insert_method',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Enum definitions
    protected static $enumOptions = [
        'class' => [
            'SIGNATORY' => 'SIGNATORY',
            'INVESTIGATOR' => 'INVESTIGATOR',
            'INVESTIGATOR_ASSISTANT' => 'INVESTIGATOR_ASSISTANT',
        ],
        'status' => [
            'PRESENT' => 'PRESENT',
            'ABSENT' => 'ABSENT',
        ],
        'flag' => [
            'INTERNAL' => 'INTERNAL',
            'EXTERNAL' => 'EXTERNAL',
        ],
        'insert_method' => [
            'IMPORT' => 'IMPORT',
            'MANUAL' => 'MANUAL',
        ],
    ];

    /**
     * Get enum option value
     */
    public static function getEnumOption($field, $key)
    {
        return self::$enumOptions[$field][$key] ?? null;
    }

    /**
     * Get all enum options for a field
     */
    public static function getEnumOptions($field)
    {
        return self::$enumOptions[$field] ?? [];
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

    /**
     * Relasi ke SP2HP Document
     */
    public function sp2hpDocument()
    {
        return $this->belongsTo(SuratPemberitahuanPerkembanganHasilPenyidikanDocument::class, 'sp2hp_document_id');
    }

    /**
     * Relasi ke Officer master
     */
    public function officer()
    {
        return $this->belongsTo('App\Models\Officer', 'officer_id');
    }

    /**
     * Relasi ke Rank master
     */
    public function rank()
    {
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id');
    }

    /**
     * Relasi ke Position master
     */
    public function position()
    {
        return $this->belongsTo('App\Models\Lib\Position', 'position_id');
    }

    /**
     * Relasi ke Police master
     */
    public function police()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id');
    }

    /**
     * Scope untuk sorting
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
