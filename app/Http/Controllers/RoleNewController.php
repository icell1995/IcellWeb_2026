<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleNewController extends Controller
{
    protected $_title = 'Role New';

    public function __construct()
    {
        view()->share('_title', $this->_title);
    }

    public function index()
    {
        $rolesData = \Illuminate\Support\Facades\DB::table('lib.roles')->orderBy('level', 'asc')->get();
        // Format to match view expecting array style
        $data = [];
        foreach ($rolesData as $role) {
            $userCount = 0;
            try {
                $userCount = \Illuminate\Support\Facades\DB::table('users')->where('role_id', $role->id)->count();
            } catch (\Exception $e) {}

            $data[] = [
                'id' => $role->id,
                'level' => $role->level,
                'name' => $role->name,
                'description' => $role->description,
                'user_count' => $userCount
            ];
        }

        return view('role-new.index', compact('data'));
    }

    public function add()
    {
        return view('role-new.add');
    }

    public function store(Request $request) 
    {
        $level = (int) $request->level; // Cast ke integer agar cocok dengan tipe kolom PostgreSQL

        // Validasi: level tidak boleh duplikat
        $levelExists = \Illuminate\Support\Facades\DB::table('lib.roles')
            ->where('level', $level)
            ->exists();

        if ($levelExists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['level' => 'Level ' . $level . ' sudah digunakan oleh role lain. Gunakan level yang berbeda.']);
        }

        // Validasi: name tidak boleh duplikat
        $nameExists = \Illuminate\Support\Facades\DB::table('lib.roles')
            ->where('name', $request->name)
            ->exists();

        if ($nameExists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'Nama role "' . $request->name . '" sudah digunakan. Gunakan nama yang berbeda.']);
        }

        // Temukan ID terbaru dan tambahkan 1 (manual auto-increment)
        $newId = \Illuminate\Support\Facades\DB::table('lib.roles')->max('id') + 1;

        \Illuminate\Support\Facades\DB::table('lib.roles')->insert([
            'id' => $newId,
            'name' => $request->name,
            'level' => $level,
            'description' => $request->description,
            'state' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if($request->has('permissions')) {
            $perms = \Illuminate\Support\Facades\DB::table('permissions')
                ->whereIn('name', $request->permissions)
                ->pluck('id');
            
            $pivotData = [];
            foreach($perms as $pId) {
                $pivotData[] = ['role_id' => $newId, 'permission_id' => $pId];
            }
            \Illuminate\Support\Facades\DB::table('permission_role')->insert($pivotData);
        }

        return redirect()->route('role-new')->with('success', 'Role berhasil ditambahkan');
    }

    public function edit($id)
    {
        $roleObj = \Illuminate\Support\Facades\DB::table('lib.roles')->where('id', $id)->first();
        if(!$roleObj) return abort(404);

        // Role level 2 tidak dapat diedit — redirect ke halaman view
        if((int)$roleObj->level === 2) {
            return redirect()->route('role-new-view', $id);
        }

        $role = [
            'id' => $roleObj->id,
            'level' => $roleObj->level,
            'name' => $roleObj->name,
            'description' => $roleObj->description,
        ];

        // Fetch attached permissions for this role
        $rolePermissions = \Illuminate\Support\Facades\DB::table('permission_role')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('permission_role.role_id', $id)
            ->pluck('permissions.name')
            ->toArray();

        return view('role-new.edit', compact('role', 'rolePermissions'));
    }

    public function view($id)
    {
        $roleObj = \Illuminate\Support\Facades\DB::table('lib.roles')->where('id', $id)->first();
        if(!$roleObj) return abort(404);

        $role = [
            'id' => $roleObj->id,
            'level' => $roleObj->level,
            'name' => $roleObj->name,
            'description' => $roleObj->description,
        ];

        // Ambil permission yang dimiliki role ini
        $rolePermissions = \Illuminate\Support\Facades\DB::table('permission_role')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('permission_role.role_id', $id)
            ->pluck('permissions.name')
            ->toArray();

        return view('role-new.view', compact('role', 'rolePermissions'));
    }

    public function update(Request $request, $id) 
    {
        $role = \Illuminate\Support\Facades\DB::table('lib.roles')->where('id', $id)->first();
        if(!$role) return redirect()->back();

        // Role level 2 tidak dapat diperbarui
        if((int)$role->level === 2) {
            return redirect()->route('role-new-view', $id)->with('error', 'Role level 2 tidak dapat dimodifikasi.');
        }

        $level = (int) $request->level; // Cast ke integer agar cocok dengan tipe kolom PostgreSQL

        // Validasi: level tidak boleh duplikat (kecuali diri sendiri)
        $levelExists = \Illuminate\Support\Facades\DB::table('lib.roles')
            ->where('level', $level)
            ->where('id', '!=', $id)
            ->exists();

        if ($levelExists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['level' => 'Level ' . $level . ' sudah digunakan oleh role lain. Gunakan level yang berbeda.']);
        }

        // Validasi: name tidak boleh duplikat (kecuali diri sendiri)
        $nameExists = \Illuminate\Support\Facades\DB::table('lib.roles')
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($nameExists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'Nama role "' . $request->name . '" sudah digunakan. Gunakan nama yang berbeda.']);
        }

        \Illuminate\Support\Facades\DB::table('lib.roles')->where('id', $id)->update([
            'name' => $request->name,
            'level' => $request->level,
            'description' => $request->description,
            'updated_at' => now(),
        ]);

        // Sync permissions
        \Illuminate\Support\Facades\DB::table('permission_role')->where('role_id', $id)->delete();
        
        if($request->has('permissions')) {
            $perms = \Illuminate\Support\Facades\DB::table('permissions')
                ->whereIn('name', $request->permissions)
                ->pluck('id');
            
            $pivotData = [];
            foreach($perms as $pId) {
                $pivotData[] = ['role_id' => $id, 'permission_id' => $pId];
            }
            \Illuminate\Support\Facades\DB::table('permission_role')->insert($pivotData);
        }

        return redirect()->route('role-new')->with('success', 'Role berhasil diperbarui');
    }
}
