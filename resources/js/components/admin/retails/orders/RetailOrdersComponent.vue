<template>
    <div>
        <update-retail-order-form-modal-component :order="order" @update-retail-order="refreshAjaxUrl"/>
        <div class="row">
            <div class="col-md-12">
                <div class="justify-content-center float-right">
                    <button type="button" class="btn btn-secondary my-2 me-2" @click="showHideFilters">
                        Filter
                    </button>
                </div>
            </div>
        </div>
        <div class="row" v-if="showFilters">
            <div class="card custom-card">
                <div class="card-body">
                    <h5>Filters</h5>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Order From</label>
                                <input type="date" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Order To</label>
                                <input type="date" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <button class="btn btn-primary form-control">
                                Save Filters
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary form-control">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="buttons" tableID="orders-table"
                v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            />
        </div>
    </div>
</template>

<script>
export default{
    data: function() {
        return {
            ajaxUrl: "",
            refresh: 0,
            showFilters: false,
            order: null,
            columns: [
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
                { name: "Order Date", field: "date_order_s", sortable: true, show: true },
                { name: "Sales", field: "sales_name", sortable: true, show: true },
                { name: "Store", field: "store_s", sortable: true, show: true },
                { name: "Branch", field: "branch_s", sortable: true, show: true },
                { name: "Quantity", field: "count_orders", sortable: true, show: true },
                { name: "MSRP", field: "total_msrp", sortable: true, show: true },
                { name: "Commission %", field: "comms", sortable: true, show: true },
                { name: "Commission Amount", field: "comms_amount", sortable: true, show: true },
                { name: "Gross Profit", field: "gross_profit", sortable: true, show: true },
                { name: "Cogs Amount", field: "cogs_amount", sortable: true, show: true },
                { name: "Cogs %", field: "cogs_percentage", sortable: true, show: true },
                { name: "Net Profit", field: "net_profit", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View Order",
                    method: "viewOrder",
                    type: "success",
                    kind: "group",
                    buttons: [
                        { name: "Update", method: "updateOrder" }
                    ]
                }
            ]
        }
    },
    mounted() {
        this.refreshAjaxUrl();
    },
    methods: {
        showHideFilters() {
            this.showFilters = !this.showFilters;
        },
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/retails/order/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        onButtonClick(method, object) {
            if (method === "viewOrder") {
                window.open('/admin/retails/orders/' + object.item.id);
            } else if (method === "updateOrder") {
                this.order = object.item;
                this.$bvModal.show("updateRetailOrderFormModal");
            }
        }
    }
}
</script>