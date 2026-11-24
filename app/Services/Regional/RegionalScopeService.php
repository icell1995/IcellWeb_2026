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

    /**
     * Validasi apakah pengguna yang sedang login ($actor) berhak mengakses/memodifikasi target user/officer ($target).
     *
     * @param mixed $actor Model User atau object pengguna/petugas yang sedang login
     * @param mixed $target Model User/Officer atau object pengguna/petugas yang dituju
     */
    public static function authorizeTargetUser(mixed $actor, mixed $target): bool
    {
        if (!$actor || !$target) {
            return false;
        }

        if ((int) $actor->role_id === 1) {
            return true;
        }

        // Non-Super-Admin tidak pernah boleh memodifikasi akun Super Admin (role_id = 1)
        if (isset($target->role_id) && (int) $target->role_id === 1) {
            return false;
        }

        $actorPolda = (string) ($actor->polda_id ?? '');
        $actorPolres = (string) ($actor->polres_id ?? '');
        $actorPolice = (string) ($actor->police_id ?? '');

        $targetPolda = (string) ($target->polda_id ?? '');
        $targetPolres = (string) ($target->polres_id ?? '');
        $targetPolice = (string) ($target->police_id ?? '');

        if ((int) $actor->role_id === 2) {
            if ($actorPolda !== '' && $targetPolda === $actorPolda) {
                return true;
            }
            if ($actorPolice !== '' && $targetPolice === $actorPolice) {
                return true;
            }
            return false;
        }

        if (in_array((int) $actor->role_id, [3, 5], true)) {
            if ($actorPolres !== '' && $actorPolres !== '0' && $targetPolres === $actorPolres) {
                return true;
            }
            if ($actorPolice !== '' && $targetPolice === $actorPolice) {
                return true;
            }
            if (($actorPolres === '' || $actorPolres === '0') && $actorPolda !== '' && $targetPolda === $actorPolda) {
                return true;
            }
            // Cek juga apakah target berada di satker anak dari satker actor
            if ($actorPolice !== '' && $targetPolice !== '') {
                try {
                    return DB::table('lib.polices')
                        ->where('id', $targetPolice)
                        ->where('parent_id', $actorPolice)
                        ->exists();
                } catch (\Throwable $e) {
                    return false;
                }
            }
            return false;
        }

        return isset($actor->id, $target->id) && (string) $actor->id === (string) $target->id;
    }

    /**
     * Validasi apakah $actor diizinkan menetapkan $newRoleId kepada pengguna lain.
     *
     * @param mixed $actor Model User atau object pengguna yang sedang login
     * @param int $newRoleId Role ID baru yang akan ditetapkan
     */
    public static function canAssignRole(mixed $actor, int $newRoleId): bool
    {
        if (!$actor) {
            return false;
        }

        $actorRole = (int) $actor->role_id;
        if ($actorRole === 1) {
            return true;
        }
        if ($actorRole === 2) {
            return $newRoleId >= 3;
        }
        if ($actorRole === 3) {
            return in_array($newRoleId, [3, 4, 5], true);
        }

        return false;
    }

    /**
     * Validasi apakah pengguna ($actor) berhak mengakses data atau dokumen perkara kecelakaan ($accident).
     *
     * @param mixed $actor Model User atau object pengguna yang sedang login
     * @param mixed $accident Model Accident atau object perkara kecelakaan
     */
    public static function authorizeAccident(mixed $actor, mixed $accident): bool
    {
        if (!$actor || !$accident) {
            return false;
        }

        $actorRole = (int) ($actor->role_id ?? 0);
        if ($actorRole === 1) {
            return true;
        }

        $actorPolda  = (string) ($actor->polda_id ?? '');
        $actorPolres = (string) ($actor->polres_id ?? '');
        $actorPolice = (string) ($actor->police_id ?? '');

        $targetPolres = (string) ($accident->polres_id ?? '');
        $targetPolice = (string) ($accident->police_id ?? '');
        $targetPolda  = (string) ($accident->polda_id ?? ($accident->polres->polda_id ?? ''));

        if ($targetPolda === '' && $targetPolres !== '') {
            try {
                $targetPolda = (string) (DB::table('polres')->where('id', $targetPolres)->value('polda_id') ?? '');
            } catch (\Throwable) {
                $targetPolda = '';
            }
        }

        // Level 2 (Polda): berhak mengakses seluruh perkara di bawah Poldanya
        if ($actorRole === 2) {
            if ($actorPolda !== '' && $targetPolda !== '' && $actorPolda === $targetPolda) {
                return true;
            }
            if ($actorPolice !== '' && $targetPolice !== '' && $actorPolice === $targetPolice) {
                return true;
            }
            return false;
        }

        // Level 3, 4, 5, 6 (Polres / Penyidik): berhak mengakses perkara di Polres/Satkernya
        if ($actorPolres !== '' && $actorPolres !== '0' && $targetPolres !== '' && $actorPolres === $targetPolres) {
            return true;
        }
        if ($actorPolice !== '' && $targetPolice !== '' && $actorPolice === $targetPolice) {
            return true;
        }
        if ($actorPolice !== '' && $targetPolres !== '' && $actorPolice === $targetPolres) {
            return true;
        }
        if (($actorPolres === '' || $actorPolres === '0') && $actorPolda !== '' && $targetPolda !== '' && $actorPolda === $targetPolda) {
            return true;
        }

        if ($actorPolice !== '' && ($targetPolice !== '' || $targetPolres !== '')) {
            $checkTarget = $targetPolice !== '' ? $targetPolice : $targetPolres;
            try {
                return DB::table('lib.polices')
                    ->where('id', $checkTarget)
                    ->where('parent_id', $actorPolice)
                    ->exists();
            } catch (\Throwable) {
                return false;
            }
        }

        return false;
    }
}
