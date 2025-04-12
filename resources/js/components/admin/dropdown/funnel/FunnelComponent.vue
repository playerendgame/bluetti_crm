<template>
    <div>
        <add-funnel-form-modal-component @add-funnel="refreshAjaxUrl"/>
        <update-funnel-form-modal-component :funnel="funnel" @update-funnel="refreshAjaxUrl"/>
        <button v-if="hasPermission.funnel_create" class="btn btn-primary" @click="addFunnel">Add</button>
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
        if (this.hasPermission.funnel_update) {
            buttons.push({ name: "Update", method: "updateFunnel" });
        }
        return {
            ajaxUrl: "",
            refresh: 0,
            funnel: null,
            columns: [
                { name: "Name", field: 'name', sortable: true, show: true },
                { name: 'Attribution', field: 'attribution_name', sortable: true, show: true },
                { name: "Campaign Name", field: "campaign_name", sortable: true, show: true },
                { name: "Is Active", field: "is_active_s", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View",
                    method: "",
                    kind: "group",
                    type: "success",
                    buttons: buttons,
                    //[
                        //{ name: "Update", method: "updateFunnel" },
                   // ]
                }
            ]
        }
    },
    mounted() {
        this.refreshAjaxUrl();
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.funnel_update) {
                return this.buttons;
            }
            return [];
        }
    },
    methods: {
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/dropdown/funnel/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        addFunnel() {
            this.$bvModal.show("addFunnelFormModal");
        },
        onButtonClick(method, object) {
            if (method === "updateFunnel") {
                this.funnel = object.item;
                this.$bvModal.show("updateFunnelFormModal");
            }
        }
    }
}
</script>