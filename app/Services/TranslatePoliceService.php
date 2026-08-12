<?php
namespace App\Services;

use Illuminate\Http\Request;

class TranslatePoliceService
{
    public static function translatePoliceId($policeIds = [])
    {
        $translatedPoliceId = null;

        $headquarterPoliceId = (isset($policeIds['headquarter_police_id'])) ? $policeIds['headquarter_police_id'] : null;
        $regionalPoliceId = (isset($policeIds['regional_police_id'])) ? $policeIds['regional_police_id'] : null;
        $resorPoliceId = (isset($policeIds['resor_police_id'])) ? $policeIds['resor_police_id'] : null;

        if($regionalPoliceId == '11' && empty($resorPoliceId)) {
            $translatedPoliceId = '1114';
        }

        return $translatedPoliceId;
    }
}