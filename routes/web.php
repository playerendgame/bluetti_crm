<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get("/admin/login", [App\Http\Controllers\Admin\Login\LoginController::class, 'showLoginForm']);
Route::post("/admin/login", [App\Http\Controllers\Admin\Login\LoginController::class, "login"])->name("admin.login");

/**
 * Admin View
 */

Route::prefix('admin')->name('admin.')->namespace('App\Http\Controllers\Admin')->middleware(['admin'])->group(function() {

    /**
     * My Stats
     */

    // Account Manager
    Route::get('mystats', 'Stats\StatController@index')->name('stats.mystat');

    // Impersonate
    Route::get('impersonate/{id}', 'Dropdown\AdminController@impersonate')->name('admin.impersonate');

    // Dashboard
    Route::get('dashboard', 'Dashboard\DashboardController@dashboard')->name('dashboard');

    // Dropdown 
    Route::middleware(['permission:admins.show'])->group(function () {
        Route::get('dropdown/admin', 'Dropdown\AdminController@getAdmin')->name('dropdown.admin.index');
    });

    Route::middleware(['permission:roles.show'])->group(function () {
        Route::get('dropdown/role', 'Dropdown\RoleController@index')->name('dropdown.role.index');
    });

    Route::middleware(['permission:distribution_channels.show'])->group(function () {
        Route::get('dropdown/distribution_channels', 'Dropdown\DistributionChannelsController@index')->name('dropdown.distribution_channels.index');
    });

    Route::middleware(['permission:att.show'])->group(function () {
        Route::get('dropdown/attribution', 'Dropdown\AttributionController@index')->name('dropdown.attribution.index');
    });

    Route::middleware(['permission:regions.show'])->group(function () {
        Route::get('dropdown/region', 'Dropdown\RegionController@index')->name('dropdown.region.index');
    });

    Route::middleware(['permission:provinces.show'])->group(function () {
        Route::get('dropdown/province', 'Dropdown\ProvinceController@index')->name('dropdown.province.index');
    });

    Route::middleware((['permission:cities.show']))->group(function() {
        Route::get('dropdown/city', 'Dropdown\CityController@index')->name('dropdown.city.index');
    });

    Route::middleware(['permission:funnels.show'])->group(function () {
        Route::get('dropdown/funnel', 'Dropdown\FunnelController@index')->name('dropdown.funnel.index');
    });

    Route::middleware(['permission:targets.show'])->group(function () {
        Route::get('dropdown/target', 'Dropdown\TargetController@index')->name('dropdown.target.index');
    });

    Route::middleware(['permission:couriers.show'])->group(function () {
        Route::get('dropdown/courier', 'Dropdown\CourierController@index')->name('dropdown.courier.index');
    });

    Route::middleware(['permission:mop.show'])->group(function () {
        Route::get('dropdown/mode-of-payment', 'Dropdown\ModeOfPaymentController@index')->name('dropdown.mode-of-payment.index');
    });

    Route::get('dropdown/referrals', 'Dropdown\ReferralController@index')->name('dropdown.referrals.index');
    Route::get('dropdown/incentives', 'Dropdown\Incentives\IncentiveController@index')->name('dropdown.incentives.index');


    // Orders
    Route::get('orders/my-orders', 'Orders\OrderController@myOrder')->name('orders.my-orders');
    Route::get('/orders/updateRegion/{regionId}', 'Orders\OrderController@updateRegion');
    Route::get('/orders/updateProvince/{provinceId}', 'Orders\OrderController@updateProvince');
    Route::get('orders/orders-v2', 'Orders\OrderController@ordersV2')->name('orders.orders-v2');

    Route::middleware(['permission:orders.show'])->group(function () {
        Route::get('orders', 'Orders\OrderController@all')->name('orders.all');
    });

    Route::get('orders/create', 'Orders\OrderController@create')->name('orders.create');
    Route::get('orders/{id}', 'Orders\OrderController@show')->name('orders.show');

    // Customers
    Route::middleware(['permission:customers.show'])->group(function () {
        Route::get('customers/dashboard', 'Customers\CustomerController@dashboard')->name('customers.dashboard');
        Route::get('customers', 'Customers\CustomerController@index')->name('customers.index');
        Route::get('customers/quotation/{id}', 'Customers\QuotationController@showPage')->name('customers.quotation.show');
        Route::get('api/customers/quotation/{id}', 'Customers\QuotationController@view')->name('api.customers.quotation.view');   
 });

    Route::get('customers/create', 'Customers\CustomerController@create')->name('customers.create');
    Route::get('customers/{id}', 'Customers\CustomerController@show')->name('customers.show');


    // Products
    Route::middleware(['permission:products.show'])->group(function () {
        Route::get('products/items', 'Products\ProductController@index')->name('items.index');
        Route::get('products/category', 'ProductsCategory\ProductsCategoryController@index')->name('category.index');
    
    });
    Route::get('products/create', 'Products\ProductController@create')->name('products.create');
    Route::get('products/{id}', 'Products\ProductController@show')->name('products.show');

    /**
     * Marketing
     */
    Route::middleware(['permission:daily-aud-ads.show'])->group(function () {
        Route::get('marketing/daily-ads-audit', 'Marketing\DailyAdsAuditController@index')->name('marketing.daily-ads-audit.index');
    });
    Route::get('marketing/daily-ads-audit/create', 'Marketing\DailyAdsAuditController@create')->name('marketing.daily-ads-audit.create');

    Route::middleware(['permission:campaign-spent.show'])->group(function () {
        Route::get('marketing/campaign-spent-per-attribution', 'Marketing\CampaignSpentPerAttributionController@index')->name('marketing.campaign-spent-per-attribution.index');
    });

    Route::middleware(['permission:qr.show'])->group(function () {
        Route::get('marketing/qr-generator', 'Marketing\QRController@index')->name('marketing.qr.index');
    });

    Route::post('marketing/campaign-spent-per-attribution/import', 'Marketing\CampaignSpentPerAttributionController@import')->name('marketing.campaign-spent-per-attribution.import');
    
    /**
     * Reports
     */

    // Pancake
    Route::get('report/pancake', 'Report\PancakeController@index')->name('report.pancake.index');
    Route::get('report/summary', 'Report\SummaryController@index')->name('report.summary.index');
    Route::get('report/activity-logs', 'Report\ActivityLogsController@index')->name('report.activity-logs.index');
    Route::get('report/mode-of-payments-order', 'Report\ModeOfPaymentOrdersController@index')->name('report.mode-of-payment-orders.index');
    Route::get('report/orders-overview', 'Report\OrdersOverviewController@index')->name('report.orders-overview.index');
    Route::get('report/orders-per-category', 'Report\OrdersPerCategoryController@index')->name('report.orders-per-category.index');
    Route::get('report/orders-per-product', 'Report\OrderPerProductController@index')->name('report.orders-per-product.index');
    Route::get('report/orders-per-product-category', 'Report\OrdersPerProductCategoryController@index')->name('report.orders-per-product-category.index');
    Route::get('report/orders-per-area', 'Report\OrderPerAreaController@index')->name('report.orders-per-area');
    // Orders Per Distribution Channel
    Route::get('report/orders-per-distribution-channel', 'Report\OrderPerDistributionChannelController@index')->name('report.orders.per-distribution-channel.index');

    // Retails
    Route::get('retails/orders', 'Retail\Order\OrderController@index')->name('retails.order.index');
    Route::get('retails/orders/create', 'Retail\Order\OrderController@create')->name('retails.order.create');
    Route::get('retails/orders/{id}', 'Retail\Order\OrderController@show')->name('retail.order.show');
    Route::get('retails/dropdown/stores', 'Retail\Dropdown\StoreController@index')->name('retails.dropdown.store.index');
    Route::get('retails/dropdown/branches', 'Retail\Dropdown\BranchController@index')->name('retails.dropdown.branch.index');
    Route::get('retails/reports/summary', 'Retail\Report\SummaryController@summary')->name('retails.report.summary');;

    /**
     * Finance
     */
    Route::middleware(['permission:finance.show'])->group(function () {
        Route::get('finance/purchase-order', 'Finance\PurchaseController@index')->name('finance.purchase.index');
    });
    Route::get('finance/purchase-order/create', 'Finance\PurchaseController@create')->name("finance.purchase.create");
    Route::get('finance/purchase-order/{id}', 'Finance\PurchaseController@show')->name('finance.purchase.show');

    // Inventory
    Route::middleware(['permission:inventory.show'])->group(function () {
        Route::get('inventory', 'Inventory\InventoryController@index')->name('inventory.index');
    });

    //settings
    Route::get('settings/dropdown/changepassword', 'Settings\SettingsController@index')->name('settings.dropdown.changepassword');
});

