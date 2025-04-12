<template>
    <div class="row">
        <div class="col-md-12">
            <div class="row mb-2">
                <div class="col-md-12">
                    <label>Select Date Range</label>
                </div>
                <div class="col-md-6">
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
                </div>
                <div class="col-md-6">
                    <div class="button text-end">
                        <button class="btn btn-success" @click="exportSummary">Export</button>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-body">
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Gross <br> Sales</th>
                                <th>Cogs</th>
                                <th>% COGS <br> Vat INC</th>
                                <th>No. of <br> Orders</th>
                                <th>Daily Average <br> No. Order</th>
                                <th>AOV</th>
                                <th>Daily <br> Average Sales</th>
                                <th>Sales Lag</th>
                                <th>Sales Target</th>
                                <th>No. of Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Total</th>
                                <th>{{ data.total_gross_sales }}</th>
                                <th>{{ data.total_cogs }}</th>
                                <th>{{ data.cogs_vat }}</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr v-for="(table, index) in data.table" :key="table.id">
                                <td>{{ table.month }}</td>
                                <td>{{ table.gross_sales }}</td>
                                <td>{{ table.cogs }}</td>
                                <td>{{ table.conversion }}</td>
                                <td>{{ table.orders }}</td>
                                <td>{{ table.daily_ave_order }}</td>
                                <td>{{ table.aov }}</td>
                                <td>{{ table.daily_ave_sales }}</td>
                                <td>{{ table.sales_lag }}</td>
                                <td>{{ table.sales_target }}</td>
                                <td>{{ table.count_days }}</td>
                            </tr>
                        </tbody>
                    </table>
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
        var startDate = new Date(endDate.getFullYear(), 0);
        return {
            data: {},
            dateRange: {
                startDate: startDate,
                endDate: endDate,
            },
        }
    },
    mounted() {
        var app = this;
        app.refreshData();
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
        updateValues() {
            this.refreshData();
        },
        refreshData() {
            var self = this;

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/report/summary?";

            if (this.dateRange.startDate != null && this.dateRange.endDate != null) {
                url +=
                "start_date=" +
                this.formatDate(this.dateRange.startDate) +
                "&end_date=" +
                this.formatDate(this.dateRange.endDate);
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
        exportSummary(){
            let headers = [
                "Month",
                "Gross Sales",
                "Cogs",
                "Conversion",
                "Orders",
                "Daily Average No.Order",
                "Aov",
                "Daily Average Sales",
                "Sales Lag",
                "Sales Target",
                "Count Days",
            ];

            let totalData = [
                "Total",
                this.data.total_gross_sales.replace('₱', '').replace(',', ''),
                this.data.total_cogs.replace('₱', '').replace(',', ''),
                this.data.cogs_vat,
                "",
                "",
                "",
                "",
                "",
                "",
                "",
            ];

            let data = this.data.table.map((row) => {
                return [
                row.month,
                row.gross_sales.replace('₱', '').replace(',', ''),
                row.cogs.replace('₱', '').replace(',', ''),
                row.conversion,
                row.orders,
                row.daily_ave_order,
                row.aov.replace('₱', '').replace(',', ''),
                row.daily_ave_sales.replace('₱', '').replace(',', ''),
                row.sales_lag.replace('₱', '').replace(',', ''),
                row.sales_target.replace('₱', '').replace(',', ''),
                row.count_days,
                ];
            });

            let csvData = [headers, ...data, totalData].map((row) => row.join(','));

            let csvString = csvData.join("\n");

            let a = document.createElement('a');
            a.href = `data:text/csv;charset=utf-8,${encodeURIComponent(csvString)}`;
            a.target = '_blank';
            a.download = 'summary-report.csv';
            a.click();
            }
    }
}
</script>