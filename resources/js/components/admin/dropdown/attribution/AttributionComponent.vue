<template>
    <div>
        <update-attribution-form-modal-component :attribution="attribution" @update-attribution="refreshAjaxUrl"/>
        <add-attribution-form-modal-component @add-new-attribution="refreshAjaxUrl"/>
        <button v-if="hasPermission.att_create" class="btn btn-primary" @click="addAttribution">Add</button>
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
        if (this.hasPermission.att_update) {
            buttons.push({ name: "Edit", method: "editAttribution" });
        }
        if (this.hasPermission.att_delete) {
            buttons.push({ name: "Delete", method: 'deleteAttribution'});
        }
        return {
            refresh: 0,
            ajaxUrl: "",
            columns: [
                { name: "Name", field: 'name', sortable: true, show: true },
                { name: "Category", field: "category_name", sortable: true, show: true },
                { name: "Campaign Name", field: "campaign_name", sortable: true, show: true },
                { name: "Distribution Channel", field: "distribution_channel_name", sortable: true, show: true },
                { name: "Is Active", field: "is_active_s", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View Details",
                    methods: "",
                    kind: "group",
                    type: "success",
                    buttons: buttons
                    //[
                        //{ name: "Edit", method: "editAttribution" },
                        //{ name: "Delete", method: "deleteAttribution" }
                    //]
                }
            ],
            attribution: null,
        }
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.att_update) {
                return this.buttons;
            }else if(this.hasPermission.att_delete){
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
            let url = "/ajax/admin/dropdown/attribution/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        addAttribution() {
            this.$bvModal.show("addAttributionFormModal");
        },
        onButtonClick(method, object) {
            if (method === "editAttribution") {
                this.attribution = object.item;
                this.$bvModal.show("updateAttributionFormModal");
            }else if(method === 'deleteAttribution'){
                this.deleteAttribution(object.item.id)

            }
        },
        deleteAttribution(attributionId) {
            const self = this;

            Swal.fire({
                title: "Delete",
                text: "Are you sure you want to delete this Attribution?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Delete",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return axios.delete(`/ajax/admin/dropdown/attribution/delete/${attributionId}`)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire('Deleted', 'Attribution has been deleted.', 'success');
                                self.refreshAjaxUrl();
                            } else {
                                Swal.fire('Failed', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error("Error deleting attribution:", error);
                            Swal.fire('Error!', 'An error occurred while deleting this attribution.', 'error');
                        });
                }
            });
        }

    }
}
</script>