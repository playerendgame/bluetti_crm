<template>
    <div>
        <dropdown-add-city-component @refreshAjaxUrl="refreshAjaxUrl"/>
        <dropdown-update-city-component :city="city" @refreshAjaxUrl="refreshAjaxUrl"/>
        <button class="btn btn-primary" v-if="hasPermission.city_create" @click="createCity">Add</button>
        <hr>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"/>
    </div>
</template>

<script>
export default {
    props:{
        hasPermission:{
            type: Object,
            required: true
        }
    },
    data() {
        let buttons = [];
        if (this.hasPermission.city_update) {
            buttons.push({ name: "Edit", method: "editCity" });
        }
        return {
            ajaxUrl: "",
            refresh: 0,
            columns: [
                { name: "Name", field: "name", sortable: true, show: true },
                { name: "Province", field: "province_name", sortable: true, show: true },
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
            city: null,
        }
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.city_update) {
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
            let url = "/ajax/admin/dropdown/city/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        onButtonClick(method, object) {
            if (method === "editCity") {
                this.city = object.item;
                this.$bvModal.show("updateCity");
            }
        },
        createCity(){
            this.$bvModal.show('addCity');
        }
    }
}
</script>