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
    public function getCountAccident(){
        $user = Auth::user();
        switch($user->role_id){
            case 2:
                $polda = $request->polda_id;
                $polres = $request->polres_id;
                $jumlahTotalLaka = '0';
                $jumlahLakaInput='0';
                $jumlahLakaSidik = DB::select('select coalesce(count(*),0) as jumlah_sidik from accidents
                                    left join polres on accidents.polres_id = polres.id
                                    left join polda on polres.polda_id = polda.id
                                    where
                                    CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                    AND
                                    CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END 
                                    and accidents.selra_flag = \'S0106\' ');
                $jumlahLakaSelra = DB::select('select coalesce(count(*),0) as jumlah_selra from accidents
                                    left join polres on accidents.polres_id = polres.id
                                    left join polda on polres.polda_id = polda.id
                                    where
                                    CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                    AND
                                    CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
                                    and accidents.selra_flag <> \'S0106\' ');
            break;
        
            case 3:
                $polda = $request->polda_id;
                $polres = $request->polres_id;
                $jumlahTotalLaka = '0';
                $jumlahLakaInput='0';
                $jumlahLakaSidik = DB::select('select coalesce(count(*),0) as jumlah_sidik from accidents
                                    left join polres on accidents.polres_id = polres.id
                                    where
                                            CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                            AND
                                            CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
                                    and accidents.selra_flag = \'S0106\' ');
                $jumlahLakaSelra = DB::select('select coalesce(count(*),0) as jumlah_selra from accidents
                                    left join polres on accidents.polres_id = polres.id
                                    where
                                    CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                    AND
                                    CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
                                    and accidents.selra_flag <> \'S0106\' ');
                
            break;
            default:
            $polda = $request->polda_id;
            $polres = $request->polres_id;
            $jumlahTotalLaka = '0';
            $jumlahLakaInput='0';
            $jumlahLakaSidik = DB::select('select coalesce(count(*),0) as jumlah_sidik from accidents
                                where  where
                                CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                AND
                                CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
                                and 
                                accidents.selra_flag = \'S0106\' ');
            $jumlahLakaSelra = DB::select('select coalesce(count(*),0) as jumlah_selra from accidents
                                where  where
                                CASE WHEN \''.$polda.'\' <> \''.$b.'\' THEN polda.id = \''.$polda.'\' ELSE TRUE END
                                AND
                                CASE WHEN \''.$polres.'\' <> \''.$b.'\' THEN polres.id = \''.$polres.'\' ELSE TRUE END
                                and accidents.selra_flag <> \'S0106\' ');
              
        }
        $data['jumlahTotalLaka'] = $jumlahTotalLaka;
        $data['jumlahLakaInput'] = $jumlahLakaInput;
        $data['jumlahLakaSidik'] = $jumlahLakaSidik[0]->jumlah_sidik;
        $data['jumlahLakaSelra'] = $jumlahLakaSelra[0]->jumlah_selra;
        return $this->sendResponse($data, 'Products retrieved successfully.');
    }
}
