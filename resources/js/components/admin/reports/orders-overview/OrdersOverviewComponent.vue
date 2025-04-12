<template>
    <div>
        <div class="row">
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="col-md-6">
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
                <div class="col-md-6 d-flex justify-content-end">
                    <div class="buttons">
                        <button class="btn btn-success" @click="exportOrdersOverview">Export</button>
                    </div>
                </div>

                <br>
                <br>
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                        </div>
                        <div class="table-responsive">
                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                <thead>
                                    <tr>
                                        <th @click="sortTable('status')">
                                            Category
                                            <i class="fas fa-sort" v-if="sortColumn === 'status' && sortDirection === 'asc'"></i>
                                            <i class="fas fa-sort-down" v-if="sortColumn === 'status' && sortDirection === 'desc'"></i>
                                        </th>
                                        <th @click="sortTable('count')">
                                            Count
                                            <i class="fas fa-sort" v-if="sortColumn === 'count' && sortDirection === 'asc'"></i>
                                            <i class="fas fa-sort-down" v-if="sortColumn === 'count' && sortDirection === 'desc'"></i>
                                        </th>
                                        <th @click="sortTable('percentage')">
                                            %
                                            <i class="fas fa-sort" v-if="sortColumn === 'percentage' && sortDirection === 'asc'"></i>
                                            <i class="fas fa-sort-down" v-if="sortColumn === 'percentage' && sortDirection === 'desc'"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in sortedData" :key="row.status">
                                        <td>{{ row.status }}</td>
                                        <td>{{ row.count }}</td>
                                        <td>{{ row.percentage }}</td>
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
            sortColumn: '',
            sortDirection: 'asc',
        }
    },
    mounted() {
        this.refreshData();
    },
    computed: {
        sortedData() {
            if (!this.data) {
                return []
            }

            let dataArray = [
                { status: 'Total', count: this.data.total },
                { status: 'Pending', count: this.data.pending, percentage: this.data.conversion_pending },
                { status: 'Shipped', count: this.data.shipped, percentage: this.data.conversion_shipped },
                { status: 'Delivered', count: this.data.delivered, percentage: this.data.conversion_delivered },
                { status: 'RTS', count: this.data.rts, percentage: this.data.conversion_rts },
                { status: 'Returned', count: this.data.returned, percentage: this.data.conversion_returned },
                { status: 'Out For Delivery', count: this.data.out_of_delivery, percentage: this.data.conversion_out_of_delivery },
            ];

            if (this.sortColumn === 'status') {
                dataArray.sort((a, b) => {
                    if (this.sortDirection === 'asc') {
                        return a.status.localeCompare(b.status)
                    } else {
                        return b.status.localeCompare(a.status)
                    }
                })
            } else if (this.sortColumn === 'count') {
                dataArray.sort((a, b) => {
                    if (this.sortDirection === 'asc') {
                        return a.count - b.count
                    } else {
                        return b.count - a.count
                    }
                })
            } else if (this.sortColumn === 'percentage') {
                dataArray.sort((a, b) => {
                    if (this.sortDirection === 'asc') {
                        return parseFloat(a.percentage) - parseFloat(b.percentage)
                    } else {
                        return parseFloat(b.percentage) - parseFloat(a.percentage)
                    }
                })
            }

            return dataArray
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
        sortTable(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
            } else {
                this.sortColumn = column
                this.sortDirection = 'asc'
            }
        },
        refreshData() {
            var self = this;

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/report/orders-overview?";

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
        exportOrdersOverview(){
            let headers = [
                'Status',
                'Count',
                '%'
            ];

            let data = [
                ['Total', this.data.total],
                ['Pending', this.data.pending, this.data.conversion_pending],
                ['Shipped', this.data.shipped, this.data.conversion_shipped],
                ['Delivered', this.data.delivered, this.data.conversion_delivered],
                ['RTS', this.data.rts, this.data.conversion_rts],
                ['Returned', this.data.returned, this.data.conversion_returned],
                ['Out of Delivery', this.data.out_of_delivery, this.data.conversion_out_of_delivery],
            ];

            let csvData = [headers, ...data].map((row) => row.join(','));

            let csvString = csvData.join("\n");

            let a = document.createElement('a');
            a.href = `data:text/csv;charset=utf-8,${encodeURIComponent(csvString)}`;
            a.target = '_blank';
            a.download = 'orders-overview-report.csv';
            a.click();
        }
    }
}
</script>