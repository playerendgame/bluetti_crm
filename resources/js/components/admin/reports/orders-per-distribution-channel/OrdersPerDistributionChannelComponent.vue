<template>
    <div>
        <div class="row">
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
            <div class="row mb-4">
                <div class="col-md-12 d-flex justify-content-end">
                    <div class="buttons">
                        <button class="btn btn-success" @click="exportOrdersDistribution">Export</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                        </div>
                        <div class="table-responsive">
                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                <thead>
                                <tr>
                                    <th @click="sort('name')">
                                        Distribution Channels
                                        <i :class="{'fa fa-arrow-up': sortDirection === 'asc' && sortOrder === 'name', 'fa fa-arrow-down': sortDirection === 'desc' && sortOrder === 'name'}"></i>
                                    </th>
                                    <th @click="sort('count')">
                                        Counts
                                        <i :class="{'fa fa-arrow-up': sortDirection === 'asc' && sortOrder === 'count', 'fa fa-arrow-down': sortDirection === 'desc' && sortOrder === 'count'}"></i>
                                    </th>
                                    <th @click="sort('sales')">
                                        Sales
                                        <i :class="{'fa fa-arrow-up': sortDirection === 'asc' && sortOrder === 'sales', 'fa fa-arrow-down': sortDirection === 'desc' && sortOrder === 'sales'}"></i>
                                    </th>                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(channel, index) in data.channels" :key="channel.id">
                                        <td>{{ channel.name }}</td>
                                        <td>{{ channel.count }}</td>
                                        <td>{{ channel.sales }}</td>
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
            sortOrder: '',
            sortDirection: 'asc',
        }
    },
    mounted() {
        this.refreshData();
    },
    computed: {
        sortedData: function() {
            if (this.data.channels) {
                return this.data.channels.sort((a, b) => {
                    if (this.sortDirection === 'asc') {
                        if (this.sortOrder === 'name') {
                            return a.name.localeCompare(b.name);
                        } else if (this.sortOrder === 'count') {
                            return a.count - b.count;
                        } else if (this.sortOrder === 'sales') {
                            return parseFloat(a.sales.replace('₱', '').replace(',', '')) - parseFloat(b.sales.replace('₱', '').replace(',', ''));
                        }
                    } else {
                        if (this.sortOrder === 'name') {
                            return b.name.localeCompare(a.name);
                        } else if (this.sortOrder === 'count') {
                            return b.count - a.count;
                        } else if (this.sortOrder === 'sales') {
                            return parseFloat(b.sales.replace('₱', '').replace(',', '')) - parseFloat(a.sales.replace('₱', '').replace(',', ''));
                        }
                    }
                });
            }
            return [];
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

            var url = "/ajax/admin/report/orders-per-distribution-channel?";

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
            this.refreshData();
        },
        sort(column) {
            this.sortOrder = column;
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            this.$set(this, 'sortedData', this.data.channels.sort((a, b) => {
                const sortedValue = {
                    name: (a.name.localeCompare(b.name)),
                    count: a.count - b.count,
                    sales: parseFloat(a.sales.replace('₱', '').replace(',', '')) - parseFloat(b.sales.replace('₱', '').replace(',', ''))
                }[column];
                return this.sortDirection === 'asc' ? sortedValue : -sortedValue;
            }));
        },
        exportOrdersDistribution(){
            let headers = [
                "Distribution Channels",
                "Counts",
                "Sales",
            ];

            let data = this.data.channels.map((row) => {
                return [
                    row.name,
                    row.count,
                    row.sales.replace('₱', '').replace(',', ''),
                ]
            });

            let csvData = [headers, ...data].map((row) => row.join(','));

            let csvString = csvData.join("\n");

            let a = document.createElement('a');
            a.href = `data:text/csv;charset=utf-8,${encodeURIComponent(csvString)}`;
            a.target = '_blank';
            a.download = 'orders-per-distribution-channel.csv';
            a.click();
        }
    }
}
</script>