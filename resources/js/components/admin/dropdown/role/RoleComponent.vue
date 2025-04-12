<template>
    <div>
        <dropdown-update-role-form-modal-component :role="role" @update-role="refreshAjaxUrl" />
        <div class="dropdown-roles-button d-flex">
            <add-role-form-modal-component @add-new-role="refreshAjaxUrl" />
            <button v-if="hasPermission.role_create" class="btn btn-primary" @click="addRole">Add</button>
            &nbsp;  
            <button class="btn btn-primary" @click="showDisabledModal">Restore Role</button>
            <show-disabled-role-component @disabled-role="refreshAjaxUrl"/>
            &nbsp;  
            <button v-if="hasPermission.role_create" class="btn btn-primary" @click="showAddPermissionsModal">Add Permissions</button>
            <add-role-permission-component/>
        </div>
        <hr>

        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
        />
        <permissions-modal-component :role="role" />
     
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
        if (this.hasPermission.role_update) {
            buttons.push({ name: "Edit", method: "editRole" });
            buttons.push({  name: "Permissions", method: "permissionsRole"  })
        }
        if (this.hasPermission.role_delete) {
            buttons.push({ name: "Delete", method: 'deleteRole'});
        }
        return {
            role: null,
            ajaxUrl: '',
            refresh: 0,
            columns: [
                { name: "Role", field: 'name', sortable: true, show: true },
                { name: "Description", field: 'description', sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View",
                    method: "",
                    type: "success",
                    kind: "group",
                    buttons: buttons,
                    //buttons: [
                       // { name: "Edit", method: "editRole" },
                       // { name: "Permissions", method: "permissionsRole" },
                        //{ name: "Delete", method: "deleteRole" },
                   //]
                }
            ]
        }
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.role_update) {
                return this.buttons;
            }else if(this.hasPermission.role_delete){
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
            let url = "/ajax/admin/dropdown/role/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        addRole() {
            this.$bvModal.show('addRoleFormModal');
        },
        showDisabledModal(){
            this.$bvModal.show('restoreRoleModal')
        },
        showAddPermissionsModal(){
            this.$bvModal.show('addPermissionsModal') 
        },
        onButtonClick(method, object) {
            if (method === "editRole") {
                this.role = object.item;
                this.$bvModal.show("updateRoleFormModal");
            }else if(method === 'deleteRole'){
                this.deleteRole(object.item.id);
            }else if(method === 'permissionsRole'){
                this.role = object.item;
                this.$bvModal.show("permissionsModal");
            }
        },


        deleteRole(roleId){

            const self = this;

            Swal.fire({

                title: "Delete",
                text: "Are you sure you want to delete this Role?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Delete",
                showLoaderOnConfirm: true,
                
                preConfirm: function(){

                    return axios.delete(`/ajax/admin/dropdown/role/delete/${roleId}`)
                            .then(response => {

                                if(response.data.success){
                                   
                                    Swal.fire('Deleted', 'Role has been deleted.', 'success');
                                    // self.refreshAjaxUrl();

                                }else{

                                    Swal.fire('Failed', response.data.message, 'error');

                                }

                            })
                            .catch(error => {

                                Swal.fire('Error!', 'An error occured while deleting this role.', 'error');

                            });

                        
                }

            });

        }



    }

}
</script>