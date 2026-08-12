<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\HomeQueryTraits;
use App\Services\IRSMSService\IrsmsServices;

class LombaDashboardController extends Controller
{
    use HomeQueryTraits;

    public function index()
    {
        $totalService = new IrsmsServices();

        // Rekapitulasi Selra 2025 ( Lomba )
        $recapLombaBeginDate = "2025-01-01";
        $recapLombaLimitDate = "2025-12-31";
        $recapLombaNewCrimeClearanceStartTime = date('Y-m-d') . ' 00:00:00';
        $recapLombaNewCrimeClearanceEndTime = date('Y-m-d') . ' 23:59:59';

        $recapLombaCaseResolutions = $this->recapLombaCaseResolutions(
            $recapLombaBeginDate,
            $recapLombaLimitDate,
            $recapLombaNewCrimeClearanceStartTime,
            $recapLombaNewCrimeClearanceEndTime
        )
            ->where('xpolices.is_active', true)
            ->where('xpolices.class', 'RESOR')
            ->orderBy('xpolices.id', 'asc')
            ->get();

        $recapLombaCaseCollections = $totalService->getDataWithDateRange($recapLombaBeginDate, $recapLombaLimitDate);
        $recapLombaCaseResolutionCollections = collect($recapLombaCaseResolutions);

        $recapLombaLeaderboardItems = $recapLombaCaseCollections->map(function ($item) use ($recapLombaCaseResolutionCollections) {
            $match = $recapLombaCaseResolutionCollections->firstWhere('polres_id', $item['polres']);

            if ($match) {
                $p21 = $match->p21 ?? 0;
                $sp3 = $match->sp3 ?? 0;
                $sp2lid = $match->sp2lid ?? 0;
                $diversi = $match->diversi ?? 0;
                $pomtni = $match->pomtni ?? 0;
                $polda_name = $match->polda_name;
                $polres_name = $match->polres_name;

                $total = $p21 + $sp3 + $sp2lid + $diversi;

                return array_merge($item, [
                    'polda_name' => $polda_name,
                    'polres_name' => $polres_name,
                    'p21' => $p21 ?? 0,
                    'sp3' => $sp3 ?? 0,
                    'sp2lid' => $sp2lid ?? 0,
                    'diversi' => $diversi ?? 0,
                    'pomtni' => $pomtni ?? 0,
                    'total' => $total,
                ]);
            }

            return $item;
        });

        $recapLombaLeaderboardItems = collect($recapLombaLeaderboardItems)
            ->groupBy('polda')
            ->map(function ($items, $polda) {
                if (isset($items->first()['polda_name'])) {
                    $accidentTotal = $items->sum('jumlah_laka');
                    $p21Total = $items->sum('p21');
                    $p21TotalWeight = $items->sum('p21') * 6;
                    $sp3Total = $items->sum('sp3');
                    $sp3TotalWeight = $items->sum('sp3') * 2;
                    $diversiTotal = $items->sum('diversi');
                    $diversiTotalWeight = $items->sum('diversi') * 2;
                    $sp2lidTotal = $items->sum('sp2lid');
                    $sp2lidTotalWeight = $items->sum('sp2lid') * 1;
                    $totalTotal = $items->sum('total');
                    $pomtniTotal = $items->sum('pomtni');

                    $accidentNewTotal = $accidentTotal - $pomtniTotal;

                    $selraTotalPercentage = ($accidentTotal != 0) ? ($totalTotal / $accidentNewTotal) * 100 : 0;

                    $maxWeight = $accidentNewTotal * 6;
                    $totalWeight = $p21TotalWeight + $sp3TotalWeight + $diversiTotalWeight + $sp2lidTotalWeight;
                    $weightPercentage = ($maxWeight != 0) ? ($totalWeight / $maxWeight) * 100 : 0;

                    $p21TotalWeightPercentage = ($maxWeight != 0) ? ($p21TotalWeight / $maxWeight) * 100 : 0;
                    $sp3TotalWeightPercentage = ($maxWeight != 0) ? ($sp3TotalWeight / $maxWeight) * 100 : 0;
                    $diversiTotalWeightPercentage = ($maxWeight != 0) ? ($diversiTotalWeight / $maxWeight) * 100 : 0;
                    $sp2lidTotalWeightPercentage = ($maxWeight != 0) ? ($sp2lidTotalWeight / $maxWeight) * 100 : 0;
                    $selraTotalWeightPercentage = $p21TotalWeightPercentage + $sp3TotalWeightPercentage + $diversiTotalWeightPercentage + $sp2lidTotalWeightPercentage;

                    return [
                        'polda' => $polda,
                        'polda_name' => $items->first()['polda_name'],
                        'accident_total' => $accidentTotal,
                        'accident_new_total' => $accidentNewTotal,
                        'p21_total' => $p21Total,
                        'p21_total_weight' => $p21TotalWeight,
                        'p21_total_weight_percentage' => $p21TotalWeightPercentage,
                        'sp3_total' => $sp3Total,
                        'sp3_total_weight' => $sp3TotalWeight,
                        'sp3_total_weight_percentage' => $sp3TotalWeightPercentage,
                        'diversi_total' => $diversiTotal,
                        'diversi_total_weight' => $diversiTotalWeight,
                        'diversi_total_weight_percentage' => $diversiTotalWeightPercentage,
                        'sp2lid_total' => $sp2lidTotal,
                        'sp2lid_total_weight' => $sp2lidTotalWeight,
                        'sp2lid_total_weight_percentage' => $sp2lidTotalWeightPercentage,
                        'pom_tni_total' => $pomtniTotal,
                        'selra_total' => $totalTotal,
                        'selra_total_percentage' => $selraTotalPercentage,
                        'max_weight' => $maxWeight,
                        'total_weight' => $totalWeight,
                        'weight_percentage' => $weightPercentage,
                        'selra_total_weight_percentage' => $selraTotalWeightPercentage,
                    ];
                }
            });

        $_title = 'Dashboard Lomba';
        return view('lomba-dashboard.index', compact('recapLombaLeaderboardItems', 'recapLombaBeginDate', 'recapLombaLimitDate', '_title'));
    }
}
