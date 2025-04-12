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
                <div class="col-md-6">
                    <div class="buttons d-flex justify-content-end">
                        <button class="btn btn-success" @click="exportOrdersPerCategory">Export</button>
                    </div>
                </div>
        
                <br>
                <br>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <label class="h3">Top Performing Products:</label><br><br>
                            <ul class="h4">
                                <li v-for="product in data.most_ordered_products" :key="product.name">
                                    {{ product.name }} ({{ product.total_orders }} orders)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                        </div>
                        <div class="table-responsive">
                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                <thead>
                                    <tr>
                                        <th @click="sortTable('category')">
                                            Category
                                            <i class="fas fa-sort" v-if="sortColumn === 'category' && sortDirection === 'asc'"></i>
                                            <i class="fas fa-sort-down" v-if="sortColumn === 'category' && sortDirection === 'desc'"></i>
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
                                    <tr v-for="row in sortedData" :key="row.category">
                                        <td>{{ row.category }}</td>
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
                { category: 'Total', count: this.data.total },
                { category: 'Facebook', count: this.data.facebook, percentage: this.data.conversion_facebook },
                { category: 'Website', count: this.data.website, percentage: this.data.conversion_website },
                { category: 'Lazada', count: this.data.lazada, percentage: this.data.conversion_lazada },
                { category: 'Shopee', count: this.data.shopee, percentage: this.data.conversion_shopee },
                { category: 'Organic', count: this.data.organic, percentage: this.data.conversion_organic },
                { category: 'Referral', count: this.data.referral, percentage: this.data.conversion_referral },
            ];

            if (this.sortColumn === 'category') {
                dataArray.sort((a, b) => {
                    if (this.sortDirection === 'asc') {
                        return a.category.localeCompare(b.category)
                    } else {
                        return b.category.localeCompare(a.category)
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

            var url = "/ajax/admin/report/orders-per-category?";

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
        exportOrdersPerCategory(){
            let headers = [
                'Category',
                'Count',
                '%'
            ];

            let data = [
                ['Total', this.data.total],
                ['Facebook', this.data.facebook, this.data.conversion_facebook],
                ['Website', this.data.website, this.data.conversion_website],
                ['Lazada', this.data.lazada, this.data.conversion_lazada],
                ['Shopee', this.data.shopee, this.data.conversion_shopee],
                ['Organic', this.data.organic, this.data.conversion_organic],
                ['Referral', this.data.referral, this.data.conversion_referral],
            ];

            let topProducts = [
                ['Top Performing Products:'],
            ];

            this.data.most_ordered_products.forEach((product) => {
                topProducts.push([product.name, product.total_orders]);
            })

            let csvData = [headers, ...data, ...topProducts].map((row) => row.join(','));

            let csvString = csvData.join("\n");

            let a = document.createElement('a');
            a.href = `data:text/csv;charset=utf-8,${encodeURIComponent(csvString)}`;
            a.target = '_blank';
            a.download = 'orders-per-category.csv';
            a.click();

        }
    }
}
</script>
