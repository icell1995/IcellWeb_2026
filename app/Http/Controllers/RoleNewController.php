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
                $query = \Illuminate\Support\Facades\DB::table('users')
                    ->join('officers', 'users.id', '=', 'officers.user_id')
                    ->where('users.role_id', $role->id)
                    ->where('users.is_active', true)
                    ->where('officers.is_active', true);

                if ($role->id == 2) {
                    $query->whereNull('users.polda_id');
                }

                $userCount = $query->count();
            } catch (\Exception $e) {}

            $data[] = [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'user_count' => $userCount
            ];
        }

        return view('role-new.index', compact('data'));
    }

    public function add()
    {
        if (!auth()->user()->hasPermission('role.U')) {
            return redirect()->route('role-new')->with('error', 'Anda tidak memiliki akses untuk menambah role.');
        }
        return view('role-new.add');
    }

    public function store(Request $request) 
    {
        if (!auth()->user()->hasPermission('role.U')) {
            return redirect()->route('role-new')->with('error', 'Anda tidak memiliki akses untuk menambah role.');
        }

        // Validasi: name tidak boleh duplikat (case-insensitive)
        $nameExists = \Illuminate\Support\Facades\DB::table('lib.roles')
            ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
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
            'level' => $newId, // Level disamakan dengan ID
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
        if (!auth()->user()->hasPermission('role.U')) {
            return redirect()->route('role-new')->with('error', 'Anda tidak memiliki akses untuk mengubah role.');
        }

        $roleObj = \Illuminate\Support\Facades\DB::table('lib.roles')->where('id', $id)->first();
        if(!$roleObj) return abort(404);

        // Role level 2 tidak dapat diedit — redirect ke halaman view
        if((int)$roleObj->level === 2) {
            return redirect()->route('role-new-view', $id);
        }

        $role = [
            'id' => $roleObj->id,
            'level' => $roleObj->id, // Menggunakan ID sebagai level
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
            'level' => $roleObj->id, // Menggunakan ID sebagai level
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
        if (!auth()->user()->hasPermission('role.U')) {
            return redirect()->route('role-new')->with('error', 'Anda tidak memiliki akses untuk mengubah role.');
        }

        $role = \Illuminate\Support\Facades\DB::table('lib.roles')->where('id', $id)->first();
        if(!$role) return redirect()->back();

        // Role level 2 tidak dapat diperbarui
        if((int)$role->level === 2) {
            return redirect()->route('role-new-view', $id)->with('error', 'Role level 2 tidak dapat dimodifikasi.');
        }

        // Validasi: name tidak boleh duplikat (kecuali diri sendiri - case-insensitive)
        $nameExists = \Illuminate\Support\Facades\DB::table('lib.roles')
            ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->where('id', '!=', $id)
            ->exists();

        if ($nameExists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'Nama role "' . $request->name . '" sudah digunakan. Gunakan nama yang berbeda.']);
        }

        \Illuminate\Support\Facades\DB::table('lib.roles')->where('id', $id)->update([
            'name' => $request->name,
            'level' => $id, // Selalu set level = id agar tetap sinkron
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

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('role.U')) {
            return redirect()->route('role-new')->with('error', 'Anda tidak memiliki akses untuk menghapus role.');
        }

        // Jangan izinkan hapus role default 1-5
        if (in_array((int)$id, [1, 2, 3, 4, 5])) {
            return redirect()->route('role-new')->with('error', 'Role default tidak boleh dihapus.');
        }

        // Cek apakah masih digunakan oleh user
        $userCount = \Illuminate\Support\Facades\DB::table('users')->where('role_id', $id)->count();
        if ($userCount > 0) {
            return redirect()->route('role-new')->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh ' . $userCount . ' pengguna.');
        }

        // Gunakan transaction untuk memastikan integritas
        \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            // Hapus permission_role
            \Illuminate\Support\Facades\DB::table('permission_role')->where('role_id', $id)->delete();
            // Hapus dari lib.roles
            \Illuminate\Support\Facades\DB::table('lib.roles')->where('id', $id)->delete();
        });

        return redirect()->route('role-new')->with('success', 'Role berhasil dihapus');
    }
}
