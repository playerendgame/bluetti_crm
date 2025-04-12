<template>
    <div>
        <b-modal id="addRoleFormModal" title="Add Role" :hide-footer="true">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="role.name" />
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" v-model="role.description"></textarea>
            </div>
            <button class="btn btn-primary" @click="addRole">Add</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            role: {
                name: '',
                description: '',
            }
        }
    },
    methods: {
        addRole() {
            self = this;
            
            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Role?",
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
                            url: "/ajax/admin/dropdown/role/create",
                            data: self.role,
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
                                    self.$bvModal.hide("addRoleFormModal");
                                    self.$emit("add-new-role");
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
            this.role.name = '';
            this.role.description = '';
        }
    }
}
</script>