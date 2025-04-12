<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permissions;
use Carbon\Carbon;

class permissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'permissions command';

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
        $permissions = [
            ['name' => 'dropdown.show', 'description' => 'Show Dropdown'],
            ['name' => 'admins.show', 'description' => 'Show Dropdown Admins'],
            ['name' => 'admin.create', 'description' => 'Admin Add Permissions'],
            ['name' => 'admin.update', 'description' => 'Admin Edit Permissions'],
            ['name' => 'admin.delete', 'description' => 'Admin Delete Permissions'],
            ['name' => 'roles.show', 'description' => 'Show Dropdown Roles'],
            ['name' => 'role.create', 'description' => 'Role Add Permissions'],
            ['name' => 'role.update', 'description' => 'Role Edit Permissions'],
            ['name' => 'role.delete', 'description' => 'Role Delete Permissions'],
            ['name' => 'couriers.show', 'description' => 'Show Dropdown Couriers'],
            ['name' => 'courier.create', 'description' => 'Courier Add Permissions'],
            ['name' => 'courier.update', 'description' => 'Courier Edit Permissions'],
            ['name' => 'courier.delete', 'description' => 'Courier Delete Permissions'],
            ['name' => 'mop.show', 'description' => 'Show Dropdown Mode Of Payments'],
            ['name' => 'mop.create', 'description' => 'Mode Of Payment Add Permissions'],
            ['name' => 'att.show', 'description' => 'Show Dropdown Attribution'],
            ['name' => 'att.create', 'description' => 'Attribution Add Permissions'],
            ['name' => 'att.update', 'description' => 'Attribution Edit Permissions'],
            ['name' => 'att.delete', 'description' => 'Attribution Delete Permissions'],
            ['name' => 'funnels.show', 'description' => 'Show Dropdown Funnel'],
            ['name' => 'funnel.create', 'description' => 'Funnel Add Permissions'],
            ['name' => 'funnel.update', 'description' => 'Funne; Edit Permissions' ],
            ['name' => 'targets.show', 'description' => 'Show Dropdown Targets'],
            ['name' => 'target.create', 'description' => 'Target Add Permissions'],
            ['name' => 'marketing.show' , 'description' => 'Show Marketing'],
            ['name' => 'daily-aud-ads.show' , 'description' => 'Marketing Daily Ads vs Gross Sales Show'],
            ['name' => 'daily-aud-ads.create', 'description' => 'Marketing Daily Ads vs Gross Sales Add Permissions'],
            ['name' => 'campaign-spent.show', 'description' => 'Marketing Campaign Spent Permissions show'],
            ['name' => 'campaign-spent.create', 'description' => 'Marketing Campaign Spent Add Permissions'],
            ['name' => 'finance.show', 'description' => 'Show Finance'],
            ['name' => 'purchase.show', 'description' => 'Show Finance Purchase'],
            ['name' => 'purchase.create', 'description' => 'Finance Purchase Add Permissions'],
            ['name' => 'orders.show', 'description' => 'Show Orders'],
            ['name' => 'orders.create', 'description' => 'Orders Add Permissions'],
            ['name' => 'orders.update', 'description' => 'Orders Edit Permissions'],
            ['name' => 'orders.delete', 'description' => 'Orders Delete Permissions'],
            ['name' => 'view.orders.delete', 'description' => 'View Orders Delete Permissions'],
            ['name' => 'view.orders.update', 'description' => 'View Orders Edit Permissions'],
            ['name' => 'customers.show', 'description' => 'Show Customers'],
            ['name' => 'customer.create', 'description' => 'Customers Add Permissions'],
            ['name' => 'products.show', 'description' => 'Show Products'],
            ['name' => 'product.create', 'description' => 'Products Add Permissions'],
            ['name' => 'product.update', 'description' => 'Products Edit Permissions'],
            ['name' => 'inventory.show', 'description' => 'Show Inventory'],
            ['name' => 'reports.show', 'description' => 'Show Reports'],
            ['name' => 'cities.show', 'description' => 'Show Reports'],
            ['name' => 'provinces.show', 'description' => 'Show Reports'],
            ['name' => 'regions.show', 'description' => 'Show Reports'],


        ];
    
        foreach ($permissions as $permissionData) {
            $permission = new Permissions();
            $permission->name = $permissionData['name'];
            $permission->description = $permissionData['description'];
            $permission->created_at = Carbon::now();
            $permission->save();
        }

        return 0;
        
    }
}
