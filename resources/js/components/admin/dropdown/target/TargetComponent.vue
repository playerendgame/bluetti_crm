<template>
    <div>
        <dropdown-add-target-form-modal-component @add-target="refreshAjaxUrl"/>
        <button v-if="hasPermission.target_create" class="btn btn-primary" @click="addTarget">Add</button>
        <br>
        <br>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="buttons" tableID="orders-table"
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
        return {
            refresh: 0,
            ajaxUrl: "",
            columns: [
                { name: "Date", field: "date_s", sortable: true, show: true },
                { name: "Sales Target", field: "sales_target_s", sortable: true, show: true },
            ]
        }
    },
    mounted () {
        this.refreshAjaxUrl();
    },
    methods: {
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/dropdown/target/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        addTarget() {
            this.$bvModal.show("addTargetFormModal");
        }
    }
}
</script>