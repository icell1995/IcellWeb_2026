<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lib\Role;
use App\Models\User;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';

	protected $fillable = [
		// 'code',
		'name',
		'state',
		// 'feature'
	];


	// public function users(){
	// 	return $this->belongsTo(User::class);
	// }

	public function roles()
    {   
        return $this->belongsToMany('App\Models\Lib\Role','permission_role');
    }

	

	// Relation
	// public function role()
	// {
	// 	return $this->belongsToMany('App\Models\Lib\Role', 'role_permissions', 'permission_id', 'role_id')
	// 			    ->withPivot(
	// 			    	'have_access',
	// 			    )->withTimestamps();
	// }
}
