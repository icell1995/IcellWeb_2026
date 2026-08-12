<?php

namespace App\Models;

use App\Observers\UserActionObserver;
use App\Helpers\PeopleNameHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Http\Middleware\AuthorityLevel;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "public.users";
    protected $primaryKey = "id";

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'timestamps' => 'json',
        'ip_addresses' => 'json',
        'properties' => 'json',
    ];

    public static function boot()
    {
        parent::boot();
    
        self::observe(UserActionObserver::class);
    }

    public function scopeWithRelated($query) {
        return $query->with([
            'officer',
            'createdByUser',
            'updatedByUser',
        ]);
    }

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function scopeSelectFullNameExpression($query) {
        return $query->select('*', PeopleNameHelper::getFullNameQueryExpression());
    }

    public function scopeIsAdminHeadquarter($query) {
        return $query->where('role_id', '1');
    }

    public function scopeIsAdminPolda($query) {
        return $query->where('role_id', '2');
    }

    public function scopeIsAdminPolice($query) {
        return $query->where('role_id', '3');
    }

    public function scopeIsSignatoryPolice($query) {
        return $query->where('role_id', '5');
    }

    public function scopeIsOfficerPolice($query) {
        return $query->where('role_id', '4');
    }

    public function scopeWhereHasOfficerActive($query) {
        return $query->whereHas('officer', function($query2){
            $query2->active();
        });
    }

    // public function roles(){
    //     return $this->hasOne('App\Models\Lib\Role');
    // }
    
    public function officer() {
        return $this->hasOne(Officer::class, 'user_id', 'id')
            ->selectFullName()
            ->withRelated();
    }

    public function rank() {
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id', 'id');
    }

    public function role() {
        return $this->belongsTo('App\Models\Lib\Role');
    }

    public function polda() {
        return $this->belongsTo(Polda::class);
    }

    public function polres() {
        return $this->belongsTo(Polres::class);
    }
    
    public function police() {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id');
    }

    protected $permissionCache = null; // Caching permissions for the current request to prevent N+1 Queries

    public function hasPermission($permission) {
        // Cache hasil query pertama ke properti agar request permission ke-2 dsb tidak menyebabkan query berulang
        if (is_null($this->permissionCache)) {
            // Ambil semua daftar text permission milik role ini lalu jadikan array biasa
            $this->permissionCache = \Illuminate\Support\Facades\DB::table('permission_role')
                ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
                ->where('permission_role.role_id', $this->role_id)
                ->pluck('permissions.name')
                ->toArray();
        }

        return in_array($permission, $this->permissionCache);
    }

    // public function roles()
    // {
    //     return $this->hasOne('App\Models\Lib\Role', 'id');
    // }

    // /**
    //  * Checks if User has access to $permissions.
    //  */
    // public function hasAccess(array $permissions) : bool
    // {
    //     // check if the permission is available in any role
    //     foreach ($this->roles as $role) {
    //         if($role->hasAccess($permissions)) {
    //             return true;
    //         }
    //     }
    //     return false;
    // }

    /**
     * Checks if the user belongs to role.
     */
    // public function inRole(string $roleSlug)
    // {
    //     return $this->roles()->where('slug', $roleSlug)->count() == 1;
    // }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
