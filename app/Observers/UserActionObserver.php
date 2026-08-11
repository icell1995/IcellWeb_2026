<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Models\Accident;

class UserActionObserver
{
    private $user;

    public function __construct()
    {
        $id = Auth::user()->id ?? null;
        // $firstTitle = Auth::user()->first_title ?? null;
        // $firstName = Auth::user()->first_name ?? '-';
        // $lastName = Auth::user()->last_name ?? null;
        // $lastTitle = Auth::user()->last_title ?? null;

        // $fullName = PeopleNameHelper::getFullName($firstTitle, $firstName, $lastName, $lastTitle);
        // $registerNumber = (isset(Auth::user()->register_number)) ? Auth::user()->register_number : ((isset(Auth::user()->officer_id)) ? Auth::user()->officer_id : '-');
        // $rankName = (isset(Auth::user()->rank->name)) ? Auth::user()->rank->name : ((isset(Auth::user()->pangkat)) ? Auth::user()->pangkat : '-');

        $this->user = $id;
    }

    public function creating($model)
    {
        if (Auth::check()) {
            $model->created_by_user_id = $this->user;

            $currentIpAddresses = $model->ip_addresses ?? [];
            $currentIpAddresses['created_ip'] = request()->ip();
            $model->ip_addresses = $currentIpAddresses;
        }
    }

    public function updating($model)
    {
        if (Auth::check()) {
            $model->updated_by_user_id = $this->user;

            $currentIpAddresses = $model->ip_addresses ?? [];
            $currentIpAddresses['updated_ip'] = request()->ip();
            $model->ip_addresses = $currentIpAddresses;
        }
    }

    public function deleting($model)
    {
        if (Auth::check()) {
            $model->deleted_by_user_id = $this->user;

            $currentIpAddresses = $model->ip_addresses ?? [];
            $currentIpAddresses['deleted_ip'] = request()->ip();
            $model->ip_addresses = $currentIpAddresses;
            
            $model->save(); // Menyimpan perubahan sebelum penghapusan
        }
    }
}
