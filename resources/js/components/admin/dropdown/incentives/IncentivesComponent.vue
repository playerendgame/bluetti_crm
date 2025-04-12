<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Year</label>
                    <select class="form-control" v-model="currentFilters.year">
                        <option value="0">--Select Year--</option>
                        <option value="2025">2025</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Quarter</label>
                    <select class="form-control" v-model="currentFilters.quarter">
                        <option value="0">--Select Quarter--</option>
                        <option value="1">Q1</option>
                        <option value="2">Q2</option>
                        <option value="3">Q3</option>
                        <option value="4">Q4</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12 mb-2">
                <button class="btn btn-success" @click="refreshData">Generate</button>
            </div>
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card" v-for="(admin, index) in data.admins">
                        <div class="card-body">{{ admin.name }}</div>
                        <div class="table-responsive">
                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Ad-Generated Sales</th>
                                        <th>Direct Leads Sales</th>
                                        <th>Total Sales</th>
                                        <th style="background-color: black;"></th>
                                        <th>Ad-Generated Incentives</th>
                                        <th>Direct Leads Incentives</th>
                                        <th>Total Incentives</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(monthData, month) in getAdminMonthlyData(admin)" :key="month">
                                        <td>{{ month }}</td>
                                        <td>{{ monthData.ad_sales_generated }}</td>
                                        <td>{{ monthData.direct_sales_generated }}</td>
                                        <td>{{ monthData.total_sales_generated }}</td>
                                        <td style="background-color: black;"></td>
                                        <td>{{ monthData.generated_ad_incentives }}</td>
                                        <td>{{ monthData.direct_incentives_generated }}</td>
                                        <td>{{ monthData.total_incentives_generated }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: black; color: white;"></td>
                                        <td style="background-color: black; color: white;"></td>
                                        <td style="background-color: black; color: white;"></td>
                                        <td style="background-color: black; color: white;"></td>
                                        <td style="background-color: black;"></td>
                                        <td style="background-color: black; color: white;"></td>
                                        <td style="background-color: black; color: white;"></td>
                                        <td style="background-color: black; color: white;"></td>
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
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
Vue.use(Loading);
export default {
    props: {
        filters: {
            type: Object,
            required: false,
        }
    },
    data: function() {
        return {
            data: {},
            currentFilters: {
                year: 0,
                quarter: 0,
            },
        }
    },
    watch: {
        filters: {
            handler: function (filters) {
                this.currentFilters = JSON.parse(JSON.stringify(filters));
            },
            immediate: true,
        }
    },
    mounted() {
        this.refreshData();
    },
    methods: {
        refreshData() {
            var self = this;
            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/dropdown/incentives?";
            if (this.currentFilters.year != null && this.currentFilters.quarter != null) {
                url +=
                    "year=" +
                    this.currentFilters.year +
                    "&quarter=" +
                    this.currentFilters.quarter;
            }

            axios.get(url).then(function (resp) {
                loader.hide();
                self.data = resp.data.data;
            })["catch"](function (resp) {
                loader.hide();
                alert("Could not load Data");
            });

            this.refreshAjaxUrl();
        },
        getAdminMonthlyData(admin) {
            const { name, ...months } = admin;
            return months;
        },
        getAdminTotal(admin, field) {
            const months = this.getAdminMonthlyData(admin);
            return Object.values(months).reduce((total, monthData) => {
                return total + (monthData[field] || 0);
            }, 0);
        },

    }
}
</script>