Route::prefix('ajax/admin')->name('ajax.admin.')->namespace('App\Http\Controllers\Ajax\Admin')->middleware(['admin'])->group(function () {

    // Dashboard
    Route::get('dashboard/list', 'Dashboard\DashboardController@list')->name('dashboard.list');
    Route::get('dashboard/regular-list', 'Dashboard\DashboardController@regularDispatchList')->name('dashboard.dispatch-list');
    Route::get('dashboard/summary', 'Dashboard\DashboardController@dashboardSummary')->name('dashboard.summary');
    Route::get('dashboard/{id}/orders', 'Dashboard\DashboardController@getTargetDateCustomerOrders')->name('dashboard.orders');    
    Route::get('dashboard/couriers', 'Dashboard\DashboardController@fetchCouriers');

    // Dropdown
    Route::get('dropdown/admin/list', 'Dropdown\AdminController@list')->name('dropdown.admin.list');
    Route::post('dropdown/admin/create', 'Dropdown\AdminController@create')->name('dropdown.admin.create');
    Route::get('dropdown/admin/api', 'Dropdown\AdminController@adminApi')->name('dropdown.admin.api');
    Route::post('dropdown/admin/update/admin-role-permission', 'Dropdown\AdminController@updateAdminRolePermission')->name('dropdown.admin.update.admin-role-permission');
    Route::put('dropdown/admin/update/{id}', 'Dropdown\AdminController@update')->name('dropdown.admin.update');
    Route::delete('dropdown/admin/delete/{id}', 'Dropdown\AdminController@delete')->name('dropdown.admin.delete');
    Route::get('dropdown/admin/disabled', 'Dropdown\AdminController@disabledAccounts')->name('dropdown.admin.disabled');
    Route::post('dropdown/admin/revive/{id}', 'Dropdown\AdminController@confirmRestore')->name('dropdown.admin.revive');

    // Attributions
    Route::get('dropdown/attribution/list', 'Dropdown\AttributionController@list')->name('dropdown.attribution.list');
    Route::post('dropdown/attribution/create', 'Dropdown\AttributionController@create')->name('dropdown.attribution.create');
    Route::post('dropdown/attribution/update', 'Dropdown\AttributionController@update')->name('dropdown.attribution.update');
    Route::get('dropdown/attribution/api', 'Dropdown\AttributionController@api')->name('dropdown.attribution.api');
    Route::delete('dropdown/attribution/delete/{id}', 'Dropdown\AttributionController@delete')->name('dropdown.attribution.delete');
    Route::get('dropdown/fetch/distribution-channels/data', 'Dropdown\AttributionController@fetchDistributionChannel');

    // Distribution Channels
    Route::get('/dropdown/distribution-channels/list', 'Dropdown\DistributionChannelsController@list')->name('dropdown.dis_channels.list');
    Route::post('/dropdown/add/distribution-channel', 'Dropdown\DistributionChannelsController@store')->name('dropdown.create.dis_channels.list');
    Route::post('/dropdown/distribution-channel/update', 'Dropdown\DistributionChannelsController@update')->name('dropdown.dist_channels.update');

    // Couriers
    Route::get('dropdown/courier/list', 'Dropdown\CourierController@list')->name('dropdown.courier.list');
    Route::post('dropdown/courier/create', 'Dropdown\CourierController@create')->name('dropdown.courier.create');
    Route::post('dropdown/courier/update', 'Dropdown\CourierController@update')->name('dropdown.courier.update');
    Route::delete('dropdown/courier/delete/{id}', 'Dropdown\CourierController@delete')->name('dropdown.courier.delete');
    Route::get('dropdown/courier/api', 'Dropdown\CourierController@api')->name('dropdown.courier.api');

    // Funnels
    Route::get('dropdown/funnel/list', 'Dropdown\FunnelController@list')->name('dropdown.funnel.list');
    Route::post('dropdown/funnel/create', 'Dropdown\FunnelController@create')->name('dropdown.funnel.create');
    Route::get('dropdown/funnel/api', 'Dropdown\FunnelController@api')->name('dropdown.funnel.api');
    Route::post('dropdown/funnel/update', 'Dropdown\FunnelController@update')->name('dropdown.funnel.update');

    // Role
    Route::get('dropdown/role/list', 'Dropdown\RoleController@list')->name('dropdown.role.list');
    Route::post('dropdown/role/update', 'Dropdown\RoleController@update')->name('dropdown.role.update');
    Route::post('dropdown/role/create', 'Dropdown\RoleController@create')->name('dropdown.role.create');
    Route::get('dropdown/role/roleApi', 'Dropdown\RoleController@roleApi')->name('dropdown.role.roleApi');
    Route::delete('dropdown/role/delete/{id}', 'Dropdown\RoleController@delete')->name('dropdown.role.delete');
    Route::get('dropdown/role/disabled', 'Dropdown\RoleController@disabledRoles')->name('dropdown.role.disabled');
    Route::post('dropdown/role/revive/{id}', 'Dropdown\RoleController@confirmRestore')->name('dropdown.role.revive');
    Route::post('role-permissions/add', 'Dropdown\PermissionController@addRolePermissions')->name('role-permissions.add');
    Route::post('role-permissions/assign', 'Dropdown\PermissionController@assignPermissions')->name('role-permissions.assign');
    Route::get('role-permissions/{roleId}', 'Dropdown\PermissionController@getRolePermissions')->name('role-permissions.get');
    Route::post('role-permissions/{roleId}/update/{permissionId}', 'Dropdown\PermissionController@updatePermission')->name('role-permissions.update');

    // Regions
    Route::get('dropdown/region/list', 'Dropdown\RegionController@list')->name('dropdown.region.list');
    Route::get('orders/fetch-regions', 'Dropdown\RegionController@fetchRegions');
    Route::post('/dropdown/region/update', 'Dropdown\RegionController@update');
    Route::post('/dropdown/region/create', 'Dropdown\RegionController@store');
    Route::get('/dropdown/region/api', 'Dropdown\RegionController@regionsApi');

    // Provinces
    Route::get('dropdown/province/list', 'Dropdown\ProvinceController@list')->name('dropdown.province.list');
    Route::get('orders/fetch-provinces/{regionId}', 'Dropdown\ProvinceController@fetchProvinces');
    Route::post('/dropdown/province/update', 'Dropdown\ProvinceController@update');
    Route::post('/dropdown/province/create', 'Dropdown\ProvinceController@store');
    Route::get('/dropdown/province/api', 'Dropdown\ProvinceController@provinceApi');


    // Cities
    Route::get('dropdown/city/list', 'Dropdown\CityController@list')->name('dropdown.city.list');
    Route::get('orders/fetch-cities/{provinceId}', 'Dropdown\CityController@fetchCity');
    Route::get('orders/fetch-provinces', 'Dropdown\ProvinceController@fetchProvincesAll');
    Route::post('/dropdown/city/update', 'Dropdown\CityController@update');
    Route::post('/dropdown/city/create', 'Dropdown\CityController@store');
    Route::get('/dropdown/city/api', 'Dropdown\CityController@cityApi');


    // Targets
    Route::get('dropdown/target/list', 'Dropdown\TargetController@list')->name('dropdown.target.list');
    Route::post('dropdown/target/create', 'Dropdown\TargetController@create')->name('dropdown.target.create');

    // Mode Of Payments
    Route::get('dropdown/mode-of-payment/list', 'Dropdown\ModeOfPaymentController@list')->name('dropdown.mode-of-payment.list');
    Route::post('dropdown/mode-of-payment/create', 'Dropdown\ModeOfPaymentController@create')->name('dropdown.mode-of-payment.create');
    Route::get('dropdown/mode-of-payment/api', 'Dropdown\ModeOfPaymentController@apiList')->name('dropdown.mode-of-payment.api');
    Route::get('dropdown/mode-of-payment/fetch/{id}', 'Dropdown\ModeOfPaymentController@apiShow')->name('dropdown.mode-of-payment.fetch');
    Route::put('dropdown/update/mode-of-payment/{id}', 'Dropdown\ModeOfPaymentController@updateMOP')->name('dropdown.mode-of-payment.update');

    // Customers
    Route::post('customers/create', 'Customers\CustomerController@create')->name('customers.create');
    Route::get('customers/list', 'Customers\CustomerController@list')->name('customers.list');
    Route::get('customers/api', 'Customers\CustomerController@getAllCustomers')->name('customers.api');
    Route::get('customers/{id}/orders', 'Customers\CustomerController@getCustomerOrders')->name('customers.orders');    
    //Route::get('customers/orders/{id}', 'Customers\CustomerController@getCustomerOrders')->name('customers.orders');
    Route::get('customers/dashboard', 'Customers\CustomerController@dashboard')->name('customers.dashboard');
    // Route::get('customers/customer-leads', 'Customers\LeadController@list')->name('customers.leads');
    Route::get('customers/{id}/quotations', 'Customers\QuotationController@index')->name('customers.quotations.index');
    Route::post('customers/{id}/quotations', 'Customers\QuotationController@store')->name('customers.quotations.store');
    Route::get('products/api', 'Products\ProductController@getAllProducts')->name('products.api');

    // Incentives
    Route::get('dropdown/incentives', 'Dropdown\Incentives\IncentiveController@incentives')->name('dropdown.incentives.incentives');

    // Product
    Route::get('products/list', 'Products\ProductController@list')->name('products.list');
    Route::post('products/create', 'Products\ProductController@create')->name('products.create');
    Route::get('products/api', 'Products\ProductController@productApi')->name('products.api');
    Route::post('products/update', 'Products\ProductController@update')->name('products.update');
    Route::post('products/delete', 'Products\ProductController@delete')->name('products.delete');
    Route::get('products/{id}', 'Products\ProductController@showPurchaseOrder')->name('products.show-purchase-order');
    Route::get('/products/category/all', 'Products\ProductController@fetchCategories');

    //Product Category
    Route::get('products/category/list', 'ProductsCategory\ProductsCategoryController@list')->name('products.category.list');
    Route::post('/products/add/category', 'ProductsCategory\ProductsCategoryController@store')->name('products.category.create');

    // Orders
    Route::get('orders/list', 'Orders\OrderController@list')->name('orders.list');
    Route::post('orders/create', 'Orders\OrderController@create')->name('orders.create');
    Route::post('orders/update', 'Orders\OrderController@update')->name('orders.update');
    Route::get('order/history/{id}', 'Orders\OrderController@orderHistory')->name('orders.history');
    Route::get('orders/my-orders', 'Orders\OrderController@myOrderList')->name('orders.my-orders');
    Route::get('orders/{id}/payment-method', 'Orders\OrderController@getOrderPaymentMethods')->name('orders.my-payment-method');
    Route::post('orders/add-payment-method', 'Orders\OrderController@addOrderPaymentMethod')->name('orders.add-payment-method');
    Route::post('orders/update-payment-method', 'Orders\OrderController@updateOrderPaymentMethod')->name('orders.update-payment-method');
    Route::post('orders/update/mark-as-paid', 'Orders\OrderController@markAsPaid')->name('orders.update.mark-as-paid');
    Route::get('orders/export-excel', 'Orders\OrderController@exportExcel')->name('orders.export');
    Route::post('orders/import', 'Orders\OrderController@import')->name('orders.import');


    Route::middleware(['permission:orders.update'])->group(function () {
        Route::put('orders/{id}/order-update', 'Orders\OrderController@updateOrder')->name('orders.updateOrder');
    });
    Route::middleware(['permission:orders.delete'])->group(function () {
        Route::delete('orders/{id}/destroy', 'Orders\OrderController@destroy')->name('orders.destroy');
    });
    /**
     * Reports
     */

    // Activity Logs
    Route::get('report/activity-logs-data', 'Report\ActivityLogsController@getActivityLogs')->name('report.activity-logs-data');

    // Pancake
    Route::get('report/pancake', 'Report\PancakeController@getPancakeReport')->name('report.pancake');

    // Summary
    Route::get('report/summary', "Report\SummaryController@data")->name('report.summary');

    // Mode Of Payment Orders
    Route::get('report/mode-of-payments-orders', "Report\ModeOfPaymentOrdersController@getModeOfPaymentOrders");

    // Orders Overview
    Route::get('report/orders-overview', "Report\OrdersOverviewController@orderStatusOverview")->name('report.orders-overview');

    // Orders Per Category
    Route::get('report/orders-per-category', 'Report\OrdersPerCategoryController@data')->name('report.orders-per-category');

    // Orders Per Product
    Route::get('report/orders-per-product', 'Report\OrderPerProductController@orderPerProduct')->name('report.orders-per-product');

    //Orders per product category
    Route::get('report/orders-per-product-category', 'Report\OrdersPerProductCategoryController@orderPerProductCategory')->name('report.orders-per-product-category');

    // Orders Per Area
    Route::get('report/orders-per-area', 'Report\OrderPerAreaController@orderPerArea')->name('report.orders-per-area');

    // Orders Per Distribution Channel
    Route::get('report/orders-per-distribution-channel', 'Report\OrderPerDistributionChannelController@orderPerDistributionChannel')->name('report.orders-per-distribution-channel');

    // Inventory
    Route::get('inventory/list', 'Inventory\InventoryController@list')->name('inventory.list');

    // Referrals
    Route::get('referralsApi', 'Referral\ReferralController@getReferral')->name('referral.api');
    Route::post('referral/create', 'Referral\ReferralController@create')->name('referral.create');
    Route::get('referral/list', 'Referral\ReferralController@list')->name('referral.list');

    /**
     * Marketing
     */

    Route::get('marketing/daily-ads-audit/list', 'Marketing\DailyAdsAuditController@list')->name('marketing.daily-ads-audit.list');
    Route::get('marketing/daily-ads-audit/get-data', 'Marketing\DailyAdsAuditController@getData')->name('marketing.daily-ads-audit.get-data');
    Route::get('marketing/daily-ads-audit/get-table', 'Marketing\DailyAdsAuditController@table')->name('marketing.daily-ads-audit.get-table');
    Route::post('marketing/daily-ads-audit/create', 'Marketing\DailyAdsAuditController@create')->name('marketing.daily-ads-audit.create');
    Route::post('marketing/daily-ads-audit/update', 'Marketing\DailyAdsAuditController@update')->name('marketing.daily-ads-audit.update');
    Route::get('marketing/campaign-spent-per-attribution/per-campaign', 'Marketing\CampaignSpentPerAttributionController@campaign_list')->name('marketing.campaign-spent-per-attribution.campaign-list');
    Route::post('marketing/qr-generator/add-qr-link', 'Marketing\QRController@create')->name('marketing.add.qr');
    Route::get('marketing/fetch-qr-links', 'Marketing\QRController@list')->name('marketing.fetch.qr');
    Route::get('marketing/fetch-qr-links/{id}', 'Marketing\QRController@fetchQrPerId')->name('marketing.fetch.qr.id');
    
    /**
     * Finance
     */

    Route::get('finance/purchase-order/list', 'Finance\PurchaseController@list')->name('finance.purchase-order.list');
    Route::post('finance/purchase-order/create', 'Finance\PurchaseController@create')->name('finance.purchase-order.create');

    //Settings change password
    Route::post('change-password', 'Dropdown\AdminController@changePassword')->name('change-password');

    // Stats
    Route::get('mystats', 'Stats\StatController@stats')->name('mystats');
    Route::get('mystats/order', 'Stats\StatController@list')->name('mystats.order');

    // Retails
    
    Route::post('retails/dropdown/store/create', 'Retail\Dropdown\StoreController@create')->name('retail.dropdown.store.create');
    Route::get('retails/dropdown/store/list', 'Retail\Dropdown\StoreController@list')->name('retail.dropdown.store.list');
    Route::get('retails/dropdown/store/api', 'Retail\Dropdown\StoreController@api')->name('retial.dropdown.store.api');

    Route::post('retails/dropdown/branch/create', 'Retail\Dropdown\BranchController@create')->name('retail.dropdown.branch.create');
    Route::get('retails/dropdown/branch/list', 'Retail\Dropdown\BranchController@list')->name('retail.dropdown.branch.list');
    Route::get('retails/dropdown/branch/api', 'Retail\Dropdown\BranchController@api')->name('retail.dropdown.branch.api');

    Route::post('retails/dropdown/order/create', 'Retail\Order\OrderController@create')->name('retail.order.create');

    Route::get('retails/order/list', 'Retail\Order\OrderController@list')->name('retail.order.list');
    Route::delete('retails/order/{id}/destroy', 'Retail\Order\OrderController@destroy')->name('retail.order.destroy');
    Route::post('retails/order/update', 'Retail\Order\OrderController@update')->name('retail.order.update');

    // Reports
    Route::get('retails/reports/summary', 'Retail\Report\SummaryController@summary')->name('retail.report.summary');
});


route::prefix('ajax/admin')->namespace('App\Http\Controllers\Ajax\Admin')->group(function () {
    Route::post('/admins', 'Dropdown\ApiController@createAdmin');

    Route::middleware('api.token')->group(function () {
        Route::get('/admins/{id}', 'Dropdown\ApiController@getAdmin');
    });
});
    


