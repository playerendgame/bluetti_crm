<template>
    <div>
        <create-customer-form-modal-component @add-customer="refreshAjaxUrl"/>
        <div class="row">
            <div class="col-md-12">
                <button v-if="hasPermission.customer_create" class="btn btn-primary" @click="addCustomer">Add</button>
            </div>
            <br>
            <br>
            <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="buttons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
        />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        hasPermission: {
            type: Object,
            required: true,
        }
    },
    data: function() {
        return {
            refresh: 0,
            ajaxUrl: '',
            columns: [
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
                { name: "Name", field: 'name', sortable: true, show: true },
                { name: "Email", field: 'email', sortable: true, show: true },
                { name: "Phone Number", field: 'number', sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View Details",
                    method: "viewCustomer",
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
            let url = "/ajax/admin/customers/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        onButtonClick(method, object) {
            if (method === "viewCustomer") {
                window.open("/admin/customers/" + object.item.id);
            }
        },
        addCustomer() {
            this.$bvModal.show("addCustomerFormModal");
        }
    }
}
</script>