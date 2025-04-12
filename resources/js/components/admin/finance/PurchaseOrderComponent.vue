<template>
    <div>
        <hr>
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
            refresh: 0,
            ajaxUrl: "",
            columns: [
                { name: "Series", field: "ref_code_s", sortable: true, show: true },
                { name: "Purchase Date", field: "purchase_date_s", sortable: true, show: true },
                { name: "Quantity", field: "quantity", sortable: true, show: true },
                { name: "Stocks Left", field: "stocks_left", sortable: true, show: true },
                { name: "Total Cost", field: "total_amount", sortable: true, show: true },
            ],
            buttons: [
                { 
                    name: "View Purchase Order",
                    method: "viewPurchaseOrder",
                    type: "success",
                }
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
            let url = "/ajax/admin/finance/purchase-order/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        onButtonClick(method, object) {
            if (method === "viewPurchaseOrder") {
                window.open("/admin/finance/purchase-order/" + object.item.id);
            }
        }
    }
}
</script>