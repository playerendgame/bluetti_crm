<template>
    <div>
        <add-courier-form-modal-component @add-new-courier="refreshAjaxUrl"/>
        <update-courier-form-modal-component :courier="courier" @update-courier="refreshAjaxUrl" />
        <button v-if="hasPermission.courier_create" class="btn btn-primary" @click="addCourier">Add</button>
        <hr>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
        />
    </div>
</template>

<script>
import Swal from 'sweetalert2';

export default {
    props: {
        hasPermission: {
            type: Object,
            required: true,
        }
    },
    data: function() {
        let buttons = [];
        if (this.hasPermission.courier_update) {
            buttons.push({ name: "Update", method: "updateCourier" });
        }
        if (this.hasPermission.courier_delete) {
            buttons.push({ name: "Delete", method: 'deleteCourier'});
        }
        return {
            refresh: 0,
            ajaxUrl: "",
            courier: null,
            columns: [
                { name: "Name", field: "name", sortable: true, show: true },
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
                       // { name: "Update", method: "updateCourier" },
                       // { name: "Delete", method: "deleteCourier" }
                  //  ]
                }
            ],
            attribution: null,
        }
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.courier_update) {
                return this.buttons;
            }else if(this.hasPermission.courier_delete){
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
            let url = "/ajax/admin/dropdown/courier/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        addCourier() {
            this.$bvModal.show("addCourierFormModal");
        },
        onButtonClick(method, object) {
            if (method === "updateCourier") {
                this.courier = object.item;
                this.$bvModal.show("updateCourierFormModal");
            }else if(method === "deleteCourier"){

                this.deleteCourier(object.item.id)

            }
        },

      deleteCourier(courierId){

        const self = this;

        Swal.fire({
            title: "Delete",
            text: 'Do you really want to delete this courier?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: "#8ad919",
            cancelButtonColor: "#d33",
            confirmButtonText: "Delete",
            showLoaderOnConfirm: true,

            preConfirm: function(){
                return axios.delete(`/ajax/admin/dropdown/courier/delete/${courierId}`)
                .then(response => {
                    if(response.data.success){
                        Swal.fire('Deleted', 'Courier has been deleted.', 'success');
                        self.refreshAjaxUrl();
                    }else{
                        Swal.fire('Failed', response.data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'An error occured while deleting this Courier.', 'error');
                });

            }

        })
        
    }

        

    }
}
</script>