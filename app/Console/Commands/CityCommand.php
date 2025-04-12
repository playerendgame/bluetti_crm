<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\City;

class CityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:cities';

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
        $cities = (new FastExcel)->sheet(3)->import(storage_path('app/storage/cities/cities.xlsx'));

        foreach ($cities as $city) {
            $data = City::where('id', '=', $city["id"])->first();
            if ($data != null) {
                continue;
            } else {
                $data = new City();
            }

            $data->id = $city["id"] ?? NULL;
            $data->name = $city["name"] ?? NULL;
            $data->province_id = $city["province_id"] ?? NULL;
            $data->is_active = 1;
            $data->save();
        }

        return;
    }
}
