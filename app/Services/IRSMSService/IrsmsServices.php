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
        $this->baseUrl = (string) config('services.irsms.total_laka_url', 'https://irsms.korlantas.polri.go.id/irsmsapi/api/getTotalLaka');
        $this->headers =
            [
                'Key' => (string) config('services.irsms.total_laka_key'),
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
        } catch (\Throwable $th) {
            return collect([]);
        }
    }
}
