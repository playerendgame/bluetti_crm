<template>
    <div>
        <div class="row">
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="col-md-6">
                        <label class="form-label">Select Date Range</label>
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
                        <button class="btn btn-success" @click="exportMOP">Export</button>
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
                                        <th @click="sort('name')">
                                            Mode Of Payment
                                            <i :class="{'fa fa-arrow-up': sortDirection === 'asc' && sortOrder === 'name', 'fa fa-arrow-down': sortDirection === 'desc' && sortOrder === 'name'}"></i>
                                        </th>
                                        <th @click="sort('orders_count')">
                                            Count
                                            <i :class="{'fa fa-arrow-up': sortDirection === 'asc' && sortOrder === 'orders_count', 'fa fa-arrow-down': sortDirection === 'desc' && sortOrder === 'orders_count'}"></i>
                                        </th>
                                        <th @click="sort('percentage')">
                                            %
                                            <i :class="{'fa fa-arrow-up': sortDirection === 'asc' && sortOrder === 'percentage', 'fa fa-arrow-down': sortDirection === 'desc' && sortOrder === 'percentage'}"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(paymentMethod, index) in paymentMethods" :key="index">
                                        <td>{{ paymentMethod.name }}</td>
                                        <td>{{ paymentMethod.orders_count }}</td>
                                        <td>{{ paymentMethod.percentage }}</td>
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


export default{
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
            paymentMethods: [],
        }
    },
    mounted() {
        this.getModes();
    },
    computed: {
        sortedData: function() {
            if (this.paymentMethods) {
                return this.paymentMethods.sort((a, b) => {
                    if (this.sortDirection === 'asc') {
                        if (this.sortOrder === 'name') {
                            return a.name.localeCompare(b.name);
                        } else if (this.sortOrder === 'orders_count') {
                            return a.orders_count - b.orders_count;
                        } else if (this.sortOrder === 'percentage') {
                            return parseFloat(a.percentage.replace('₱', '').replace(',', '')) - parseFloat(b.percentage.replace('₱', '').replace(',', ''));
                        }
                    } else {
                        if (this.sortOrder === 'name') {
                            return b.name.localeCompare(a.name);
                        } else if (this.sortOrder === 'orders_count') {
                            return b.orders_count - a.orders_count;
                        } else if (this.sortOrder === 'percentage') {
                            return parseFloat(b.percentage.replace('₱', '').replace(',', '')) - parseFloat(a.percentage.replace('₱', '').replace(',', ''));
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
        sort(column) {
            this.sortOrder = column;
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            this.$set(this, 'sortedData', this.paymentMethods.sort((a, b) => {
                const sortedValue = {
                    name: (a.name.localeCompare(b.name)),
                    orders_count: a.orders_count - b.orders_count,
                    percentage: parseFloat(a.percentage.replace('₱', '').replace(',', '')) - parseFloat(b.percentage.replace('₱', '').replace(',', ''))
                }[column];
                return this.sortDirection === 'asc' ? sortedValue : -sortedValue;
            }));
        },
        updateValues() {
            this.getModes();
        },
        getModes() {
            axios.get('/ajax/admin/report/mode-of-payments-orders', {
                params: {
                    start_date: this.formatDate(this.dateRange.startDate),
                    end_date: this.formatDate(this.dateRange.endDate)
                }
        })
        .then(response => {
            this.paymentMethods = response.data.map(paymentMethod => {
            return {
                ...paymentMethod,
                orders_count: paymentMethod.count,
                percentage: paymentMethod.percentage
            };
        });
        })
        .catch(error => {
            console.error(error);
        });
        },
        exportMOP(){
            let headers = [
                'Mode Of Payment',
                'Count',
                '%'
            ];

            let data = this.paymentMethods.map((row) => {
                return [
                    row.name,
                    row.orders_count,
                    row.percentage,
                ];
            });

            let csvData = [headers, ...data].map((row) => row.join(','));

            let csvString = csvData.join("\n");

            let a = document.createElement('a');
            a.href = `data:text/csv;charset=utf-8,${encodeURIComponent(csvString)}`;
            a.target = '_blank';
            a.download = 'mode-of-payment-orders.csv';
            a.click();
        }
        }
    };


</script>