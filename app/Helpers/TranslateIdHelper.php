<?php

namespace App\Helpers;

class TranslateIdHelper
{
    public static function getTranslatePoliceResorId($headquarterId = null, $regionalId = null, $resorId = null)
    {
        $translatedId = $resorId;

        if(!empty($regionalId) && in_array($resorId, ['', null, '-', 0, '0'])) {
            switch($regionalId) {
                case '11':
                    $translatedId = '1114';
                    break;
                default:
                    $translatedId = $resorId;
                    break;
            }
        }

        return $translatedId;
    }
}