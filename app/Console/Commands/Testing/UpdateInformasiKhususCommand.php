<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class UpdateInformasiKhususCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:informasi-khusus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new client();

        $accidentWithoutSpecialInfos = DB::table('public.accidents')->select('accidents.id')->whereNull('special_info')->limit(100000)->get();
        $accidentWithoutSpecialInfosCount = $accidentWithoutSpecialInfos->count();

        $countSpecialInfo = 0;

        foreach($accidentWithoutSpecialInfos as $accidentWithoutSpecialInfo){
            // $response = $client->request('GET', 'https://irsms.korlantas.polri.go.id/irsmsapi/api/search?accident_id=' . $accidentWithoutSpecialInfo->id,
            // [
            //     'headers' => [
            //             'Content-Type' => 'application/json',
            //             'Key' => '09s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataviewICELL'
            //         ]
            // ]);

            // $result = json_decode($response->getBody()->getContents(), true);

            $result = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Key' => '09s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataviewICELL'
            ])->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/search?accident_id=' . $accidentWithoutSpecialInfo->id);

            dd($result->json());

            echo $countSpecialInfo . PHP_EOL;

            if($result['status'] == 'success'){
                $resultInformasiKhusus = (isset($result['result'][0]['informasi_khusus'])) ? $result['result'][0]['informasi_khusus'] : NULL;

                $specialInfo = "-";
                
                if($resultInformasiKhusus != NULL){
                    $specialInfo = str_replace(" ", "_", strtoupper($resultInformasiKhusus));
                    $countSpecialInfo++;
                }

                DB::table('public.accidents')->where('id', $accidentWithoutSpecialInfo->id)->update(['special_info' => $specialInfo]);

                echo "Task Successfully || " . "Accident ID : " . $accidentWithoutSpecialInfo->id . " || " . "SPECIAL INFO : " . $specialInfo . " || " . "Remaining : " . $accidentWithoutSpecialInfosCount . PHP_EOL;

            }elseif($result['status'] == 'failed'){
                echo "Task Skipped || " . "Accident ID : " . $accidentWithoutSpecialInfo->id . " || " . "FAILED" . " || " . "Remaining : " . $accidentWithoutSpecialInfosCount . PHP_EOL;
            }
            

            $accidentWithoutSpecialInfosCount--;
        }

        return 0;
    }
}
