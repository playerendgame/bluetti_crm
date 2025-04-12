<template>
    <div>
        <b-modal id="editAdminFormModal" title="Edit Admin Details" :hide-footer="true">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" v-model="admin.first_name" />
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" v-model="admin.last_name"/>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" v-model="admin.email"/>
            </div>
            <button class="btn btn-success" @click="updateAdmin">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        admin: {
            type: Object,
            default: () => ({
                first_name: '',
                last_name: '',
                email: '',
            })
        }
    },
    methods: {
        updateAdmin() {
            Swal.fire({
                title: "Update",
                text: "Are you sure you want to update this admin detail?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Update",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return axios.put(`/ajax/admin/dropdown/admin/update/${this.admin.id}`, this.admin)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire({
                                    title: "Updated",
                                    text: response.data.message,
                                    icon: "success",
                                    confirmButtonText: "Okay"
                                });
                                this.$bvModal.hide('editAdminFormModal');
                                this.$emit('update-admin');
                            } else {
                                Swal.fire({
                                    title: "Failed",
                                    text: response.data.message,
                                    icon: "error",
                                    confirmButtonText: "Okay"
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: "Error",
                                text: "An error occurred while updating the admin details.",
                                icon: "error",
                                confirmButtonText: "Okay"
                            });
                        });
                },
                allowOutsideClick: false
            });
        }
    }
}
</script>
