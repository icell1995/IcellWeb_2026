<?php

namespace App\Services\IRSMSService;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class IrsmsServices
{
    protected $baseUrl;
    protected $headers;

    public function __construct()
    {
        $this->baseUrl = 'https://irsms.korlantas.polri.go.id/irsmsapi/api/getTotalLaka';
        $this->headers =
            [
                'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell',
                'Content-Type' => 'application/json'
            ];
    }

    public function getDataWithDateRange($startDate, $endDate): Collection
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->withQueryParameters([
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ])
                ->get($this->baseUrl)
                ->json();

            return collect($response['result']);
        } catch (\Throwable $th){
            return collect([]);
        }
    }
}
