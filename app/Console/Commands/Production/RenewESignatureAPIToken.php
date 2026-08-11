<?php

namespace App\Console\Commands\Production;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RenewESignatureAPIToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:esignature:renew-api-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew E-Signature API Token';

    /**
     * Execute the console command.
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //ESIGNATURE_API_TOKEN

        try{
            $response = Http::post(env('ESIGNATURE_API_HOST') . '/api/values/GetTokenICELL', [
                'Username' => env('ESIGNATURE_API_USERNAME'),
                'Password' => env('ESIGNATURE_API_PASSWORD')
            ]);

            $result = json_decode($response->getBody(), true);

            if(!empty($result)){
                $data = $result['data'];
		echo $data . PHP_EOL;

                //put to env
                $key = 'ESIGNATURE_API_TOKEN';
                $value = $data;
        
                // Update the .env file
                file_put_contents(base_path('.env'), str_replace(
                    "$key='" . env($key) . "'",
                    "$key='Bearer $value'",
                    file_get_contents(base_path('.env'))
                ));

                $this->info('Success Renew API Token ' . date('Y-m-d H:i:s'));
            }else{  
                //error execute api get token
                $this->error('Failed Get API Token');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
