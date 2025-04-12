<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Region;

class RegionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:region';

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
        $regions = (new FastExcel)->sheet(1)->import(storage_path('app/storage/regions/regions.xlsx'));

        foreach ($regions as $region) {
            $data = Region::where('id', '=', $region["id"])->first();
            if ($data != null) {
                continue;
            } else {
                $data = new Region();
            }

            $data->id = $region["id"] != null ? $region["id"] : NULL;
            $data->name = $region["name"] != null ? $region["name"] : NULL;
            $data->is_active = 1;
            $data->save();
        }

        return;
    }
}
