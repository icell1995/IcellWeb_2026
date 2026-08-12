<?php
namespace App\Helpers;

use Carbon\Carbon;

class FormatDateHelper {
    public static function formatDateRange($startDate, $endDate) {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($start->year === $end->year) {
            if ($start->month === $end->month) {
                // Jika tanggal berbeda tetapi bulan sama
                return $start->day . '-' . $end->day . ' ' . $start->locale('id')->translatedFormat('F Y');
            } else {
                // Jika tanggal dan bulan berbeda tetapi tahun sama
                return $start->day . ' ' . $start->locale('id')->translatedFormat('F') . ' - ' . 
                    $end->day . ' ' . $end->locale('id')->translatedFormat('F Y');
            }
        } else {
            // Jika tahunnya berbeda
            return $start->day . ' ' . $start->locale('id')->translatedFormat('F Y') . ' - ' . 
                $end->day . ' ' . $end->locale('id')->translatedFormat('F Y');
        }
    }
}