<template>
    <div>
        <add-mode-of-payment-form-modal-component @add-new-mode-of-payment="refreshAjaxUrl"/>
        <button v-if="hasPermission.mop_create" class="btn btn-primary" @click="addModeOfPayment">Add</button>
        <update-mop-modal-component :mop="mop" @update-mop="refreshAjaxUrl" />
        <hr>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
        />
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
        let buttons = [];
        if (this.hasPermission.mop_update) {
            buttons.push({ name: "Update", method: "updateMOP" });
        }
        return {
            ajaxUrl: "",
            refresh: 0,
            columns: [
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
                { name: "Name", field: "name", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "Actions",
                    method: 'viewMOP',
                    type: "success",
                    kind: "group",
                    buttons: buttons,
                },
            ],
            mop: {},//passed the mop to the editmopmodalcomponent
        }
    },
    mounted() {
        var app = this;
        app.refreshAjaxUrl();
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.mop_update) {
                return this.buttons;
            }
            return [];
        }
    },
    methods: {
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/dropdown/mode-of-payment/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        addModeOfPayment() {
            this.$bvModal.show("addModeOfPaymentFormModal");
        },
        onButtonClick(method, object){
            if(method === 'updateMOP' ){
                this.mop = object.item;
                this.$bvModal.show('updateMOPModal')
            }

        }
    }
}
</script>