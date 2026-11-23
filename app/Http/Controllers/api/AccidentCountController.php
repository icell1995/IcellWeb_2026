<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\Accident;
use App\Http\Resources\Accident as AccidentResource;
use App\Http\Controllers\api\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccidentCountController extends BaseController
{
    public function getCountAccident(Request $request){
        $b = '-';
        $polda = (string) ($request->polda_id ?? '-');
        $polres = (string) ($request->polres_id ?? '-');
        $jumlahTotalLaka = '0';
        $jumlahLakaInput = '0';

        $counts = DB::selectOne("
            select
                coalesce(count(*) filter (where accidents.selra_flag = 'S0106'), 0) as jumlah_sidik,
                coalesce(count(*) filter (where accidents.selra_flag <> 'S0106'), 0) as jumlah_selra
            from accidents
            left join polres on accidents.polres_id = polres.id
            left join polda on polres.polda_id = polda.id
            where
                CASE WHEN ? <> ? THEN polda.id = ? ELSE TRUE END
                AND
                CASE WHEN ? <> ? THEN polres.id = ? ELSE TRUE END
        ", [$polda, $b, $polda, $polres, $b, $polres]);

        $data['jumlahTotalLaka'] = $jumlahTotalLaka;
        $data['jumlahLakaInput'] = $jumlahLakaInput;
        $data['jumlahLakaSidik'] = $counts->jumlah_sidik ?? 0;
        $data['jumlahLakaSelra'] = $counts->jumlah_selra ?? 0;
        return $this->sendResponse($data, 'Products retrieved successfully.');
    }
}
