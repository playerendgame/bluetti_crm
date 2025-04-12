<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\AdminRole;
use App\Models\Role;
use App\Models\Permissions;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\CampaignSpent;
use App\Models\Attribution;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Command';

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

        $permissions = Permissions::all();

        //Find the "Super Admin" role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
    
        //Assign all permissions to the "Super Admin" role
        foreach ($permissions as $permission) {
            $superAdminRole->permissions()->attach($permission->id);
        }
        
        return;
    }
}
