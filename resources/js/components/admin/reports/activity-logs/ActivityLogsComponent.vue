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
                <br>
                <br>
                <div class="col-md-12">
                    <div class="card custom-card">
                        <div class="table-responsive">
                            <br>
                            <div class="search-container w-25 pb-4 ml-3">
                                <input type="search" class="form-control" v-model="searchQuery" placeholder="Search by Admin, Source, Source Name">
                            </div>
                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Admin</th>
                                        <th>Source</th>
                                        <th>Source Name</th>
                                        <th>Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(log, index) in sortedPaginatedActivityLogs" :key="index">
                                        <td>{{ log.created_at }}</td>
                                        <td>{{ log.admins.first_name + ' ' + log.admins.last_name }}</td>
                                        <td>{{ log.source }}</td>
                                        <td> {{ log.name }}</td>
                                        <td>{{ log.activity }}</td>
                                    </tr>
                                </tbody>
                            </table>    
                        </div>
                    </div>
                </div>
            </div>
            <!--Pagination-->
            <nav aria-label="Page navigation">
                <div class="float-start">
                    Showing {{ startIndex }} - {{ endIndex }} of {{ activity_logs.length }} entries
                </div>
                <ul class="pagination float-end">
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <a class="page-link" href="#" aria-label="Previous" @click.prevent="previousPage">
                            <span>Previous</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <span class="page-link">{{ 1 }} > {{ totalPages }}</span>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <a class="page-link" href="#" aria-label="Next" @click.prevent="nextPage">
                            <span>Next</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>
<script>
import DateRangePicker from "vue2-daterange-picker";
import moment from 'moment';
//you need to import the CSS manually (in case you want to override it)
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import Loading from "vue-loading-overlay";
// Import stylesheet
import "vue-loading-overlay/dist/vue-loading.css";
// Init plugin
Vue.use(Loading);


export default{
    components: {
        DateRangePicker,
        moment
    },
    data: function() {
        var endDate = new Date();
        var startDate = new Date(endDate.getFullYear(), endDate.getMonth(), 1);
        return {
            dateRange: {
                startDate: startDate,
                endDate: endDate,
            },
            activity_logs:[],
            currentSortField: null,
            currentPage: 1,
            activityLogsPerPage: 10,
            searchQuery: ''
        }
    },
    computed: {
        sortedPaginatedActivityLogs() {
            const startIndex = (this.currentPage - 1) * this.activityLogsPerPage;
            const endIndex = startIndex + this.activityLogsPerPage;
            return this.activity_logs.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(startIndex, endIndex);
        },
        totalPages() {
            return Math.ceil(this.activity_logs.length / this.activityLogsPerPage);
        },
        pageRange() {
            const start = 1;
            const end = this.totalPages;
            return `${start} > ${end}`;
        },
        startIndex() {
            return (this.currentPage - 1) * this.activityLogsPerPage + 1;
        },
        endIndex() {
            const end = this.currentPage * this.activityLogsPerPage;
            return end > this.activity_logs.length ? this.activity_logs.length : end;
        },
    },
    mounted() {
        this.getData();
    },
    watch: {
        searchQuery: function(newQuery) {
            if(this.debounceTimeout) {
                clearTimeout(this.debounceTimeout);
            }
            this.debounceTimeout = setTimeout(() => {
                this.getData();
            }, 500);
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
            
            return moment(date).format('YYYY-MM-DD HH:mm:ss');
        },
        updateValues() {
            this.getData();
        },
        getData() {
            axios.get('/ajax/admin/report/activity-logs-data', {
                params: {
                    from_date: this.formatDate(this.dateRange.startDate),
                    to_date: this.formatDate(this.dateRange.endDate),
                    search: this.searchQuery
                }
            })
                .then(response => {
                    this.activity_logs = response.data;
                    this.activity_logs.forEach(log => {
                        log.created_at = moment(log.created_at).format('ddd MMM D, YYYY h:mm A');
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        },
        changePage(page) {
            this.currentPage = page;
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
    },

}

</script>