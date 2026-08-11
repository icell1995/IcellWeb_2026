<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class UpdateDorsIDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:dorsid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accidentWithoutDorsIds = DB::table('accidents')->select('accidents.id')->whereNull('dors_id')->get();

        $client = new client();

        foreach($accidentWithoutDorsIds as $accidentWithoutDorsId){
            $response = $client->request('GET', 'https://irsms.korlantas.polri.go.id/irsmsapi/api/getVersion?accidentId=' . $accidentWithoutDorsId->id,
                            [
                                'headers' => [
                                        'Content-Type' => 'application/json',
                                        'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell'
                                    ]
                            ]);

            $result = json_decode($response->getBody()->getContents(), true);
        
            $dorsId = $result['dors_id'];

            if($dorsId != NULL){
                DB::table('public.accidents')->where('id', $accidentWithoutDorsId->id)->update(['dors_id' => $dorsId]);

                echo "Task Successfully || " . "Accident ID : " . $accidentWithoutDorsId->id . " || " . "DORS ID : " . $dorsId . PHP_EOL;
            }

            echo "Task Skipped || " . "Accident ID : " . $accidentWithoutDorsId->id . " || " . "DORS ID : NULL" . PHP_EOL;
        }

        return 0;
    }

    /*
    public function updateDorsId(Request $request){
        // get id from parameter url
        $accidentId = $request->query('accidentId');

        if(empty($accidentId)){
            return response()->json([
                "code" => "404",
                "status" => "NOT FOUND",
                "message" => "An error occurred while processing your request.",
                "dors_id" => null
            ], 404);
        }

        $accident = DB::table('accident')->where('id', $accidentId)->first();

        return response()->json([
            "code" => "200",
            "status" => "OK",
            "message" => "Success processing your request.",
            "dors_id" => $accident->id
        ], 200);
    }
    */
}
