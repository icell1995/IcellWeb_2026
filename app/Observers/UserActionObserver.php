<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class UserActionObserver
{
    private $user;

    /**
     * In-memory cache untuk mencegah query Schema::hasColumn berulang pada setiap penghapusan data.
     *
     * @var array<string, bool>
     */
    private static array $tableHasDeletedByCache = [];

    public function __construct()
    {
        $this->user = Auth::id();
    }

    public function creating($model): void
    {
        if (Auth::check()) {
            $userId = Auth::id() ?? $this->user;
            $model->created_by_user_id = $userId;

            if (array_key_exists('ip_addresses', $model->getCasts())) {
                $currentIpAddresses = $model->ip_addresses ?? [];
                $currentIpAddresses['created_ip'] = request()->ip();
                $model->ip_addresses = $currentIpAddresses;
            }
        }
    }

    public function updating($model): void
    {
        if (Auth::check()) {
            $userId = Auth::id() ?? $this->user;
            $model->updated_by_user_id = $userId;

            if (array_key_exists('ip_addresses', $model->getCasts())) {
                $currentIpAddresses = $model->ip_addresses ?? [];
                $currentIpAddresses['updated_ip'] = request()->ip();
                $model->ip_addresses = $currentIpAddresses;
            }
        }
    }

    public function deleting($model): void
    {
        if (Auth::check()) {
            $userId = Auth::id() ?? $this->user;

            try {
                $table = $model->getTable();
                if (!array_key_exists($table, self::$tableHasDeletedByCache)) {
                    self::$tableHasDeletedByCache[$table] = Schema::hasColumn($table, 'deleted_by_user_id');
                }

                if (self::$tableHasDeletedByCache[$table]) {
                    $model->deleted_by_user_id = $userId;

                    if (array_key_exists('ip_addresses', $model->getCasts())) {
                        $currentIpAddresses = $model->ip_addresses ?? [];
                        $currentIpAddresses['deleted_ip'] = request()->ip();
                        $model->ip_addresses = $currentIpAddresses;
                    }

                    $model->saveQuietly();
                }
            } catch (\Throwable) {
                // Abaikan jika kolom deleted_by_user_id tidak tersedia
            }
        }
    }
}
