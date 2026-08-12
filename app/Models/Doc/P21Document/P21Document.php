<?php

namespace App\Models\Doc\P21Document;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class P21Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table="doc.p21_documents";

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

     // cast
    protected $casts = [
        'id' => 'string',
        'messages' => 'json',
        'timestamps' => 'json',
        'ip_addresses' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);
     
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
            $model->status_id = '2';
            $model->document_category_id = '0804';
        });

        self::created(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => '',
                'tipe_update' => 'MEMBUAT',
            ]);
        });

        self::updated(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => '',
                'tipe_update' => 'MENGUBAH',
            ]);
        });

        self::deleted(function ($model) {
            $accident = $model->accident;
            $accident->update([
                'last_update' => Carbon::now(),
                'category' => '',
                'tipe_update' => 'MENGHAPUS',
            ]);
        });
    }

    public function scopeWithRelated($query){
        return $query->with([
            'accident',
            'documentCategory',
            'createdByUser',
            'updatedByUser',
            'deletedByUser',
            'status',
        ]);
    }

    public function documentCategory(){
        return $this->belongsTo('App\Models\Lib\DocumentCategory', 'document_category_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id')->with(['police']);
    }

    public function createdByUser()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo('App\Models\User', 'updated_by_user_id', 'id');
    }

    public function deletedByUser()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by_user_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Opt\Status', 'status_id', 'id');
    }
}
