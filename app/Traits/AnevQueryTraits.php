<?php

namespace App\Traits;

trait AnevQueryTraits
{
    protected function countSelra($jumlah_laka, $pom_tni, $total_selra)
    {
        $lakaPengurangan = max(0, $jumlah_laka - $pom_tni);

        if($jumlah_laka == 0 && $total_selra == 0){
            return 100;
        }

        if($lakaPengurangan == 0 && $total_selra == 0){
            return 100;
        }

        if($lakaPengurangan == 0){
            return 0;
        }

        return round(($total_selra / $lakaPengurangan) * 100, 2);
    }
}
