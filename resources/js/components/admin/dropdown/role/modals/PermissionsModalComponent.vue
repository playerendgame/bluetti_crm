<template>
    <b-modal id="permissionsModal" title="Permissions" :hide-footer="true" size="lg" @show="fetchPermissions">
        <div class="row">
            <div class="col-12">
                <input class="form-control" type="text" v-model="role.name" disabled>
            </div>
        </div>
        <div class="row pt-3">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" @change="selectAll" v-model="allSelected"/> Select All</th>
                            <th>Permissions</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(permission, index) in permissions" :key="index">
                            <td><input type="checkbox" v-model="permission.checked" @change="updatePermission(permission)"/></td>
                            <td>{{ permission.name }}</td>
                            <td>{{ permission.description }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </b-modal>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    props: {
        role: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            permissions: [],
            allSelected: false
        };
    },
    methods: {
        fetchPermissions() {
            if (this.role && this.role.id) {
                axios.get(`/ajax/admin/role-permissions/${this.role.id}`)
                    .then(response => {
                        this.permissions = response.data.permissions.map(permission => {
                            return {
                                ...permission,
                                checked: response.data.selectedPermissions.includes(permission.id)
                            };
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching permissions:', error);
                    });
            }
        },
        selectAll() {
            if (this.allSelected) {
                this.permissions.forEach(permission => {
                    permission.checked = true;
                });
            } else {
                this.permissions.forEach(permission => {
                    permission.checked = false;
                });
            }
        },
        updatePermission(permission) {
            const roleId = this.role.id;
            const permissionId = permission.id;
            const checked = permission.checked;

            axios.post(`/ajax/admin/role-permissions/${roleId}/update/${permissionId}`, {
                checked: checked
            })
            .then(response => {
                console.log('Permission updated successfully:', response.data);

                Swal.fire({
                    icon: 'success',
                    title: 'Permission Updated',
                    text: 'Permission status has been updated successfully!',
                });
            })
            .catch(error => {
                console.error('Error updating permission:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Error Updating Permission',
                    text: 'There was an error updating permission status. Please try again.',
                });
            });
        },
        assignPermissions() {
            const roleId = this.role.id;
            const selectedPermissions = this.permissions.filter(permission => permission.checked).map(permission => permission.id);

            axios.post(`/ajax/admin/role-permissions/assign`, {
                role_id: roleId,
                permissions: selectedPermissions
            })
            .then(response => {
                console.log('Permissions assigned successfully:', response.data);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Permissions Assigned',
                    text: 'Permissions have been assigned to the role successfully!',
                });

                this.fetchPermissions();

            })
            .catch(error => {
                console.error('Error assigning permissions:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Error Assigning Permissions',
                    text: 'There was an error assigning permissions. Please try again.',
                });
            });
        },
    },
    watch: {
        role: {
            handler: function(newRole, oldRole) {
                if (newRole && newRole.id !== oldRole.id) {
                    this.fetchPermissions();
                }
            },
            deep: true
        }
    }
};
</script>
