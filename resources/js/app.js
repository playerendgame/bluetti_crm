/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;
import swal from 'sweetalert2';
import Vue from 'vue';
window.Swal = swal;


import wysiwyg from "vue-wysiwyg";
Vue.use(wysiwyg, {});
import 'vue-wysiwyg/dist/vueWysiwyg.css';


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('datatable-component', require('./components/admin/utils/DatatableComponent.vue').default);

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('admin-dashboard', require('./components/admin/dashboard/DashboardComponent.vue').default);

// Dashboard
Vue.component('dashboard-target-date-modal', require('./components/admin/dashboard/ViewTargetDateModalComponent.vue').default);

// Dropdown
Vue.component('dropdown-admin-component', require('./components/admin/dropdown/admin/AdminComponent.vue').default);
Vue.component('dropdown-role-component', require('./components/admin/dropdown/role/RoleComponent.vue').default);
Vue.component('dropdown-attribution-component', require('./components/admin/dropdown/attribution/AttributionComponent.vue').default);
Vue.component('dropdown-courier-component', require('./components/admin/dropdown/courier/CourierComponent.vue').default);
Vue.component('dropdown-mode-of-payment-component', require('./components/admin/dropdown/mode-of-payment/ModeOfPaymentComponent.vue').default);
Vue.component('dropdown-funnel-component', require('./components/admin/dropdown/funnel/FunnelComponent.vue').default);
Vue.component('dropdown-target-component', require('./components/admin/dropdown/target/TargetComponent.vue').default);
Vue.component('dropdown-region-component', require('./components/admin/dropdown/region/RegionComponent.vue').default);
Vue.component('dropdown-province-component', require('./components/admin/dropdown/province/ProvinceComponent.vue').default);
Vue.component('dropdown-city-component', require('./components/admin/dropdown/city/CityComponent.vue').default);
Vue.component('dropdown-distribution-channels-component', require('./components/admin/dropdown/distribution_channels/DistributionChannelsComponent.vue').default);
Vue.component('dropdown-add-distribution-channels-component', require('./components/admin/dropdown/distribution_channels/modal/AddDistributionChannelsComponent.vue').default);
Vue.component('dropdown-update-distribution-channels-component', require('./components/admin/dropdown/distribution_channels/modal/UpdateDistributionChannelComponent.vue').default);
Vue.component('dropdown-referrals-component', require('./components/admin/dropdown/referral/ReferralComponent.vue').default);

// Retails
Vue.component('retails-order-component', require('./components/admin/retails/orders/RetailOrdersComponent.vue').default);
Vue.component('retails-order-create-component', require('./components/admin/retails/orders/AddRetailOrderFormComponent.vue').default);
Vue.component('retails-order-show-component', require('./components/admin/retails/orders/ShowRetailOrderFormModalComponent.vue').default);
// Modals
Vue.component('update-retail-order-form-modal-component', require('./components/admin/retails/orders/modals/UpdateRetailOrderFormModalComponent.vue').default);

// Dropdown
Vue.component('retails-dropdown-store-component', require('./components/admin/retails/dropdown/stores/RetailStoresComponent.vue').default);
Vue.component('retails-dropdown-branch-component', require('./components/admin/retails/dropdown/branches/RetailBranchesComponent.vue').default);

// Dropdown Modals
Vue.component('add-retail-store-form-modal-component', require('./components/admin/retails/dropdown/stores/modals/AddRetailStoreFormModalComponent.vue').default);
Vue.component('add-retail-branch-form-modal-component', require('./components/admin/retails/dropdown/branches/modals/AddRetailBrancheFormModalComponent.vue').default);

// Reports
Vue.component('retails-report-summary-component', require('./components/admin/retails/reports/RetailSummaryComponent.vue').default);


// Dropdown Modals
Vue.component('update-mop-modal-component', require('./components/admin/dropdown/mode-of-payment/modals/EditMOPModalComponent.vue').default);
Vue.component('dropdown-update-role-form-modal-component', require('./components/admin/dropdown/role/modals/EditRoleFormModalComponent.vue').default);
Vue.component('dropdown-add-target-form-modal-component', require('./components/admin/dropdown/target/modals/AddTargetFormModalComponent.vue').default);
Vue.component('add-role-permission-component', require('./components/admin/dropdown/role/modals/AddPermissionModalComponent.vue').default);
Vue.component('permissions-modal-component', require('./components/admin/dropdown/role/modals/PermissionsModalComponent.vue').default);
Vue.component('dropdown-update-region-component', require('./components/admin/dropdown/region/modal/UpdateRegionComponent.vue').default);
Vue.component('dropdown-update-province-component', require('./components/admin/dropdown/province/modal/UpdateProvinceComponent.vue').default);
Vue.component('dropdown-update-city-component', require('./components/admin/dropdown/city/modal/UpdateCityComponent.vue').default);
Vue.component('dropdown-add-region-component', require('./components/admin/dropdown/region/modal/AddRegionComponent.vue').default);
Vue.component('dropdown-add-province-component', require('./components/admin/dropdown/province/modal/AddProvinceComponent.vue').default);
Vue.component('dropdown-add-city-component', require('./components/admin/dropdown/city/modal/AddCityComponent.vue').default);


