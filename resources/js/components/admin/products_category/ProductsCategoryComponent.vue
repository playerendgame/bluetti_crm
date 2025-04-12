<template>
    <div>
        <add-products-category-component @refreshAjaxUrl="refreshAjaxUrl"/>

        <button class="btn btn-primary" @click="addCategory">Add</button>
        <hr>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"/>
    </div>
</template>

<script>
export default {
    data() {
        let buttons = [];

        return {
            ajaxUrl: "",
            refresh: 0,
            columns: [
                { name: "Name", field: "name", sortable: true, show: true },
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
            ],
        }
    },
    mounted() {
        this.refreshAjaxUrl();
    },
    methods: {
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/products/category/list?refresh=" + this.refresh + '&';
            this.ajaxUrl = url;
        },
        addCategory(){
            this.$bvModal.show('addProductCategory');
        }
    }
}
</script>