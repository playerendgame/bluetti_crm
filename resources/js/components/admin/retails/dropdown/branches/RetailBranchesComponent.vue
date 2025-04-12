<template>
    <div>
        <add-retail-branch-form-modal-component @add-retail-branch="refreshAjaxUrl" />
        <button class="btn btn-primary" @click="addBranch">Add</button>
        <hr>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
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
                { name: "Name", field: "name", sortable: true, show: true },
                { name: "Store", field: "store_name", sortable: true, show: true },
                { name: "Status", field: "status", sortable: true, show: true },
            ],
        }
    },
    mounted() {
        this.refreshAjaxUrl();
    },
    methods: {
        addBranch() {
            this.$bvModal.show("addRetailBranchFormModal");
        },
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/retails/dropdown/branch/list?" + this.refresh+ "&";
            this.ajaxUrl = url;
        }
    }
}
</script>