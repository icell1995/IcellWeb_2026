<?php

namespace App\Classes;

use App\Role;
use App\Permission;
use Illuminate\Support\Facades\Cache;
use Auth;
use Illuminate\Routing\Redirector;

class Authority {

    protected $_permissionModel;
	protected $_roleModel;
    protected $_authority;

    public function __construct()
	{
        $this->service = Auth::user();
        // dd(!$this->service);
        if (!$this->service) {
            return redirect('/login');
        }else{
            $this->_permissionModel = new Permission;
            $this->_roleModel       = new Role;
        }
	}

	public function init($code)
	{
        $this->_authority = [];
        $data = $this->getAuthority($code);

        if($data) {
        	$this->_authority = $data;

            session(['authority.'.$code => $this->_authority]);
        }

        return $this;
	}

	public function get($val = NULL)
    {
        if ($val) {
            return $this->_authority[$val];
        }

        return $this->_authority;
    }

    public function getByModule($code)
    {
        return $this->getAuthority($code);
    }

    public function check($method = NULL, $callback = false, $refData = [])
    {
        if(is_array($refData) && !count($refData))
            $refData = $this->_authority;

    	$access = $this->checkAuthority($method, $refData);

        if(! $access && $callback) {
            throw new \App\Exceptions\AuthorityException;

		}

		return $access;
    }

    public function checkByModule($module, $method = NULL, $callback = false)
    {
        $refData = $this->getAuthority($module);

        return $this->check($method, $callback, $refData);
    }

    public function checkSpecialAccess($accessCode)
    {
        $qUser   = Auth::user();
        $roleId  = $qUser->role_id;
        $fAccess = !empty($qUser->fungsi_akses) && is_array(json_decode($qUser->fungsi_akses, true)) ? json_decode($qUser->fungsi_akses, true) : [];

        $userAccess = [
            'V' => 'A0501',
            'E' => 'A0502',
        ];

        $userHaveAccess = false;
        $roleHaveAccess = false;

        if(!empty($userAccess[$accessCode]) && in_array($userAccess[$accessCode], $fAccess))
            $userHaveAccess = true;

        $access = $this->_roleModel->find($roleId)->special_access;

        if(!empty($access)) {
            $access = json_decode($access, true);
            $roleHaveAccess = in_array($accessCode, $access);
        }

        if($userHaveAccess || $roleHaveAccess)
            return true;

        return false;
    }

    private function getAuthority($code)
    {
        if(Auth::user())
        {
        $roleId = Sentinel::getUser()->role_id;

        $authority = NULL;

        if(Cache::has('authority.'.$code.'.'.$roleId)) {
            $authority = Cache::get('authority.'.$code.'.'.$roleId);
        } else {

            $authority = Cache::rememberForever('authority.'.$code.'.'.$roleId, function() use($code, $roleId)
            {
                $query = $this->_permissionModel->where('code', $code)->with(['role' => function($q) use($roleId) {
                    $q->where('id', $roleId);
                }])->has('role')->first();

                $data = NULL;

                if($query && count($query->role)) {

                    $haveAccess = $query->role[0]->pivot->have_access !== 0;

                    $data = [
                        'id'   => $query->id,
                        'code' => $query->code,
                        'name' => $query->name,
                        'have_access' => $haveAccess,
                        'read'        => $haveAccess ? $query->role[0]->pivot->authority_read : 0,
                        'create'      => $haveAccess ? $query->role[0]->pivot->authority_create : 0,
                        'update'      => $haveAccess ? $query->role[0]->pivot->authority_update : 0,
                        'delete'      => $haveAccess ? $query->role[0]->pivot->authority_delete : 0,
                        'import'      => $haveAccess ? $query->role[0]->pivot->authority_import : 0,
                        'export'      => $haveAccess ? $query->role[0]->pivot->authority_export : 0,
                        'download'    => $haveAccess ? $query->role[0]->pivot->authority_download : 0,
                        'upload'      => $haveAccess ? $query->role[0]->pivot->authority_upload : 0,
                        'data'        => $haveAccess ? $query->role[0]->pivot->authority_data : 0
                    ];
                }
                return $data;
            });
        }

        return $authority;
        }
        else{
            return redirect('login');
        }
    }

    private function checkAuthority($method = NULL, $refData)
    {
        $access = false;

        if(! $method)
            $method = 'have_access';

        if (count($refData)) {
            $access = isset($refData[$method]) ? ($method  == 'data' ? $refData[$method] : $refData[$method] == true) : false;
        }

        return $access;
    }

    public function forgetCache()
    {
        foreach ($this->_permissionModel->get() as $perm) {
            foreach ($this->_roleModel->get() as $role) {
                Cache::forget('authority.'.$perm->code.'.'.$role->id);
            }
        }
    }
}
