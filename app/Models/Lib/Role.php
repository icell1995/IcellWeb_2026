<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Permission;
use App\Models\User;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.roles';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $guarded= [];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function users()
    {   
        return $this->belongsTo(User::class,'id');
    }

    public function permissions()
    {   
        return $this->belongsToMany(Permission::class,'permission_role');
        // ->withPivot(['role_id', 'permission_id']);
    }

    public function allRolePermissions()
	{
		return $this->belongsToMany(Permission::class,'permission_role');
	}
    

  

    // public function users()
    // {   
      
    //     return $this->hasMany(User::class, 'role_id');
    // }

   





    ///////////////////////////

    // public function user()
    // {
    //     return $this->hasOne(App\Models\User::class, 'role_id');
    // }

    // public function permission()
    // {
    // 	return $this->belongsToMany(App\Models\Permission::class, 'role_permissions', 'role_id', 'permission_id')
	// 			    ->withPivot(
	// 			    	'have_access',
	// 			    )->withTimestamps();
    // }

    // public function hasAccess(array $permissions) : bool
    // {
    //     foreach ($permissions as $permission) {
    //         if ($this->hasPermission($permission))
    //             return true;
    //     }
    //     return false;
    // }

    // private function hasPermission(string $permission) : bool
    // {
    //     return $this->permissions[$permission] ?? false;
    // }

}
