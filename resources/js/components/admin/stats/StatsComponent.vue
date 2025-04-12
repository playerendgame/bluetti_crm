<template>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Year</label>
                <select class="form-control" v-model="currentFilters.year">
                    <option value="0">--Select Year--</option>
                    <option value="2025">2025</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Quarter</label>
                <select class="form-control" v-model="currentFilters.quarter">
                    <option value="0">--Select Quarter--</option>
                    <option value="1">Q1</option>
                    <option value="2">Q2</option>
                    <option value="3">Q3</option>
                    <option value="4">Q4</option>
                </select>
            </div>
        </div>
        <div class="col-md-12 mb-2">
            <button class="btn btn-success" @click="refreshData">Update</button>
        </div>
        <div class="row" v-if="currentFilters.year != 0 && currentFilters.quarter != 0">
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Ad-Generated Incentives</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.ad_incentives_generated }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Direct Leads Incentives</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.direct_incentives_generated }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Overall Incentives</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.total_incentives_generated }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body"></div>
                        <div class="table-responsive">
                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Ad-Generated Sales</th>
                                        <th>Direct Leads Sales</th>
                                        <th>Total Sales</th>
                                        <th style="background-color: black;"></th>
                                        <th>Ad-Generated Incentives</th>
                                        <th>Direct Leads Incentives</th>
                                        <th>Total Incentives</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(stat, index) in data.stats">
                                        <td>{{ stat.month }}</td>
                                        <td>{{ stat.ad_sales_generated}}</td>
                                        <td>{{ stat.direct_sales_generated}}</td>
                                        <td>{{ stat.total_sales_generated }}</td>
                                        <td style="background-color: black;"></td>
                                        <td>{{ stat.ad_incentives_generated }}</td>
                                        <td>{{ stat.direct_incentives_generated }}</td>
                                        <td>{{ stat.total_incentives_generated }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: black; color: white;">Total</td>
                                        <td style="background-color: black; color: white;">{{ data.ad_sales_generated }}</td>
                                        <td style="background-color: black; color: white;">{{ data.direct_sales_generated }}</td>
                                        <td style="background-color: black; color: white;">{{ data.total_sales_generated}}</td>
                                        <td style="background-color: black;"></td>
                                        <td style="background-color: black; color: white;">{{ data.ad_incentives_generated }}</td>
                                        <td style="background-color: black; color: white;">{{ data.direct_incentives_generated }}</td>
                                        <td style="background-color: black; color: white;">{{ data.total_incentives_generated }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="justify-content-center float-right">
                        <button type="button" class="btn btn-secondary my-2 me-2" @click="showHideFilters">
                            Filter
                        </button>
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
                                    <label>Date Order From</label>
                                    <input type="date" class="form-control" v-model="currentFilters.date_order_from"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date Order To</label>
                                    <input type="date" class="form-control" v-model="currentFilters.date_order_to"/>
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date Delivered From</label>
                                    <input type="date" class="form-control" v-model="currentFilters.date_delivered_from"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date Delivered To</label>
                                    <input type="date" class="form-control" v-model="currentFilters.date_delivered_to"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date Paid From</label>
                                    <input type="date" class="form-control" v-model="currentFilters.date_paid_from" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date Paid To</label>
                                    <input type="date" class="form-control" v-model="currentFilters.date_paid_to" />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-2">
                                <button class="btn btn-primary form-control" @click="updateFilters">
                                    Update Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-sm">
                <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
                    :defaultSortIndex="0" defaultSortOrder="desc"
                />
            </div>
        </div>
    </div>
</template>

<script>
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
Vue.use(Loading);
export default {
    props: {
        filters: {
            type: Object,
            required: false,
        }
    },
    data: function() {
        return {
            currentFilters: {
                year: 0,
                quarter: 0,
                status: 0,
                date_paid_from: 0,
                date_paid_to: 0,
                date_order_from: 0,
                date_order_to: 0,
                delivery_status: 99,
                date_delivered_from: 0,
                date_delivered_to: 0,
            },
            data: {},
            ajaxUrl: "",
            refresh: 0,
            showFilters: false,
            columns: [
                { name: "Date Order", field: "order_date_s", sortable: true, show: true },
                { name: "Customer", field: "customer_name", sortable: true, show: true },
                { name: "Amount", field: "total_price", sortable: true, show: true },
                { name: "Delivery Status", field: "delivery_status_s", sortable: true, show: true },
                { name: "Date Delivered", field: "date_delivered_s", sortable: true, show: true },
                { name: "Paid", field: "mark_as_paid_s", sortable: true, show: true },
                { name: "Date Paid", field: "date_paid_s", sortable: true, show: true },
            ]
        }
    },
    watch: {
        filters: {
            handler: function (filters) {
                this.currentFilters = JSON.parse(JSON.stringify(filters));
            },
            immediate: true,
        }
    },
    mounted() {
        this.refreshData();
        this.refreshAjaxUrl();
    },
    methods: {
        updateFilters() {
            this.refreshAjaxUrl();
        },
        showHideFilters() {
            this.showFilters = !this.showFilters;
        },
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/mystats/order?refresh=" + this.refresh +
            "&year=" + this.currentFilters.year +
            "&quarter=" + this.currentFilters.quarter +
            "&delivery_status=" + this.currentFilters.delivery_status +
            "&date_order_from=" + this.currentFilters.date_order_from +
            "&date_order_to=" + this.currentFilters.date_order_to +
            "&date_delivered_from=" + this.currentFilters.date_delivered_from +
            "&date_delivered_to=" + this.currentFilters.date_delivered_to +
            "&date_paid_from=" + this.currentFilters.date_paid_from +
            "&date_paid_to=" + this.currentFilters.date_paid_to +
            "&";
            this.ajaxUrl = url;
        },
        refreshData() {
            var self = this;
            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/mystats?";
            if (this.currentFilters.year != null && this.currentFilters.quarter != null) {
                url +=
                    "year=" +
                    this.currentFilters.year +
                    "&quarter=" +
                    this.currentFilters.quarter;
            }

            axios.get(url).then(function (resp) {
                loader.hide();
                self.data = resp.data.data;
            })["catch"](function (resp) {
                loader.hide();
                alert("Could not load Data");
            });

            this.refreshAjaxUrl();
        }
    }
}
</script>