// Customers
Vue.component('customers-component', require('./components/admin/customers/CustomersComponent.vue').default);
Vue.component('create-customers-component', require('./components/admin/customers/AddCustomersComponent.vue').default);
Vue.component('create-customer-form-modal-component', require('./components/admin/customers/modals/AddCustomerFormModalComponent.vue').default);
Vue.component('view-customers-component', require('./components/admin/customers/ViewCustomerComponent.vue').default);
Vue.component('customers-dashboard-component', require('./components/admin/customers/CustomersDashboardComponent.vue').default);
// Vue.component('customers-leads-component', require('./components/admin/customers/CustomerLeadsComponent.vue').default);
Vue.component('customers-quotations-component', require('./components/admin/customers/modals/AddQuotationModalComponent.vue').default);
Vue.component('customers-view-quotations-component', require('./components/admin/customers/ViewQuotationComponent.vue').default);

// Orders
Vue.component('orders-component', require('./components/admin/orders/OrdersComponent.vue').default);
Vue.component('create-orders-component', require('./components/admin/orders/AddOrdersComponent.vue').default);
Vue.component('show-orders-component', require('./components/admin/orders/ShowOrdersComponent.vue').default);
Vue.component('my-orders-component', require('./components/admin/orders/MyOrdersComponent.vue').default);
Vue.component('orders-v2-component', require('./components/admin/orders/OrdersV2Component.vue').default);
Vue.component('import-order-data-component', require('./components/admin/orders/modals/ImportOrdersModalComponent.vue').default);


// Finance
Vue.component('finance-purchase-order-component', require('./components/admin/finance/PurchaseOrderComponent.vue').default);
Vue.component('create-finance-purchase-order-component', require('./components/admin/finance/CreatePurchaseOrderComponent.vue').default);
Vue.component('show-purchase-order-component', require('./components/admin/finance/ShowPurchaseOrderComponent.vue').default);

// Order Modal
Vue.component('update-orders-form-modal-component', require('./components/admin/orders/modals/UpdateOrdersComponent.vue').default);
Vue.component('update-order-delivery-status-form-modal-component', require('./components/admin/orders/modals/UpdateDeliveryStatusFormModalComponent.vue').default);
Vue.component('view-order-payment-method-form-modal-component', require('./components/admin/orders/modals/ViewOrderPaymentMethodFormModalComponent.vue').default);
Vue.component('add-order-payment-method-form-modal-component', require('./components/admin/orders/modals/AddOrderModeOfPaymentFormModalComponent.vue').default);
Vue.component('update-order-payment-method-form-modal-component', require('./components/admin/orders/modals/EditOrderPaymentMethodFormModalComponent.vue').default);
Vue.component('update-payment-details-modal-component', require('./components/admin/orders/modals/UpdatePaymentDetailsModalComponent.vue').default);

// Products
Vue.component('products-component', require('./components/admin/products/ProductsComponent.vue').default);
Vue.component('create-products-component', require('./components/admin/products/AddProductsComponent.vue').default);
Vue.component('update-product-form-modal-component', require('./components/admin/products/modals/UpdateProductFormModalComponent.vue').default);
Vue.component('show-product-component', require('./components/admin/products/ShowProductComponent.vue').default);

// Referrals
Vue.component('add-referrals-form-modal-component', require('./components/admin/referrals/modals/AddReferralFormModalComponent.vue').default);


//Products Category
Vue.component('products-category-component', require('./components/admin/products_category/ProductsCategoryComponent.vue').default);
Vue.component('add-products-category-component', require('./components/admin/products_category/modal/AddProductsCategoryComponent.vue').default);

// My Stats
Vue.component('mystats-component', require('./components/admin/stats/StatsComponent.vue').default);


/**
 * Report
 */

// Activity Logs
Vue.component('activity-logs-component', require('./components/admin/reports/activity-logs/ActivityLogsComponent.vue').default);

// Pancake
Vue.component('report-pancake-component', require('./components/admin/reports/pancakes/PancakeReportComponent.vue').default);

// Summary
Vue.component('report-summary-component', require('./components/admin/reports/summary/SummaryReportComponent.vue').default);

