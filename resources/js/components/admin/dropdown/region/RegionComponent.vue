<template>
    <div>
        <dropdown-add-region-component @refreshAjaxUrl="refreshAjaxUrl"/>
        <dropdown-update-region-component :region="region" @refreshAjaxUrl="refreshAjaxUrl"/>
    
        <button class="btn btn-primary" v-if="hasPermission.region_create" @click="createRegion">Add</button>
        <hr>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"/>
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
    data() {
        let buttons = [];
        if (this.hasPermission.region_update) {
            buttons.push({ name: "Edit", method: "editRegion" });
        }
        return {
            ajaxUrl: "",
            refresh: 0,
            columns: [
                { name: "Name", field: "name", sortable: true, show: true },
                { name: "Status", field: "status", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View Details",
                    method: '',
                    type: "success",
                    kind: "group",
                    buttons: buttons,
                    // [
                       // { name: "Update", method: "updateProduct" },
                    //]
                }
            ],

            region: null
        }
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.region_update) {
                return this.buttons;
            }
            return [];
        }
    },
    mounted() {
        this.refreshAjaxUrl();
    },
    methods: {
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/dropdown/region/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        onButtonClick(method, object) {
            if (method === "editRegion") {
                this.region = object.item;
                this.$bvModal.show("updateRegion");
            }
        },
        createRegion(){
            this.$bvModal.show('addRegion');
        }
    }
}
</script>