<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SidebarPageController;

class StoreSidebarPages extends Command
{
    protected $signature = 'sidebar:store-pages';
    protected $description = 'Store sidebar pages into the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $controller = new SidebarPageController();
        $response = $controller->storeSidebarPages();

        if ($response->status() === 200) {
            $this->info($response->getData()->message);
        } else {
            $this->error($response->getData()->message);
        }
    }
}
