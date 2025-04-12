<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="row mb-2">
                    <div class="col-md-12">
                        <label>Select Date Range</label>
                    </div>
                    <date-range-picker
                        id="from-date"
                        ref="picker"
                        :locale-data="{ firstDay: 1, format: 'mmm dd, yyyy'}"
                        :showDropdowns="true"
                        :autoApply="false"
                        :timePicker="false"
                        v-model="dateRange"
                        @update="updateValues"
                        :time-picker-increment="1"
                        :time-picker-seconds="true"
                        :linkedCalendars="true"
                        class="mb-3"
                    ></date-range-picker>
                    <div class="col-md-6">
                        <label>Select Stores</label>
                        <select class="form-control" v-model="selectedStoreId">
                            <option value="0">All</option>
                            <option v-for="(store, index) in stores" :key="store.id" :value="store.id">
                                {{ store.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Select Branch</label>
                        <select class="form-control" v-model="currentFilters.branch">
                            <option value="0">All</option>
                            <option v-for="branch in filteredBranches" :key="branch.id" :value="branch.id">
                                {{ branch.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-12 mt-2">
                        <button class="btn btn-primary" @click="updateValues">Generate</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-body">
                    </div>
                    <div class="table-responsive">
                        <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Gross Sales</th>
                                    <th>Commissions</th>
                                    <th>Net Sales</th>
                                    <th>Cogs</th>
                                    <th>Net Profit</th>
                                    <th>Orders</th>
                                    <th>Daily <br>Average <br>Order</th>
                                    <th>Daily <br>Average <br>Sales</th>
                                    <th>No. <br>of Days</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Total</b></td>
                                    <td><b>{{ data.total_gross_sales }}</b></td>
                                    <td><b>{{ data.total_commissions }}</b></td>
                                    <td><b>{{ data.total_net_sales }}</b></td>
                                    <td><b>{{ data.total_cogs }}</b></td>
                                    <td><b>{{ data.total_net_profit }}</b></td>
                                    <td><b>{{ data.total_orders }}</b></td>
                                    <td><b>{{ data.average_order }}</b></td>
                                    <td><b>{{ data.average_sales }}</b></td>
                                    <td><b>{{ data.total_days }}</b></td>
                                </tr>
                                <tr v-for="(table, index) in data.table" :key="table.id">
                                    <td>{{ table.month }}</td>
                                    <td>{{ table.gross_sales }}</td>
                                    <td>{{ table.commissions }}</td>
                                    <td>{{ table.net_sales }}</td>
                                    <td>{{ table.cogs_amount }}</td>
                                    <td>{{ table.net_profit }}</td>
                                    <td>{{ table.order_count }}</td>
                                    <td>{{ table.daily_average_order }}</td>
                                    <td>{{ table.daily_average_sales }}</td>
                                    <td>{{ table.days }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DateRangePicker from "vue2-daterange-picker";
//you need to import the CSS manually (in case you want to override it)
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import Loading from "vue-loading-overlay";
// Import stylesheet
import "vue-loading-overlay/dist/vue-loading.css";
// Init plugin
Vue.use(Loading);
export default {
    props: {
        filters: {
            type: Object,
            required: false,
        },
    },
    components: {
        DateRangePicker,
    },
    data: function() {
        var endDate = new Date();
        var startDate = new Date(endDate.getFullYear(), 0);
        return {
            stores: [],
            branches: [],
            data: {},
            dateRange: {
                startDate: startDate,
                endDate: endDate,
            },
            currentFilters: {
                store: 0,
                branch: 0,
            },
            selectedStoreId: null,
        }
    },
    watch: {
        filters: {
            handler: function (filters) {
                if (filters != null && filters != undefined) {
                    this.currentFilters = JSON.parse(JSON.stringify(filters));

                    this.selectedStoreId = this.currentFilters.store;

                    if (this.currentFilters.branch) {
                        const branch = this.branches.find(b => b.id === this.currentFilters.branch);
                        if (branch) {
                            this.selectedStoreId = this.currentFilters.store;
                        }
                    }
                }
            },
            immediate: true,
        },
        selectedStoreId: {
            handler: function(newStoreId, oldStoreId) {
                if (newStoreId !== oldStoreId) {
                    this.currentFilters.branch = 0;
                    this.currentFilters.store = newStoreId;
                }
            },
            deep: true,
        },
    },
    mounted () {
        this.refreshData();
        this.refreshStores();
        this.refreshBranches();
    },
    computed: {
        filteredBranches() {
            if (!this.selectedStoreId) return [];
            return this.branches.filter(branch => branch.store_id === this.selectedStoreId);
        }
    },
    methods: {
        formatDate(date) {
            var d = new Date(date),
            month = "" + (d.getMonth() + 1),
            day = "" + d.getDate(),
            year = d.getFullYear();

            if(month.length <2 ) month = "0" + month;
            if(day.length < 2 ) day = "0" + day;

            var date = [year, month, day].join("-");
            
            return date;
        },
        refreshData() {
            var self = this;

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/retails/reports/summary?";

            if (this.dateRange.startDate != null && this.dateRange.endDate != null) {
                url +=
                "start_date=" +
                this.formatDate(this.dateRange.startDate) +
                "&end_date=" +
                this.formatDate(this.dateRange.endDate) +
                "&store=" +
                this.selectedStoreId +
                "&branch=" +
                this.currentFilters.branch;
            }

            axios.get(url)
            .then(function (resp) {
                loader.hide();
                self.data = resp.data.data;
            })["catch"](function (resp) {
                loader.hide();
                alert("Could not load Data");
            });
        },
        updateValues() {
            this.refreshData();
        },
        refreshStores() {
            var self = this;
            axios.get('/ajax/admin/retails/dropdown/store/api').then(function (resp) {
                self.stores = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Stores");
            });
        },
        refreshBranches() {
            var self = this;
            axios.get('/ajax/admin/retails/dropdown/branch/api').then(function (resp) {
                self.branches = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Branches");
            });
        },
    }
}
</script>