// Mode Of Payment Orders
Vue.component('mode-of-payment-orders-component', require('./components/admin/reports/mode-of-payment-orders/ModeOfPaymentOrdersComponent.vue').default);

// Orders Overview
Vue.component('report-orders-overview-component', require('./components/admin/reports/orders-overview/OrdersOverviewComponent.vue').default);

Vue.component('report-orders-per-category-component', require('./components/admin/reports/orders-per-category/OrdersPerCategoryComponent.vue').default);

// Orders Per Product
Vue.component('report-orders-per-product-component', require('./components/admin/reports/orders-per-product/OrdersPerProductComponent.vue').default);

// Orders Per Product Category
Vue.component('report-orders-per-product-category-component', require('./components/admin/reports/orders-per-product-category/OrdersPerProductCategoryComponent.vue').default);

// Orders Per Area
Vue.component('report-orders-per-area-component', require('./components/admin/reports/orders-per-area/OrdersPerAreaComponent.vue').default);

// Orders Per Distribution Channel
Vue.component('report-orders-per-distribution-channel-component', require('./components/admin/reports/orders-per-distribution-channel/OrdersPerDistributionChannelComponent.vue').default);

// Inventory
Vue.component('inventory-component', require('./components/admin/inventory/InventoryComponent.vue').default);

//Settings
Vue.component('change-password-component', require('./components/admin/settings/changePasswordComponent.vue').default);

/**
 * Marketing
 */

Vue.component('marketing-daily-ads-audit-component', require('./components/admin/marketing/daily-ads-audit/DailyAdsAuditComponent.vue').default);
Vue.component('marketing-create-daily-ads-audit-component', require('./components/admin/marketing/daily-ads-audit/AddDailyAdsAuditComponent.vue').default);
Vue.component('marketing-campaign-spent-per-attribution-component', require('./components/admin/marketing/campaign-spent-per-attribution/CampaignSpentPerAttributionComponent.vue').default);
Vue.component('marketing-update-daily-ads-audit-form-modal-component', require('./components/admin/marketing/daily-ads-audit/modals/UpdateDailyAdsAuditFormModalComponent.vue').default);
Vue.component('qr-generator-component', require('./components/admin/marketing/qr-generator/QRGeneratorComponent.vue').default);
Vue.component('add-qr-link-component-modal', require('./components/admin/marketing/qr-generator/modal/AddLinkComponentModal.vue').default);
Vue.component('view-generated-link', require('./components/admin/marketing/qr-generator/modal/ViewGeneratedComponentModal.vue').default);

/**
 * Modals
 */

// Dropdown

Vue.component('incentives-component', require('./components/admin/dropdown/incentives/IncentivesComponent.vue').default);

Vue.component('show-disabled-accounts-component', require('./components/admin/dropdown/admin/modals/DisabledAccountsModalComponent.vue').default);
Vue.component('add-admin-form-modal-component', require('./components/admin/dropdown/admin/modals/AddAdminFormModalComponent.vue').default);
Vue.component('edit-admin-form-modal-component', require('./components/admin/dropdown/admin/modals/EditAdminFormModalComponent.vue').default);

Vue.component('update-role-admin-form-modal-component', require('./components/admin/dropdown/admin/modals/UpdateRoleAdminFormModalComponent.vue').default);
Vue.component('show-disabled-role-component', require('./components/admin/dropdown/role/modals/RestoreRoleFormModalComponent.vue').default);
Vue.component('add-role-form-modal-component', require('./components/admin/dropdown/role/modals/AddRoleFormModalComponent.vue').default);

Vue.component('add-attribution-form-modal-component', require('./components/admin/dropdown/attribution/modals/AddAttributionFormModalComponent.vue').default);
Vue.component('update-attribution-form-modal-component', require('./components/admin/dropdown/attribution/modals/UpdateAttributionFormModalComponent.vue').default);

Vue.component('add-courier-form-modal-component', require('./components/admin/dropdown/courier/modals/AddCourierFormModalComponent.vue').default);
Vue.component('update-courier-form-modal-component', require('./components/admin/dropdown/courier/modals/UpdateCourierFormModalComponent.vue').default);

Vue.component('add-mode-of-payment-form-modal-component', require('./components/admin/dropdown/mode-of-payment/modals/AddModeOfPaymentFormModalComponent.vue').default);

Vue.component('add-funnel-form-modal-component', require('./components/admin/dropdown/funnel/modals/AddFunnelFormModalComponent.vue').default);
Vue.component('update-funnel-form-modal-component', require('./components/admin/dropdown/funnel/modals/UpdateFunnelFormModalComponent.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
