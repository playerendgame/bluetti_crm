<template>
    <div>
        <dropdown-add-province-component @refreshAjaxUrl="refreshAjaxUrl" />
        <dropdown-update-province-component :province="province" @refreshAjaxUrl="refreshAjaxUrl"/> 
        <button class="btn btn-primary" v-if="hasPermission.province_create" @click="createProvince">Add</button>
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
        if (this.hasPermission.province_update) {
            buttons.push({ name: "Edit", method: "editProvince" });
        }
        return {
            ajaxUrl: "",
            refresh: 0,
            columns: [
                { name: "Name", field: "name", sortable: true, show: true },
                { name: "Region", field: "region_name", sortable: true, show: true },
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
            province: null,
        }
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.province_update) {
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
            let url = "/ajax/admin/dropdown/province/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        onButtonClick(method, object) {
            if (method === "editProvince") {
                this.province = object.item;
                this.$bvModal.show("updateProvince");
            }
        },
        createProvince(){
            this.$bvModal.show('addProvince');
        }
    }
}
</script>