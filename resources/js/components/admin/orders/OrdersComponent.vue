<template>
    <div>
        <view-order-payment-method-form-modal-component :order="order" @update-order-mark-as-paid="refreshAjaxUrl" @update-order-mark-as-unpaid="refreshAjaxUrl"/>
        <update-orders-form-modal-component :order="order" @update-order="refreshAjaxUrl" />
        <update-order-delivery-status-form-modal-component :order="order" @update-order-delivery-status="refreshAjaxUrl" />
        <update-payment-details-modal-component :order="order" @update-payment="refreshAjaxUrl"/>
        <div class="row">
            <div class="col-md-12">
                <div class="justify-content-center float-right">
                    <button type="button" class="btn btn-secondary my-2 me-2" @click="showHideFilters">
                        Filter
                    </button>
                    <button type="button" class="btn btn-primary my-2 me-2" @click="exportData">Export</button>
                </div>
            </div>
        </div>
        <div class="row" v-if="showFilters">
            <div class="card custom-card">
                <div class="card-body">
                    <h5>Filters</h5>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Order From</label>
                                <input type="date" class="form-control" v-model="currentFilters.order_from"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Order To</label>
                                <input type="date" class="form-control" v-model="currentFilters.order_to"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Region</label>
                                <select class="form-control" v-model="selectedRegionId">
                                    <option :value="0">All</option>
                                    <option v-for="(region, index) in regions" :key="region.id" :value="region.id">
                                        {{ region.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Province</label>
                                <select class="form-control" v-model="selectedProvinceId">
                                    <option :value="0">All</option>
                                    <option v-for="province in filteredProvinces" :key="province.id" :value="province.id">
                                        {{ province.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>City</label>
                                <select class="form-control" v-model="currentFilters.city">
                                    <option :value="0">All</option>
                                    <option v-for="city in filteredCities" :key="city.id" :value="city.id">
                                        {{ city.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">    
                                <label>Delivery Status</label>
                                <select class="form-control" v-model="currentFilters.delivery_status">
                                    <option value="99">All</option>
                                    <option value="0">Pending</option>
                                    <option value="1">Shipped</option>
                                    <option value="2">Delivered</option>
                                    <option value="3">RTS</option>
                                    <option value="4">Returned</option>
                                    <option value="5">Out For Delivery</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="currentFilters.delivery_status == 2">
                            <div class="form-group">
                                <label>Delivered From</label>
                                <input type="date" class="form-control" v-model="currentFilters.delivered_from"/>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="currentFilters.delivery_status == 2">
                            <div class="form-group">
                                <label>Delivered To</label>
                                <input type="date" class="form-control" v-model="currentFilters.delivered_to"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Attribution</label>
                                <select class="form-control" v-model="currentFilters.attribution">
                                    <option value="0">All</option>
                                    <option v-for="(attribution, index) in attributions" :key="attribution.id" :value="attribution.id">
                                        {{ attribution.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="isReferralSelected">
                            <div class="form-group">
                                <label>Referral</label>
                                <select class="form-control">
                                    <option :value="0">All</option>
                                    <option v-for="referral in referrals" :key="referral.id" :value="referral.id">
                                        {{ referral.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Admin</label>
                                <select class="form-control" v-model="currentFilters.admin">
                                    <option value="0">All</option>
                                    <option v-for="(admin, index) in admins" :key="admin.id" :value="admin.id">
                                        {{ admin.first_name }} {{ admin.last_name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Courier</label>
                                <select class="form-control" v-model="currentFilters.courier">
                                    <option :value="0">All</option>
                                    <option v-for="courier in couriers" :key="courier.id" :value="courier.id">
                                        {{ courier.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Dispatch From</label>
                                <input type="date" class="form-control" v-model="currentFilters.dispatch_from" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Dispatch To</label>
                                <input type="date" class="form-control" v-model="currentFilters.dispatch_to" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Target Delivery From</label>
                                <input type="date" class="form-control" v-model="currentFilters.target_delivery_from" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Target Delivery To</label>
                                <input type="date" class="form-control" v-model="currentFilters.target_delivery_to" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Payment Methods</label>
                                <select class="form-control" v-model="currentFilters.mop_id">
                                    <option value="0">All</option>
                                    <option v-for="(mode_of_payment, index) in mode_of_payments" :key="mode_of_payment.id" :value="mode_of_payment.id">
                                        {{ mode_of_payment.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Payment Status</label>
                                <select class="form-control" v-model="currentFilters.payment_status">
                                    <option value="99">All</option>
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="currentFilters.payment_status == 1">
                            <div class="form-group">
                                <label>Date Paid From</label>
                                <input type="date" class="form-control" v-model="currentFilters.date_paid_from"/>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="currentFilters.payment_status == 1">
                            <div class="form-group">
                                <label>Date Paid To</label>
                                <input type="date" class="form-control" v-model="currentFilters.date_paid_to"/>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-2">
                            <button class="btn btn-primary form-control" @click="onSavedFilters">
                                Save Filters
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary form-control" @click="clearFilters">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
                v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
                @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
            />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        filters: {
            type: Object,
            required: false,
        },
        hasPermission: {
            type: Object,
            required: true,
        }
    },
    data: function() {
        let buttons = [];
        if (this.hasPermission.orders_update) {
            buttons.push({ name: "Update", method: "updateOrder" });
            buttons.push({ name: "Update Delivery Status", method: "updateDeliveryStatus" });
            // buttons.push({ name: "Update Payment Method", method: "updatePaymentMethod" });
            buttons.push({ name: "Update Payment Details", method: 'updatePaymentDetails'})
        }
        // else if(this.hasPermission.orders_delete){
        //     buttons.push({ name: "Cancel Order", method: "cancelOrder" });
        // }
        return {
            currentFilters: {
                payment_status: 99,
                order_from: 0,
                order_to: 0,
                region: 0,
                province: 0,
                city: 0,
                admin: 0,
                attribution: 0,
                courier: 0,
                delivery_status: 99,
                dispatch_from: 0,
                dispatch_to: 0,
                delivered_from: 0,
                delivered_to: 0,
                target_delivery_from: 0,
                target_delivery_to: 0,
                mode_of_payment: 0,
                mop_id: 0,
                date_paid_from: 0,
                date_paid_to: 0,
            },
            selectedRegionId: 0,
            selectedProvinceId: 0,
            mode_of_payments: [],
            attributions: [],
            referrals: [],
            regions: [],
            provinces: [],
            cities: [],
            showFilters: false,
            order: null,
            refresh: 0,
            couriers: [],
            admins: [],
            ajaxUrl: "",
            columns: [
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
                { name: "Order Date", field: "order_date_s", sortable: true, show: true },
                { name: "Order Number", field: "order_number", sortable: true, show: true },
                { name: "Paid status", field: "mark_as_paid_s", sortable: true, show: true },
                { name: "Date Paid", field: "date_paid_s", sortable: true, show: true },
                { name: 'Mode Of Payment', field: 'mode_of_payment_name', sortable: true, show: true},
                { name: "Customer Name", field: "customer_name", sortable: true, show: true },
                { name: "Region", field: "region_name", sortable: true, show: true },
                { name: "Province", field: "province_name", sortable: true, show: true },
                { name: "City", field: "city_name", sortable: true, show: true },
                { name: "Attribution", field: "attribution_name", sortable: true, show: true },
                { name: "Quantity", field: "count_orders", sortable: true, show: true },
                { name: "Total Amount", field: "total_price", sortable: true, show: true },
                { name: "COGS", field: "cogs_s", sortable: true, show: true },
                { name: "% COGS", field: "percent_cogs", sortable: true, show: true },
                { name: "Contact #", field: "contact_number", sortable: true, show: true },
                { name: "Email", field: "email", sortable: true, show: true },

                { name: "Address", field: "address", sortable: true, show: true },
                { name: "Target Delivery Date", field: "target_delivery_date_s", sortable: true, show: true },
                { name: "Status", field: "delivery_status_s", sortable: true, show: true },
                { name: "Dispatch Date", field: "dispatch_date_s", sortable: true, show: true },
                { name: "Returned Date", field: "returned_date_s", sortable: true, show: true },
                { name: "Delivered Date", field: "date_delivered_s", sortable: true, show: true },
                { name: "Tracking #", field: "tracking_number", sortable: true, show: true },
                { name: "Courier", field: "courier_name", sortable: true, show: true },
                { name: "Admin Assign", field: "admin_name", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View Orders",
                    method: 'viewOrders',
                    type: "success",
                    kind: "group",
                    buttons: buttons,
                    //[
                      //  { name: "Update", method: "updateOrder" },
                        //{ name: "Update Delivery Status", method: "updateDeliveryStatus" },
                        //{ name: "Update Payment Method", method: "updatePaymentMethod" },
                        //{ name: "Cancel Order", method: "cancelOrder" },
                    //]
                },
            ]
        }
    },
    watch: {
        filters: {
            handler: function (filters) {
                if (filters != null && filters != undefined) {
                    this.currentFilters = JSON.parse(JSON.stringify(filters));

                    this.selectedRegionId = this.currentFilters.region;
                    this.selectedProvinceId = this.currentFilters.province;

                    if (this.currentFilters.province) {
                        const province = this.provinces.find(p => p.id === this.currentFilters.province);
                        if (province) {
                            this.selectedProvinceId = this.currentFilters.region;
                        }
                    }

                    if (this.currentFilters.city) {
                        const city = this.cities.find(c => c.id === this.currentFilters.city);
                        if (city) {
                            this.selectedProvinceId = this.currentFilters.province;
                        }
                    }
                }
            },
            immediate: true,
        },
        selectedRegionId: {
            handler: function(newRegionId, oldRegionId) {
                if (newRegionId !== oldRegionId) {
                    this.selectedProvinceId = 0;
                    this.currentFilters.region = newRegionId;
                }
            },
            deep: true,
        },
        selectedProvinceId: {
            handler: function(newProvinceId, oldProvinceId) {
                if (newProvinceId !== oldProvinceId) {
                    this.currentFilters.city = 0;
                    this.currentFilters.province = newProvinceId;
                }
            },
            deep: true,
        },
    },
    mounted() {
        var app = this;
        app.refreshAjaxUrl();
        app.refreshAttributions();
        app.refreshRegions();
        app.refreshAdmins();
        app.refreshProvinces();
        app.refreshCities();
        app.refreshCouriers();
        app.getPaymentMethodsInFiltering()
        app.refreshReferrals();
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.orders_update) {
                return this.buttons;
            }else if(this.hasPermission.orders_delete){
                return this.buttons;
            }
            return [];
        },
        filteredProvinces() {
            if (!this.selectedRegionId) return [];
            return this.provinces.filter(province => province.region_id === this.selectedRegionId);
        },
        filteredCities() {
            if (!this.currentFilters.province) return [];
            return this.cities.filter(city => city.province_id === this.currentFilters.province);
        },
        isReferralSelected() {
            const referralAttribution = this.attributions.find(attr => attr.name === 'Referral');
            return referralAttribution && this.currentFilters.attribution === referralAttribution.id;
        }
    },
    methods: {
        exportData() {
            self = this;

            Swal.fire({

            });
        },
        getPaymentMethodsInFiltering(){
            var self = this;
            axios.get("/ajax/admin/dropdown/mode-of-payment/api").then(function (resp) {
                self.mode_of_payments = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Mode Of Payments");
            });
        },
        showHideFilters() {
            this.showFilters = !this.showFilters;
        },
        refreshRegions() {
            var self = this;
            axios.get('/ajax/admin/dropdown/region/api').then(function (resp) {
                self.regions = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Regions");
            });
        },
        refreshProvinces() {
            var self = this;
            axios.get('/ajax/admin/dropdown/province/api').then(function (resp) {
                self.provinces = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Provinces");
            });
        },
        refreshCities() {
            var self = this;
            axios.get('/ajax/admin/dropdown/city/api').then(function (resp) {
                self.cities = resp.data.data;
            })['catch'](function (resp) {
                alert("Could not load Cities");
            });
        },
        refreshReferrals() {
            var self = this;
            axios.get('/ajax/admin/referralsApi').then(function (resp) {
                self.referrals = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Referrals");
            });
        },
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/orders/list?refresh=" + this.refresh + 
                "&order_from=" + this.currentFilters.order_from +
                "&order_to=" + this.currentFilters.order_to + 
                "&region=" + this.currentFilters.region + 
                "&province=" + this.currentFilters.province + 
                "&city=" + this.currentFilters.city + 
                "&admin=" + this.currentFilters.admin +
                "&attribution=" + this.currentFilters.attribution +
                "&courier=" + this.currentFilters.courier +
                "&delivery_status=" + this.currentFilters.delivery_status +
                "&dispatch_from=" + this.currentFilters.dispatch_from +
                "&dispatch_to=" + this.currentFilters.dispatch_to +
                "&delivered_from=" + this.currentFilters.delivered_from +
                "&delivered_to=" + this.currentFilters.delivered_to +
                "&payment_status=" + this.currentFilters.payment_status +
                "&date_paid_from=" + this.currentFilters.date_paid_from +
                "&date_paid_to=" + this.currentFilters.date_paid_to +
                "&target_delivery_from=" + this.currentFilters.target_delivery_from +
                "&target_delivery_to=" + this.currentFilters.target_delivery_to +
                "&mop_id=" + this.currentFilters.mop_id +
                "&";
            this.ajaxUrl = url;
        },
        refreshAttributions() {
            var self = this;
            axios.get("/ajax/admin/dropdown/attribution/api").then(function (resp) {
                self.attributions = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Attributions");
            });
        },
        refreshAdmins() {
            var self = this;
            axios.get("/ajax/admin/dropdown/admin/api").then(function (resp) {
                self.admins = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Admins");
            })
        },
        refreshCouriers() {
            var self = this;
            axios.get('/ajax/admin/dropdown/courier/api').then(function (resp) {
                self.couriers = resp.data.data;
            })['catch'](function (resp) {
                alert("Could not load Couriers");
            });
        },
        onButtonClick(method, object) {
            if (method === "viewOrders") {
                window.open("/admin/orders/" + object.item.id);
            } else if (method === "updateOrder") {
                this.order = object.item;
                this.$bvModal.show("updateOrderFormModal");
            } else if (method === "updateDeliveryStatus") {
                this.order = object.item;
                this.$bvModal.show("updateDeliveryStatusFormModal");
            // } else if (method === "updatePaymentMethod") {
            //     this.order = object.item;
            //     this.$bvModal.show("viewOrderPaymentMethodFormModal");
            } else if (method === "cancelOrder") {
                this.order = object.item;
                this.$bvModal.show('cancelOrderFormModal');
            } else if(method === 'updatePaymentDetails'){
                this.order = object.item;
                this.$bvModal.show('updatePaymentMethods');
            }
        },
        onSavedFilters() {
            window.location.href =
            window.location.pathname +
            "?" +
            $.param({
                order_from: this.currentFilters.order_from,
                order_to: this.currentFilters.order_to,
                region: this.selectedRegionId,
                province: this.selectedProvinceId,
                city: this.currentFilters.city,
                admin: this.currentFilters.admin,
                attribution: this.currentFilters.attribution,
                courier: this.currentFilters.courier,
                delivery_status: this.currentFilters.delivery_status,
                dispatch_from: this.currentFilters.dispatch_from,
                dispatch_to: this.currentFilters.dispatch_to,
                delivered_from: this.currentFilters.delivered_from,
                delivered_to: this.currentFilters.delivered_to,
                payment_status: this.currentFilters.payment_status,
                date_paid_from: this.currentFilters.date_paid_from,
                date_paid_to: this.currentFilters.date_paid_to,
                target_delivery_from: this.currentFilters.target_delivery_from,
                target_delivery_to: this.currentFilters.target_delivery_to,
                mop_id: this.currentFilters.mop_id,
            });
        },
        clearFilters() {
            this.currentFilters.order_from = 0,
            this.currentFilters.order_to = 0;
            this.selectedRegionId = 0;
            this.selectedProvinceId = 0;
            this.currentFilters.city = 0;
            this.currentFilters.admin = 0;
            this.currentFilters.attribution = 0;
            this.currentFilters.courier = 0;
            this.currentFilters.delivery_status = 99;
            this.currentFilters.dispatch_from = 0;
            this.currentFilters.dispatch_to = 0;
            this.currentFilters.delivered_from = 0;
            this.currentFilters.delivered_to = 0;
            this.currentFilters.payment_status = 99;
            this.currentFilters.target_delivery_from = 0;
            this.currentFilters.target_delivery_to = 0;
            this.currentFilters.mop_id = 0;
            this.currentFilters.date_paid_from = 0;
            this.currentFilters.date_paid_to = 0;
            this.refreshAjaxUrl();
        },
        updateOrderPayment(updatedPaymentMethod) {
            this.order.payment_method = updatedPaymentMethod;
        },
        exportData() {
            self = this;

            let data = {
                order_from: this.currentFilters.order_from,
                order_to: this.currentFilters.order_to,
                region: this.selectedRegionId,
                province: this.currentFilters.province,
                city: this.currentFilters.city,
                admin: this.currentFilters.admin,
                attribution: this.currentFilters.attribution,
                courier: this.currentFilters.courier,
                delivery_status: this.currentFilters.delivery_status,
                dispatch_from: this.currentFilters.dispatch_from,
                dispatch_to: this.currentFilters.dispatch_to,
                delivered_from: this.currentFilters.delivered_from,
                delivered_to: this.currentFilters.delivered_to,
                payment_status: this.currentFilters.payment_status,
                date_paid_from: this.currentFilters.date_paid_from,
                date_paid_to: this.currentFilters.date_paid_to,
                target_delivery_from: this.currentFilters.target_delivery_from,
                target_delivery_to: this.currentFilters.target_delivery_to,
                mop_id: this.currentFilters.mop_id,
            };

            Swal.fire({
                title: "Export Orders",
                text: "Are you sure you want to export these orders?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Export",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise(function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/orders/export",
                            data: data,
                            config: {headers: { "Content-type": "application/json"} },
                        })
                        .then(function (response) {
                            if (response.data.success) {
                                Swal.fire({
                                    title: response.data.message,
                                    text: "",
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "Okay",
                                })
                            } else {
                                Swal.fire({
                                    title: response.data.message,
                                    text: "",
                                    icon: "error",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                });
                            }
                        }).catch(function (response) {
                            if(response.response == 422) {
                                var key = Object.keys(response.response.data.errors)[0];
                                var errorMessage = response.response.data.errors[key][0];
                                Swal.fire({
                                    title: errorMessage,
                                    text: "",
                                    icon: "error",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                });
                            }
                        });
                    });
                },
                allowOutsideClick: false,
            }).then((result) => {
                if (!result.value) {

                }
            });
        }
    }
}
</script>