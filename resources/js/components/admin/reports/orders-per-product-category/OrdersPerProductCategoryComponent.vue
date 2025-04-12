<template>
    <div>
        <div class="row">
            <div class="row mb-2">
                <div class="col-md-6">
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
                <div class="col-md-6 d-flex justify-content-end">
                    <div class="buttons">
                        <button class="btn btn-success" @click="exportProductCategory">Export</button>
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
                                            Product Category
                                            <i :class="{'fa fa-arrow-up': sortDirection === 'asc' && sortOrder === 'name', 'fa fa-arrow-down': sortDirection === 'desc' && sortOrder === 'name'}"></i>
                                        </th>
                                        <th @click="sort('total_orders')">
                                            Counts
                                            <i :class="{'fa fa-arrow-up': sortDirection === 'asc' && sortOrder === 'total_orders', 'fa fa-arrow-down': sortDirection === 'desc' && sortOrder === 'total_orders'}"></i>
                                        </th>
                                        <th @click="sort('total_sales')">
                                            Sales
                                            <i :class="{'fa fa-arrow-up': sortDirection === 'asc' && sortOrder === 'total_sales', 'fa fa-arrow-down': sortDirection === 'desc' && sortOrder === 'total_sales'}"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="productCategory in data.productCategories" :key="productCategory.id">
                                        <td>{{ productCategory.name }}</td>
                                        <td>{{ productCategory.total_orders }}</td>
                                        <td>{{ productCategory.total_sales }}</td>
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
            productCategories: [],
        }
    },
    computed: {
        sortedData: function() {
            if (this.data.productCategories) {
                return this.data.productCategories.sort((a, b) => {
                    if (this.sortDirection === 'asc') {
                        if (this.sortOrder === 'name') {
                            return a.name.localeCompare(b.name);
                        } else if (this.sortOrder === 'total_orders') {
                            return a.total_orders - b.total_orders;
                        } else if (this.sortOrder === 'total_sales') {
                            return parseFloat(a.total_sales.replace('₱', '').replace(',', '')) - parseFloat(b.total_sales.replace('₱', '').replace(',', ''));
                        }
                    } else {
                        if (this.sortOrder === 'name') {
                            return b.name.localeCompare(a.name);
                        } else if (this.sortOrder === 'total_orders') {
                            return b.total_orders - a.total_orders;
                        } else if (this.sortOrder === 'total_sales') {
                            return parseFloat(b.total_sales.replace('₱', '').replace(',', '')) - parseFloat(a.total_sales.replace('₱', '').replace(',', ''));
                        }
                    }
                });
            }
            return [];
        }
    },
    mounted() {
        this.refreshData();
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
            this.$set(this, 'sortedData', this.data.productCategories.sort((a, b) => {
                const sortedValue = {
                    name: (a.name.localeCompare(b.name)),
                    total_orders: a.total_orders - b.total_orders,
                    total_sales: parseFloat(a.total_sales.replace('₱', '').replace(',', '')) - parseFloat(b.total_sales.replace('₱', '').replace(',', ''))
                }[column];
                return this.sortDirection === 'asc' ? sortedValue : -sortedValue;
            }));
        },
        refreshData() {
            var self = this;

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/report/orders-per-product-category?";

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
        exportProductCategory(){
            let headers = [
                'Product Category',
                'Count',
                'Sales',
            ];

            let data = this.data.productCategories.map((row) => {
                return [
                    row.name,
                    row.total_orders,
                    row.total_sales.replace('₱', '').replace(',', '')
                ]
            });

            let csvData = [headers, ...data].map((row) => row.join(','));

            let csvString = csvData.join("\n");

            let a = document.createElement('a');
            a.href = `data:text/csv;charset=utf-8,${encodeURIComponent(csvString)}`;
            a.target = '_blank';
            a.download = 'orders-per-product-category.csv';
            a.click();
            
        }
    }
}
</script>