<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Lib\Location;
use App\Models\InvolvedPeople;
use App\Models\Officer;

class CheckQueryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:query';

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
        $districts = File::get(base_path('master_seeder/districts.json'));
        $districts = json_decode($districts, true);

        // count all districts with multiple same name with in the database
        foreach($districts as $district) {
            $count = Location::where('class', 'DISTRICT')
                ->where('name', $district['Nama_Kecamatan'])
                ->count();

            if($count < 1) {
                // $this->info('Not Exist: ' . $district['Nama_Kecamatan'] . ' - ' . $count);
            }elseif($count > 1) {
                $this->info('Same Name: ' .$district['Nama_Kecamatan'] . ' - ' . $count);
            }
        }

    }
}
