<?php

namespace App\Services\Regional;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RegionalScopeService
{
    /**
     * Kode-kode Polda yang dikecualikan secara sistem dari daftar umum.
     */
    public const EXCLUDE_POLDA_IDS = ['90', '99', '80'];

    /**
     * Dapatkan daftar Polda dan Polres sesuai batasan hak akses (scope) pengguna.
     *
     * @param mixed $user Model User yang sedang login
     * @return array [
     *   'poldas' => Collection,
     *   'polress' => Collection,
     *   'locked' => array,
     *   'roleId' => int
     * ]
     */
    public static function getScopedPoldaAndPolres($user): array
    {
        $roleId       = (int) ($user->role_id ?? 0);
        $userPoldaId  = $user->polda_id ?? null;
        $userPolresId = $user->polres_id ?? null;

        $lockPolres = !empty($userPolresId);
        $lockPolda  = !$lockPolres && !empty($userPoldaId);

        if ($lockPolres) {
            $poldas = DB::table('polda')
                ->select('id', 'name')
                ->whereNotIn('id', self::EXCLUDE_POLDA_IDS)
                ->where('id', $userPoldaId)
                ->get();

            $polress = DB::table('polres')
                ->select('id', 'name')
                ->where('state', '<>', 0)
                ->where('id', $userPolresId)
                ->get();
        } elseif ($lockPolda) {
            $poldas = DB::table('polda')
                ->select('id', 'name')
                ->whereNotIn('id', self::EXCLUDE_POLDA_IDS)
                ->where('id', $userPoldaId)
                ->get();

            $polress = DB::table('polres')
                ->select('id', 'name')
                ->where('state', '<>', 0)
                ->where('polda_id', $userPoldaId)
                ->orderBy('name')
                ->get();
        } else {
            $poldas = DB::table('polda')
                ->select('id', 'name')
                ->whereNotIn('id', self::EXCLUDE_POLDA_IDS)
                ->orderBy('name')
                ->get();

            $polress = collect([]);
        }

        $locked = [
            'is_lock_polres' => $lockPolres,
            'is_lock_polda'  => $lockPolda,
            'polda_id'       => $userPoldaId,
            'polres_id'      => $userPolresId,
            'role_id'        => $roleId,
        ];

        return [
            'poldas'  => $poldas,
            'polress' => $polress,
            'locked'  => $locked,
            'roleId'  => $roleId,
        ];
    }

    /**
     * Dapatkan daftar seluruh Polda (non-excluded).
     */
    public static function getPoldas(): Collection
    {
        return DB::table('polda')
            ->select('id', 'name')
            ->whereNotIn('id', self::EXCLUDE_POLDA_IDS)
            ->orderBy('name')
            ->get();
    }

    /**
     * Dapatkan daftar Polres aktif berdasarkan Polda.
     */
    public static function getPolresByPolda(string|int $poldaId): Collection
    {
        if (empty($poldaId) || $poldaId === '-') {
            return collect([]);
        }

        return DB::table('polres')
            ->select('id', 'name')
            ->where('state', '<>', 0)
            ->where('polda_id', (string) $poldaId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Validasi apakah Polres benar-benar bagian dari Polda yang bersangkutan.
     */
    public static function validateCombo(string|int $poldaId, string|int $polresId): bool
    {
        if (empty($poldaId) || empty($polresId) || $poldaId === '-' || $polresId === '-') {
            return true;
        }

        return DB::table('polres')
            ->where('id', (string) $polresId)
            ->where('polda_id', (string) $poldaId)
            ->exists();
    }
}
