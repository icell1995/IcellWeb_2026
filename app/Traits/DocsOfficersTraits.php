<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait DocsOfficersTraits
{
    protected function getOldNewPolresIds($accidentPolresId)
    {
        if ($accidentPolresId == 0) {
            return [0];
        }

        $mapped = DB::table('polres_migration_history')
            ->where('old_police_id', $accidentPolresId)
            ->orWhere('new_police_id', $accidentPolresId)
            ->first();

        if ($mapped) {
            return [
                $mapped->old_police_id,
                $mapped->new_police_id
            ];
        }

        return [$accidentPolresId];
    }
}
