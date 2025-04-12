<template>
    <div>
        <b-modal id="addAdminFormModal" title="Add Admin" :hide-footer="true">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" v-model="admin.first_name" required/>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" v-model="admin.last_name" required/>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" v-model="admin.email" required/>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" v-model="admin.password" required/>
            </div>
            <button class="btn btn-primary" @click="addAdmin">Add</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            admin: {
                first_name: '',
                last_name: '',
                email: '',
            }
        }
    },
    methods: {
        addAdmin() {
            self = this;

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Admin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Save",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise(function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/dropdown/admin/create",
                            data: self.admin,
                            config: { headers: { "Content-Type": "application/json" } },
                        }).then(function (response) {
                            if(response.data.success) {
                                Swal.fire({
                                    title: response.data.message,
                                    text: "",
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "Okay",
                                }).then((result) => {
                                    self.$bvModal.hide("addAdminFormModal");
                                    self.$emit("add-new-admin");
                                    self.clearForm();
                                });
                            } else {
                                Swal.fire({
                                    title: response.data.message,
                                    text: "",
                                    icon: "error",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                });
                            }
                        }).catch(function (response) {
                            if (response.response.status === 422) {
                                var key = Object.keys(response.response.data.errors)[0];
                                var errorMessage = response.response.data.errors[key][0];
                                Swal.fire({
                                    title: errorMessage,
                                    text: "",
                                    icon: "error",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                });
                            }
                        });
                    });
                },
                allowOutsideClick: false,
            }).then((result) => {
                if(!result.value) {

                }
            });
        },
        clearForm() {
            this.admin.first_name = '';
            this.admin.last_name = '';
            this.admin.email = '';
            this.admin.password = '';
        }
    }
}
</script>