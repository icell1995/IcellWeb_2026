<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:generate';

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
        $this->info('Generating QR Code...');

        QrCode::format('png')
            ->size(250)
            ->errorCorrection('H')
            ->merge(public_path('images/logo2.png'), .3, true)
            ->generate('https://dokumen-tte.bareskrim.polri.go.id/DocumentInfo/Icell?id=', storage_path('images/qrcode.png'));

        $this->info('QR Code was generated successfully.');
    }
}
