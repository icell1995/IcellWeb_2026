<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;

class ReadQRCodeImageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:readinimage';

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
        $imagePath = public_path('file/QRUntitled.png');
        $zbar = new \TarfinLabs\ZbarPhp\Zbar($imagePath);
        $code = $zbar->scan();
        // $barCode = $zbar->decode();
        // echo $code;
        // echo $barCode->code(); // "1234567890128"
        // echo $barCode->type(); // "EAN-13"
    }
}
