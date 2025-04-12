<template>
    <div>
        <marketing-update-daily-ads-audit-form-modal-component :daily_audit="daily_audit" @update-daily-ads-audit="refreshAjaxUrl"/>
        <hr>
        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total Gross Sales</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.total_gross_sales }}</h4>
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
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total Ads Spent</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.total_ad_spent }}</h4>
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
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Overall ROAS</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.overall_roas }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <label>Select Date Range</label>
            <date-range-picker
                id="from-date"
                ref="picker"
                :locale-data="{ firstDay: 1, format: 'mmm dd, yyyy'}"
                :opens="true"
                :showDropdowns="true"
                :autoApply="false"
                :timePicker="false"
                v-model="dateRange"
                @update="updateValues"
                @toggle="checkOpen"
                :time-picker-increment="1"
                :time-picker-seconds="true"
                :linkedCalendars="true"
            ></date-range-picker>
            <br>
            <br>
            <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="buttons" tableID="orders-table"
                v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
                @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
            />
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div>
                            <h6 class="main-content-label mb-1">By Month</h6>
                            <p class="text-muted card-sub-title"></p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Gross Sales</th>
                                    <th>Ad Spent</th>
                                    <th>ROAS</th>
                                    <th style="background-color: black!important;"></th>
                                    <th style="background-color: black!important;"></th>
                                    <th>Facebook <br>ROAS</th>
                                    <th>Google <br>ROAS</th>
                                    <th>Lazada <br>ROAS</th>
                                    <th>Shopee <br>ROAS</th>
                                    <th style="background-color: black!important;"></th>
                                    <th style="background-color: black!important;"></th>
                                    <th>Facebook <br>Sales</th>
                                    <th>Google <br>Sales</th>
                                    <th>Lazada <br>Sales</th>
                                    <th>Shopee <br>Sales</th>
                                    <th>Referral <br>Sales</th>
                                    <th>Organic <br>Sales</th>
                                    <th style="background-color: black!important;"></th>
                                    <th style="background-color: black!important;"></th>
                                    <th>Facebook <br>Ad Spent</th>
                                    <th>Google <br>Ad Spent</th>
                                    <th>Lazada <br>Ad Spent</th>
                                    <th>Shopee <br>Ad Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(tb, index) in table.table" :key="table.id">
                                    <td>{{ tb.month }}</td>
                                    <td>{{ tb.gross_sales }}</td>
                                    <td>{{ tb.ad_spent }}</td>
                                    <td>{{ tb.roas }}</td>
                                    <td style="background-color: black!important;"></td>
                                    <td style="background-color: black!important;"></td>
                                    <td>{{ tb.facebook_roas }}</td>
                                    <td>{{ tb.google_roas }}</td>
                                    <td>{{ tb.lazada_roas }}</td>
                                    <td>{{ tb.shopee_roas }}</td>
                                    <td style="background-color: black!important;"></td>
                                    <td style="background-color: black!important;"></td>
                                    <td>{{ tb.facebook_sales }}</td>
                                    <td>{{ tb.google_sales }}</td>
                                    <td>{{ tb.lazada_sales }}</td>
                                    <td>{{ tb.shopee_sales }}</td>
                                    <td>{{ tb.referral_sales }}</td>
                                    <td>{{ tb.organic_sales }}</td>
                                    <td style="background-color: black!important;"></td>
                                    <td style="background-color: black!important;"></td>
                                    <td>{{ tb.facebook_ad_spent }}</td>
                                    <td>{{ tb.google_ad_spent }}</td>
                                    <td>{{ tb.lazada_ad_spent }}</td>
                                    <td>{{ tb.shopee_ad_spent }}</td>
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
import axios from "axios";
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
            daily_audit: null,
            data: {},
            table: {},
            ajaxUrl: "",
            refresh: 0,
            columns: [
                { name: "Date", field: "date_ad_spent_s", sortable: true, show: true },
                { name: "Gross Sales", field: "gross_sales", sortable: true, show: true },
                { name: "Ads Spent", field: "total_ad_spent", sortable: true, show: true },
                { name: "ROAS", field: "roas", sortable: true, show: true },
                { name: "" },
                { name: "Facebook ROAS", field: "facebook_roas", sortable: true, show: true },
                { name: "Google ROAS", field: "google_roas", sortable: true, show: true },
                { name: "Lazada ROAS", field: "lazada_roas", sortable: true, show: true },
                { name: "Shopee ROAS", field: "shopee_roas", sortable: true, show: true },
                { name: "" },
                { name: "Facebook Sales", field: "facebook_sales", sortable: true, show: true },
                { name: "Google Sales", field: "google_sales", sortable: true, show: true },
                { name: "Lazada Sales", field: "lazada_sales", sortable: true, show: true },
                { name: "Shopee Sales", field: "shopee_sales", sortable: true, show: true },
                { name: "Referral Sales", field: "referral_sales", sortable: true, show: true },
                { name: "Organic Sales", field: "organic_sales", sortable: true, show: true },
                { name: "" },
                { name: "Facebook Ad Spent", field: "facebook_ad_spent_s", sortable: true, show: true },
                { name: "Google Ad Spent", field: "google_ad_spent_s", sortable: true, show: true },
                { name: "Lazada Ad Spent", field: "lazada_ad_spent_s", sortable: true, show: true },
                { name: "Shopee Ad Spent", field: "shopee_ad_spent_s", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "Edit",
                    method: "updateAdsAudit",
                    type: "success",
                }
            ],
            dateRange: {
                startDate: startDate,
                endDate: endDate,
            },
        }
    },
    mounted() {
        var app = this;
        app.refreshAjaxUrl();
        app.getData();
        app.getDataTable();
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
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/marketing/daily-ads-audit/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        getData() {
            self = this;

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/marketing/daily-ads-audit/get-data?";

            if (this.dateRange.startDate != null && this.dateRange.endDate != null) {
                url +=
                "start_date=" +
                this.formatDate(this.dateRange.startDate) +
                "&end_date=" +
                this.formatDate(this.dateRange.endDate);
            }

            axios.get(url).then(function (resp) {
                loader.hide();
                self.data = resp.data.data;
            })["catch"](function (resp) {
                loader.hide();
                alert("Could not load Data");
            });
        },
        getDataTable() {
            self = this;

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/marketing/daily-ads-audit/get-table?";

            if (this.dateRange.startDate != null && this.dateRange.endDate != null) {
                url +=
                "start_date=" +
                this.formatDate(this.dateRange.startDate) +
                "&end_date=" +
                this.formatDate(this.dateRange.endDate);
            }

            axios.get(url).then(function (resp) {
                loader.hide();
                self.table = resp.data.data;
            })["catch"](function (resp) {
                loader.hide();
                alert("Could not load Data");
            });
        },
        updateValues() {
            this.getData();
            this.getDataTable();
        },
        onButtonClick(method, object) {
            if (method === "updateAdsAudit") {
                this.daily_audit = object.item;
                this.$bvModal.show("editDailyAdsAuditFormModal");
            }
        },        
    }
}

</script>