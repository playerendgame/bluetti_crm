<template>
    <div>
        <dropdown-add-distribution-channels-component @refreshAjaxUrl="refreshAjaxUrl"/>
        <dropdown-update-distribution-channels-component :distribution_channel="distribution_channel" @refreshAjaxUrl="refreshAjaxUrl"/>

        <div class="button">
            <button class="btn btn-primary" @click="addDistChannels">Add</button>
        </div>
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
        if (this.hasPermission.dist_channel_update) {
            buttons.push({ name: "Edit", method: "editDistChannel" });
        }
        return {
            product: null,
            ajaxUrl: "",
            refresh: 0,
            columns: [
                { name: "Name", field: "name", sortable: true, show: true },
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
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

            distribution_channel: null,
            
        }
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.dist_channel_update) {
                return this.buttons;
            }
            return [];
        }
    },
    mounted() {
        var app = this;
        app.refreshAjaxUrl();
    },
    methods: {
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/dropdown/distribution-channels/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        addDistChannels(){
            this.$bvModal.show('addDistributionChannels');
        },
        onButtonClick(method, object) {
            if (method === "editDistChannel") {
                this.distribution_channel = object.item;
                this.$bvModal.show("updateDistChannel");
            }
        },
    

    }
}
</script>