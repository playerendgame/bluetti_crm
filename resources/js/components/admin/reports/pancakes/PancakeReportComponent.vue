<template>
    <div>
        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">New Pancake Leads</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.total_new_pancake_leads }}</h4>
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
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Old Pancake Leads</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.total_old_pancake_leads }}</h4>
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
                                <label class="main-content-label tx-13 font-weight-bold mb-1">New Inbox Leads</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.total_new_inbox_conversation }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                        </div>
                        <div class="table-responsive">
                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>New Pancake Leads</th>
                                        <th>Old Pancake Leads</th>
                                        <th>New Inbox Conversation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(admin, index) in data.admins" :key="admin.id">
                                        <td>{{ admin.name }}</td>
                                        <td>{{ admin.new_customer_inbox }}</td>
                                        <td>{{ admin.total_engagement }}</td>
                                        <td>{{ admin.replied_count }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">Total</td>
                                        <td style="font-weight: bold;">{{ data.total_new_pancake_leads }}</td>
                                        <td style="font-weight: bold;">{{ data.total_old_pancake_leads }}</td>
                                        <td style="font-weight: bold;">{{ data.total_new_inbox_conversation }}</td>
                                    </tr>
                                </tbody>
                            </table>
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

            var url = "/ajax/admin/report/pancake?";

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
    }
}
</script>