<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="buttons">
                    <button class="btn btn-primary" @click="addLink">Add Link</button>
                    <add-qr-link-component-modal @refresh-link-data-table="refreshAjaxUrl" />
                </div> 
            </div>
            <hr>
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
                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Date</th>
                                        <th>Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(link, index) in sortedPaginatedLinks" :key="link.id">
                                        <td>
                                            <button class="btn btn-sm btn-success" @click="openGeneratedLink(link.id)">View Generated QR</button>
                                            <view-generated-link :link="link" :linkId="link.id" />
                                        </td>
                                        <td>{{ link.created_at }}</td>
                                        <td>{{ link.link }}</td>
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
                    Showing {{ startIndex }} - {{ endIndex }} of {{ links.length }} entries
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
    </div>
</template>
<script>
import DateRangePicker from "vue2-daterange-picker";
import moment from 'moment';

export default{
    components: {
        DateRangePicker,
        moment
    },

    data(){
    
        var endDate = new Date();
        var startDate = new Date(endDate.getFullYear(), endDate.getMonth(), 1);
        return {
            dateRange: {
                startDate: startDate,
                endDate: endDate,
            },
            links:[],
            currentSortField: null,
            currentPage: 1,
            linksPerPage: 5,
        }
        
    },
    computed: {
        sortedPaginatedLinks() {
            const startIndex = (this.currentPage - 1) * this.linksPerPage;
            const endIndex = startIndex + this.linksPerPage;
            return this.links.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(startIndex, endIndex);
        },
        totalPages() {
            return Math.ceil(this.links.length / this.linksPerPage);
        },
        pageRange() {
            const start = 1;
            const end = this.totalPages;
            return `${start} > ${end}`;
        },
        startIndex() {
            return (this.currentPage - 1) * this.linksPerPage + 1;
        },
        endIndex() {
            const end = this.currentPage * this.linksPerPage;
            return end > this.links.length ? this.links.length : end;
        },
    },
    mounted(){
        this.getData();
    },
    methods: {
        addLink(){
            this.$bvModal.show('addQRLink');
        },
        openGeneratedLink(linkId){
            this.$bvModal.show(`view-generated-link-${linkId}`);
        },
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
            axios.get('/ajax/admin/marketing/fetch-qr-links', {
                params: {
                    from_date: this.formatDate(this.dateRange.startDate),
                    to_date: this.formatDate(this.dateRange.endDate),
                }
            })
                .then(response => {
                    this.links = response.data;
                    this.links.forEach(log => {
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
        refreshAjaxUrl() {
            this.getData();
        },
    }

}

</script>