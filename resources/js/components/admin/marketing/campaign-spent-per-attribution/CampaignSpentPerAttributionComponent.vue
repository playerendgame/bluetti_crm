<template>
    <div>
        <div class="col-md-12">
            <h5 class="ml-1">Ads Data</h5>
            <div class="row row-sm mt-2">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <label>Select Date Range</label>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <date-range-picker 
                                        id="from-date" 
                                        ref="picker"
                                        :locale-data="{ format: 'mmm dd, yyyy'}"
                                        :opens="true"
                                        :showDropdowns="true"
                                        :autoApply="false"
                                        :timePicker="false"
                                        v-model="dateRange"
                                        @update="updateValues"
                                        @toggle="checkOpen"
                                        :time-picker-increment="1"
                                        :time-picker-seconds="true"
                                        :linkedCalendars="true">
                                    </date-range-picker>
                                </div>

                                <div class="col-md-6">
                                    <div class="dropdown float-right">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Filter
                                        </button>
                                        <ul class="dropdown-menu p-3" style="width: 13vw;">
                                            <label class="form-label">Toggle Columns</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" v-model="showAll" @change="selectAll()"/>
                                                <label class="form-check-label">Select All</label>
                                            </div>
                                            <hr>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" v-model="campaign"/>
                                                <label class="form-check-label">Campaign</label>
                                            </div>
                                            <div class="form-check form-switch ">
                                                <input class="form-check-input" type="checkbox" v-model="category"/>
                                                <label class="form-check-label">Category</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" v-model="ad_spent"/>
                                                <label class="form-check-label">Ad Spent</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" v-model="gross_sales"/>
                                                <label class="form-check-label">Gross Sales</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" v-model="order"/>
                                                <label class="form-check-label">Order</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" v-model="roas"/>
                                                <label class="form-check-label">ROAS</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" v-model="cost_per_purchase"/>
                                                <label class="form-check-label">Cost Per Purchase</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" v-model="average_order_value"/>
                                                <label class="form-check-label">Average Order Value</label>
                                            </div>                            
                                        </ul>
                                    </div>
                                </div>

                    
                          
                            </div>
                            <div class="table-responsive">
                                <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                    <thead>
                                        <tr>
                                            <th v-if="campaign">Campaign</th>
                                            <th v-if="category">Category</th>
                                            <th v-if="ad_spent">Ad Spent</th>
                                            <th v-if="gross_sales">Gross Sales</th>
                                            <th v-if="order">Orders</th>
                                            <th v-if="roas">ROAS</th>
                                            <th v-if="cost_per_purchase">Cost per Purchase</th>
                                            <th v-if="average_order_value">Average Order Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(attribution, index) in data.attributions" :key="attribution.id">
                                            <td v-if="campaign">{{ attribution.campaign_name }}</td>
                                            <td v-if="category">{{ attribution.category }}</td>
                                            <td v-if="ad_spent">{{ attribution.ad_spent }}</td>
                                            <td v-if="gross_sales">{{ attribution.gross_sales }}</td>
                                            <td v-if="order">{{ attribution.order }}</td>
                                            <td v-if="roas">{{ attribution.roas }}</td>
                                            <td v-if="cost_per_purchase">{{ attribution.cost_per_purchase }}</td>
                                            <td v-if="average_order_value">{{ attribution.aov }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    components: {
        DateRangePicker
    },
    data: function() {
        var endDate = new Date();
        var startDate = new Date(endDate.getFullYear(), endDate.getMonth(), 1);
        return {
            dateRange: {
                startDate: startDate,
                endDate: endDate,
            },
            showAll: true,
            campaign: true,
            category: true,
            ad_spent: true,
            gross_sales: true,
            order: true,
            roas: true,
            cost_per_purchase: true,
            average_order_value: true,
            data: {},
        }
    },
    mounted() {
        var app = this;
        app.refreshPerCampaignSpent();
    },
    methods: {
        selectAll(){
            if (this.showAll) {
                this.campaign = true;
                this.category = true;
                this.ad_spent = true;
                this.gross_sales = true;
                this.order = true;
                this.roas = true;
                this.cost_per_purchase = true;
                this.average_order_value = true;
            } else {
                this.campaign = false;
                this.category = false;
                this.ad_spent = false;
                this.gross_sales = false;
                this.order = false;
                this.roas = false;
                this.cost_per_purchase = false;
                this.average_order_value = false;
            }
        },
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
        refreshPerCampaignSpent() {
            var self = this;

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/marketing/campaign-spent-per-attribution/per-campaign?";

            if(this.dateRange.startDate != null && this.dateRange.endDate != null) {
                url += "start_date=" + this.formatDate(this.dateRange.startDate) + "&end_date=" + this.formatDate(this.dateRange.endDate); 
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
            this.refreshPerCampaignSpent();
        }
    }
}
</script>