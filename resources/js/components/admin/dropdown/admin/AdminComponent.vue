<template>
    <div>
        <add-admin-form-modal-component @add-new-admin="refreshAjaxUrl"/>
        <update-role-admin-form-modal-component :admin="admin" @update-admin-role-permission="refreshAjaxUrl"/>
        <button v-if="hasPermission.admin_create" class="btn btn-primary" @click="addNewAdmin">Add</button>
        
        <button class="btn btn-primary" @click="showDisabledAccountsModal">Disabled Accounts</button>
        
        <edit-admin-form-modal-component :admin="admin" @update-admin="handleUpdateAdmin"/>
        <hr>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"/>
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
    data() {
        let buttons = [];

        if (this.hasPermission.admin_update) {
            buttons.push({ name: "Impersonate", method: "impersonateAdmin"});
            buttons.push({ name: "Edit Details", method: "editAdminDetails"});
            buttons.push({ name: "Update Role", method: 'updateRoleAdmin' })
        }

        if (this.hasPermission.admin_delete) {
            buttons.push({ name: "Delete", method: 'deleteAdmin'});
        }
        return {
            ajaxUrl: "",
            refresh: 0,
            admin: null,
            columns: [
                { name: "First Name", field: "first_name", sortable: true, show: true },
                { name: "Last Name", field: "last_name", sortable: true, show: true },
                { name: "Email", field: "email", sortable: true, show: true },
                { name: "Admin Role", field: "admin_role", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: 'View',
                    methods: '',
                    type: "success",
                    kind: 'group',
                    buttons: buttons,
                    // buttons: [
                    //     { name: "Impersonate", method: 'impersonateAdmin' },
                    //     { name: "Edit Details", method: 'editAdminDetails' },
                    //     { name: "Update Role", method: 'updateRoleAdmin' },
                    //     { name: "Delete", method: 'deleteAdmin' },
                    // ]
                }
            ],
            hasAdminRole: false  
        }
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.admin_update) {
                return this.buttons;
            }

            return [];
        }
    },
    mounted() {
        this.refreshAjaxUrl();
        this.checkAdminRole();  
    },
    methods: {
        addNewAdmin() {
            this.$bvModal.show('addAdminFormModal');
        },
        showDisabledAccountsModal(){
            this.$bvModal.show('disabledAccountsModal');
        },
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/dropdown/admin/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        onButtonClick(method, object) {
            if (method === 'updateRoleAdmin') {
                this.admin = object.item;
                this.$bvModal.show('updateRoleAdminFormModal');
            } else if (method === 'impersonateAdmin') {
                window.location.href = "/admin/impersonate/" + object.item.id;
            } else if (method === 'deleteAdmin') {
                this.deleteAdmin(object.item.id);
            } else if (method === 'editAdminDetails'){
                this.admin = {...object.item};
                this.$bvModal.show('editAdminFormModal');
            }
        },
        deleteAdmin(adminId) {
            const self = this;

            Swal.fire({
                title: "Delete",
                text: "Are you sure you want to delete this admin account?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Delete",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return axios.delete(`/ajax/admin/dropdown/admin/delete/${adminId}`)
                        .then(response => {
                            if(response.data.success) {
                                Swal.fire('Deleted', 'Admin Account has been deleted.', 'success');
                                self.refreshAjaxUrl();
                            } else {
                                Swal.fire('Failed', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred while deleting this admin account.', 'error');
                        });
                }
            });
        },
        handleUpdateAdmin() {
            this.refreshAjaxUrl();
        }
    },
    watch: {
        '$auth.user.roles': {
            handler(newRoles, oldRoles) {
                this.checkAdminRole();
            },
            deep: true
        }
    }
}
</script>
