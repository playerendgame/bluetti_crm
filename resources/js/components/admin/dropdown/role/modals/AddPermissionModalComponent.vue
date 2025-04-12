<template>
    <b-modal id="addPermissionsModal" title="Add Permissions" :hide-footer="true" size="lg">
        <div class="form-group">
            <label>Permission Name</label>
            <input type="text" class="form-control" v-model="name" />
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" v-model="description"></textarea>
        </div>
        <button class="btn btn-primary" @click="addRolePermission">Add</button>
    </b-modal>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            name: '',
            description: ''
        };
    },
    methods: {
        addRolePermission() {
            // Validate input
            if (!this.name || !this.description) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter both permission name and description.'
                });
                return;
            }

            axios.post(`/ajax/admin/role-permissions/add`, {
                name: this.name,
                description: this.description,
            })
            .then(response => {
                // Handle success response
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Role Permission Added',
                        text: 'Role permission has been successfully added.',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });

                    this.$emit('permission-added');

                    this.name = '';
                    this.description = '';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: response.data.message || 'Failed to add role permission.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding role permission.'
                });
                console.error('Error adding role permission:', error);
            });
        }
    }
};
</script>
