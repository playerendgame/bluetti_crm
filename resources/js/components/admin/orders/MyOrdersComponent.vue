<template>
    <div>
        <update-orders-form-modal-component :order="order" @update-order="refreshAjaxUrl" />
        <update-order-delivery-status-form-modal-component :order="order" @update-order-delivery-status="refreshAjaxUrl" />
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="buttons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
        />
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            ajaxUrl: "",
            refresh: 0,
            columns: [
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
                { name: "Order Date", field: "order_date_s", sortable: true, show: true },
                { name: "Customer Name", field: "customer_name", sortable: true, show: true },
                { name: "Attribution", field: "attribution_name", sortable: true, show: true },
                { name: "Quantity", field: "count_orders", sortable: true, show: true },
                { name: "Total Amount", field: "total_price", sortable: true, show: true },
                { name: "COGS", field: "cogs_s", sortable: true, show: true },
                { name: "% COGS", field: "percent_cogs", sortable: true, show: true },
                { name: "Contact #", field: "contact_number", sortable: true, show: true },
                { name: "Email", field: "email", sortable: true, show: true },
                { name: "Region", field: "region_id", sortable: true, show: true },
                { name: "Province", field: "province_id", sortable: true, show: true },
                { name: "City", field: "city_id", sortable: true, show: true },

                { name: "Address", field: "address", sortable: true, show: true },
                { name: "Target Delivery Date", field: "target_delivery_date_s", sortable: true, show: true },
                { name: "Status", field: "delivery_status_s", sortable: true, show: true },
                { name: "Dispatch Date", field: "dispatch_date_s", sortable: true, show: true },
                { name: "Delivered Date", field: "date_delivered_s", sortable: true, show: true },
                { name: "Admin Assign", field: "admin_name", sortable: true, show: true },
            ]
        }
    },
    mounted() {
        var app = this;
        app.refreshAjaxUrl();
    },
    methods: {
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/orders/my-orders?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        }
    }
}
</script>