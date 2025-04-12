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
                        v-model="dateMonthRange"
                        @update="updateValues"
                        @toggle="checkOpen"
                        :time-picker-increment="1"
                        :time-picker-seconds="true"
                        :linkedCalendars="true"
                    ></date-range-picker>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <div class="card custom-card">
                    <div class="card-body">
                    </div>
                    <div class="table-responsive">
                        <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Gross <br> Sales</th>
                                    <th>Actual <br> Sales</th>
                                    <th>Cogs</th>
                                    <th>% COGS <br> Vat INC</th>
                                    <th>No. of <br> Orders</th>
                                    <th>Daily Average <br> No. Order</th>
                                    <th>AOV</th>
                                    <th>Daily <br> Average Sales</th>
                                    <th>Sales Lag</th>
                                    <th>Sales Target</th>
                                    <th>No. of Days</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Total</th>
                                    <th>{{ data.total_gross_sales }}</th>
                                    <th>{{ data.total_actual_sales }}</th>
                                    <th>{{ data.total_cogs }}</th>
                                    <th>{{ data.cogs_vat }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr v-for="(table, index) in data.table" :key="table.id">
                                    <td>{{ table.month }}</td>
                                    <td>{{ table.gross_sales }}</td>
                                    <td>{{ table.actual_sales }}</td>
                                    <td>{{ table.cogs }}</td>
                                    <td>{{ table.conversion }}</td>
                                    <td>{{ table.orders }}</td>
                                    <td>{{ table.daily_ave_order }}</td>
                                    <td>{{ table.aov }}</td>
                                    <td>{{ table.daily_ave_sales }}</td>
                                    <td>{{ table.sales_lag }}</td>
                                    <td>{{ table.sales_target }}</td>
                                    <td>{{ table.count_days }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr> 
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
            <h4>Target Delivery Date</h4>
            <hr>
            <dashboard-target-date-modal :selected-order="selectedOrder" />
            <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="buttons" tableID="orders-table"
                v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
                @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
            />

            <hr>
            <div class="col-md-12 pt-3">
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
                        v-model="dateRangeDispatch"
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
            <h4>Dispatch List</h4>
            <div class="row pb-2">
                <div class="col-md-6">
                    <button class="btn btn-primary" @click="showFilter">Filter</button>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <button class="btn btn-success " @click="printDispatchList">Print</button>
                    <button class="btn btn-warning ml-2" @click="saveAsJpg">Save as JPG</button>
                </div>
            </div>
            <div class="row" v-if="courierFilterDisabled">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <label class="form-label">Select Courier</label>
                            <select class="form-select" v-model="selectedCourier">
                                <option :value="0">All</option>
                                <option v-for="courier in couriers" :value="courier.id" :key="courier.id">
                                    {{ courier.name }}
                                </option>
                            </select>
                            <div class="button d-flex pt-3">
                                <button class="btn btn-success" @click="filterByCourier">Save Filter</button>
                                <button class="btn btn-warning ml-2" @click="clearFilter">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <datatable-component class="dispatchListTable" ref="datatableComponent" :fetch-url="dispatchAjaxUrl" :columns="dispatchColumns" :buttons="dispatchButtons" tableID="orders-table"
                v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
                @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
            />
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
import { start } from "@popperjs/core";

import html2canvas from "html2canvas";
// Init plugin
Vue.use(Loading);
export default {
    components: {
        DateRangePicker,
        html2canvas
    },
    data: function() {
        var currentDate = new Date();
        var endDate = currentDate.setDate(currentDate.getDate() + 1);
        var startDate = endDate;

        var currentMonth = new Date();
        var startMonth = new Date(currentDate.getFullYear(), 0);
        return {
               // Other data properties as per your implementation
            selectedOrder: null,
            data: {},
            dateRange: {
                startDate: startDate,
                endDate: endDate,
            },
            dateRangeDispatch:{
                startDate: startDate,
                endDate: endDate,
            },
            dateMonthRange: {
                startDate: startMonth,
                endDate: currentMonth,
            },
            ajaxUrl: "",
            dispatchAjaxUrl: "",
            refresh: 0,
            couriers: [],
            selectedCourier: 0,
            courierFilterDisabled: false,
            columns: [
                { name: "Order Date", field: "order_date_s", sortable: true, show: true },
                { name: "Target Delivery Date", field: "target_delivery_date_s", sortable: true, show: true },
                { name: "Order #", field: "order_number", sortable: true, show: true },
                { name: "Customer", field: "customer_name", sortable: true, show: true },
                { name: "Total", field: "total_price", sortable: true, show: true },
                { name: "Payment Status", field: "mark_as_paid_s", sortable: true, show: true },
                { name: "Items", field: "items", sortable: true, show: true },
            ],
            dispatchColumns:[
                { name: "Order Date", field: "order_date", sortable: true, show: true },
                { name: "Order #", field: "order_number", sortable: true, show: true },
                { name: "Courier", field: "order_courier", sortable: true, show: true },
                { name: "Customer Name", field: "customer_name", sortable: true, show: true },
                { name: "Complete Address", field: "complete_address", sortable: true, show: true },
                { name: "Contact Number", field: "contact_number", sortable: true, show: true },
                { name: "Product", field: "product_name", sortable: true, show: true },
                { name: "Amount", field: "total_price", sortable: true, show: true },
                { name: "Mode Of Payment", field: "mode_of_payment_s", sortable: true, show: true },
                { name: "Payment Status", field: "payment_status", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View",
                    method: "viewTargetDate",
                    kind: "group",
                    type: "success",
                }
            ]
          
        }
    },
    mounted() {
        this.refreshAjaxUrl();
        this.refreshData();
        this.dispatchListApi();
        this.couriersApi();
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
            this.refresh++;
            let url = "/ajax/admin/dashboard/list?refresh=" + this.refresh + 
                "&start_date=" + this.formatDate(this.dateRange.startDate) +
                "&end_date=" + this.formatDate(this.dateRange.endDate) + "&";

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            this.fetchData(url)
                .then(() => {
                    loader.hide();
                })
                .catch((error) => {
                    // Hide the loader in case of an error
                    loader.hide();
                    console.error('Error fetching data:', error);
                });

            this.ajaxUrl = url;
        },
        dispatchListApi(courierId = 0) {
            this.refresh++;
            let url = "/ajax/admin/dashboard/regular-list?refresh=" + this.refresh +
                "&start_date=" + this.formatDate(this.dateRangeDispatch.startDate) +
                "&end_date=" + this.formatDate(this.dateRangeDispatch.endDate);

            if (courierId > 0) {
                url += "&courier_id=" + courierId;
            }

            url += "&page=1"; 

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            this.fetchData(url)
                .then(() => {
                    loader.hide();
                })
                .catch((error) => {
                    loader.hide();
                    console.error('Error fetching data:', error);
                });

            this.dispatchAjaxUrl = url;
        },
        filterByCourier() {
            this.dispatchListApi(this.selectedCourier);
        },
        couriersApi(){
            axios.get('/ajax/admin/dashboard/couriers')
            .then(response => {
                this.couriers = response.data.data
            })
            .catch(error => {
                console.error(error);
            })
        },
        refreshData() {
            var self = this;

            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/dashboard/summary?";

            if (this.dateMonthRange.startDate != null && this.dateMonthRange.endDate != null) {
                url +=
                "start_date=" +
                this.formatDate(this.dateMonthRange.startDate) +
                "&end_date=" +
                this.formatDate(this.dateMonthRange.endDate);
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
        fetchData(url) {
            return new Promise((resolve, reject) => {
                // Replace with actual AJAX request
                setTimeout(() => {
                    // Simulate a successful response
                    resolve();
                }, 1000); // Simulate a delay for the AJAX request
            });
        },
        updateValues() {
            this.refreshAjaxUrl();
            this.refreshData();
            this.dispatchListApi();
        },
        onButtonClick(method, object) {
            if(method === 'viewTargetDate'){
                this.selectedOrder = object.item;
                this.$bvModal.show("viewTargetDateModal");
            }
        },
        showFilter(){
            this.courierFilterDisabled = !this.courierFilterDisabled;
        },
        clearFilter(){
            this.selectedCourier = 0;
            this.dispatchListApi();
        },
        generateDocNumber() {
            // const currentDate = new Date();
            // Extract the day from startDate
            const formattedDate = this.formatDate(this.dateRangeDispatch.startDate);
            if (!formattedDate) {
                console.error("Invalid formatted date");
                return null;
            }

            const day = formattedDate.split('-')[2];
            const month = formattedDate.split('-')[1];
            const year = formattedDate.split('-')[0];
            const docNumber = `RDL${month}${day}${year}`;
            return docNumber;
        },
        printDispatchList() {
            const formattedDate = this.formatDate(this.dateRangeDispatch.startDate);
            if (!formattedDate) {
                console.error("Invalid formatted date");
                return null;
            }

            const day = formattedDate.split('-')[2];
            const month = formattedDate.split('-')[1];
            const year = formattedDate.split('-')[0];
            const dispatchListContainer = document.querySelector('.dispatchListTable');
            const currentDateStr = `${month}/${day}/${year}`;

            // Check if the "Release WH" column already exists
            let existingReleaseWHHeader = false;
            const headerCells = dispatchListContainer.querySelectorAll('th');
            headerCells.forEach(cell => {
                if (cell.textContent.trim() === 'Release WH') {
                    existingReleaseWHHeader = true;
                }
            });

            // If the column doesn't exist, add it
            if (!existingReleaseWHHeader) {
                const checkboxHeaderCell = document.createElement('th');
                checkboxHeaderCell.innerHTML = 'Release WH';
                dispatchListContainer.querySelector('thead tr').appendChild(checkboxHeaderCell);

                const rows = dispatchListContainer.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const checkboxCell = document.createElement('td');
                    checkboxCell.innerHTML = '<input type="checkbox">';
                    row.appendChild(checkboxCell);
                });
            }

            const win = window.open();

            // The Print Layout
            win.document.write(`
                <html>
                <head>
                    <style>
                        @media print {
                            @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

                            @page {
                                size: landscape;
                                margin: 0;
                            }

                            body {
                                margin: 0;
                                padding: 0;
                                font-family: 'Poppins', sans-serif;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }

                            .container {
                                width: 100%;
                                padding: 10px;
                                box-sizing: border-box;
                            }

                            .header {
                                text-align: center;
                            }

                            .sub-infos {
                                padding-bottom: 10px;
                            }

                            .table {
                                width: 100% !important;
                                border-collapse: collapse;
                                table-layout: fixed;
                            }

                            .table th, .table td {
                                padding: 8px;
                                text-align: center;
                                font-size: 12px;
                                word-wrap: break-word;
                            }

                            .table th {
                                font-weight: 800;
                            }

                            input[type="checkbox"] {
                                width: 15px;
                                height: 15px;
                                display: inline-block;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }

                            .signatures-container {
                                display: flex;
                                justify-content: space-between;
                                margin-top: 20px;
                            }

                            .signatures-container .row {
                                width: 100%;
                                display: flex;
                                justify-content: space-between;
                            }

                            .signatures-container .row div {
                                width: 30%;
                            }

                            .signatures-container .row span {
                                display: block;
                                border-bottom: 1px solid black;
                                width: 100%;
                            }

                            /* Hide unnecessary elements */
                            .select2, .d-inline-flex, .dataTables_info, .pagination, #example1_filter {
                                display: none !important;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header text-center">
                            <h2>Regular Dispatch List</h2>
                        </div>

                        <div class="sub-infos">
                            <b>
                                Date: ${currentDateStr} <br>
                                Doc #: ${this.generateDocNumber()}
                            </b>
                        </div>
                        <hr>

                        <div class="table">
                            ${dispatchListContainer.innerHTML}
                        </div>

                        <br><br><br>

                        <div class="signatures-container">
                            <div class="row">
                                <div class="date-requested">
                                    <div class="signature-contents">
                                        <div class="request-by">
                                            <span>Requested By Date:</span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="approved-by-date">
                                    <div class="signature-contents">
                                        <div class="request-by">
                                            <span>Approved By Date:</span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="released-by-date">
                                    <div class="signature-contents">
                                        <div class="request-by">
                                            <span>Released By Date:</span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            `);

            win.document.close();
            win.print();
            win.close();
        },
        // printDispatchList(){
        //     const dispatchListContainer = document.querySelector('.dispatchListTable');
        //     const currentDate = new Date();
        //     const year = currentDate.getFullYear();
        //     const month = currentDate.getMonth() + 1; 
        //     const day = currentDate.getDate();
        //     const currentDateStr = `${month}/${day}/${year}`;
           
        //     const checkboxHeaderCells = dispatchListContainer.querySelectorAll('th:last-child');
        //     checkboxHeaderCells.forEach(cell => cell.remove());
        //     const checkboxCells = dispatchListContainer.querySelectorAll('td:last-child');
        //     checkboxCells.forEach(cell => cell.remove());

        //     const checkboxHeaderCell = document.createElement('th');
        //     checkboxHeaderCell.innerHTML = 'Release WH';
        //     dispatchListContainer.querySelector('thead tr').appendChild(checkboxHeaderCell);

        //     const rows = dispatchListContainer.querySelectorAll('tbody tr');
        //     rows.forEach(row => {
        //         const checkboxCell = document.createElement('td');
        //         checkboxCell.innerHTML = '<input type="checkbox">';
        //         row.appendChild(checkboxCell);
        //     });

        //     const win = window.open();
            
        //     //The Print Layout
        //     win.document.write(`
        //         <html>
        //         <head>
                    
        //             <style>
        //             @media print {
        //                 @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        //                 @page {
        //                     size: landscape;
        //                 }

        //                 .dispatchListTable, table{
        //                     width: 1px !important;
        //                 }

        //                 .select2{
        //                     display: none;
        //                 }

        //                 .d-inline-flex{
        //                     display: none;
        //                 }

        //                 .dataTables_info{
        //                     display: none;
        //                 }


        //                 .pagination{
        //                     display: none;
        //                 }

        //                 .header{
        //                     text-align: center !important;
        //                 }

        //                 .sub-infos{
        //                     padding-bottom: 10px;
        //                 }

        //                 .table{
        //                     width: 1px !important; 
        //                     text-align: center;

        //                 }
                            
        //                 .table th{
        //                     padding-bottom: 20px !important;
        //                     font-weight: 800 !important;
        //                 }

        //                 #example1_filter{
        //                     display: none;
        //                 }

        //                 input[type="checkbox"] {
        //                     width: 15px;
        //                     height: 15px;
        //                     display: inline-block;
        //                     -webkit-print-color-adjust: exact;
        //                     print-color-adjust: exact;
        //                 }

        //             }                
                    
        //         </style>
        //         </head>
        //             <body>
        //                <div class="container">

        //                 <div class="header text-center">
        //                   <h2>Regular Dispatch List</h2>
        //                 </div>

        //                 <div class="sub-infos">
        //                     <b>
        //                         Date: ${currentDateStr} <br>
        //                         Doc #: ${this.generateDocNumber()}
        //                     </b>
        //                 </div>
        //                 <hr>

        //                 <div class="table">
        //                     <div class="container"> 
        //                         ${dispatchListContainer.innerHTML}
        //                     </div>
        //                 </div>
        //                 <br> <br> <br>
        //                 <div class="signatures-container">
        //                     <div class="row">
        //                         <div class="date-requested" style="width: 30%;" >
        //                             <div class="signature-contents">
        //                                 <div class="request-by d-flex" style="padding-top: 20px; display: flex !important;">
        //                                     <span class="ml-2">Requested By Date:</span>
        //                                     <span style="border-bottom: 1px solid black; width: 100%; display: block;"></span>                                    
        //                                 </div>
        //                             </div>
        //                         </div>

        //                          <div class="approved-by-date" style="width: 30%;" >
        //                             <div class="signature-contents">
        //                                 <div class="request-by d-flex" style="padding-top: 20px; display: flex !important;">
        //                                     <span class="ml-2">Approved By Date:</span>
        //                                     <span style="border-bottom: 1px solid black; width: 100%; display: block;"></span>                                    
        //                                 </div>
        //                             </div>
        //                         </div>

        //                          <div class="released-by-date" style="width: 30%;" >
        //                             <div class="signature-contents">
        //                                 <div class="request-by d-flex" style="padding-top: 20px; display: flex !important;">
        //                                     <span class="ml-2">Released By Date:</span>
        //                                     <span style="border-bottom: 1px solid black; width: 100%; display: block;"></span>                                    
        //                                 </div>
        //                             </div>
        //                         </div>
        //                     </div>
        //                 </div>

        //                 </div>
        //             </body>
        //         </html>
        //     `);
        //     win.print();
        //     win.close();
        // },
        saveAsJpg(){
            const dispatchList = document.querySelector('.dispatchListTable');

            const container = document.createElement('div');
            container.style.position = 'relative';
            container.style.width = '3500px';
            container.style.fontSize = '12px';

            const dispatchListClone = dispatchList.cloneNode(true);

            container.appendChild(dispatchListClone);

            document.body.appendChild(container);

            html2canvas(container, {
                allowTaint: true,
                useCORS: true,
                scale: 5
            }).then((canvas) => {
                const url = canvas.toDataURL();
                const a = document.createElement('a');
                a.href = url;
                a.download = 'clearance-form.jpg';
                a.click();
                container.remove();
            })
        },
    }
}
</script>