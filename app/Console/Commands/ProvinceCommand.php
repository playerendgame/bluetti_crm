<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Province;

class ProvinceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:provinces';

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
        $provinces = (new FastExcel)->sheet(2)->import(storage_path('app/storage/provinces/provinces.xlsx'));

        foreach ($provinces as $province) {
            $data = Province::where('id', '=', $province["id"])->first();
            if ($data != null) {
                continue;
            } else {
                $data = new Province();
            }

            $data->id = $province["id"] ?? NULL;
            $data->name = $province["name"] ?? NULL;
            $data->region_id = $province["region_id"] ?? NULL;
            $data->is_active = 1;
            $data->save();
        }

        return;
    }
}
