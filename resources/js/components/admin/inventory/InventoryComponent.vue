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
            </div>
            <br>
            <br>
            <br>
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-body"></div>
                    <div class="table-responsive">
                        <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                            <thead>
                                <tr>
                                    <th>Items</th>
                                    <th>Purchase</th>
                                    <!-- <th>Stocks Left</th> -->
                                    <th>Current Cogs</th>
                                    <th>Count Dispatch</th>
                                    <!-- <th style="color: red;">Variance</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, index) in data.products" :key="product.id" :value="product.id">
                                    <td>{{ product.name }}</td>
                                    <td>{{ product.total_purchase }}</td>
                                    <!-- <td>{{ product.stocks_left }}</td> -->
                                    <td>{{ product.current_cogs }}</td>
                                    <td>{{ product.count_dispatch }}</td>
                                    <!-- <td style="color: red;">1</td> -->
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
    components: {
        DateRangePicker
    },
    data: function() {
        var endDate = new Date();
        var startDate = new Date(endDate.getFullYear(), endDate.getMonth(), 1);
        return {
            data: {},
            dateRange: {
                startDate: startDate,
                endDate: endDate,
            },
        }
    },
    mounted() {
        this.refreshAjaxUrl();
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
            var self = this;

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/inventory/list?";

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
        updateValues() {
            this.refreshAjaxUrl();
        }
    }
}
</script>