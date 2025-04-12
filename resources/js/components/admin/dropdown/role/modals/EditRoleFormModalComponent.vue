<template>
    <div>
        <b-modal id="updateRoleFormModal" title="Update Role" :hide-footer="true">
            <div class="form-group" v-if="role != null">
                <label>Name</label>
                <input type="text" class="form-control" v-model="role.name" />
            </div>
            <div class="form-group" v-if="role != null">
                <label>Description</label>
                <textarea class="form-control" v-model="role.description"></textarea>
            </div>
            <button class="btn btn-success" @click="updateRole">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        role: {
            type: Object,
            required: false,
            notes: "model",
        }
    },
    methods: {
        updateRole() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "Are you sure you want to update Role?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Update",
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/dropdown/role/update",
                            data: self.role,
                            config: { headers: { "Content-Type": "application/json" } },
                        })
                            .then(function (response) {
                                if (response.data.success) {
                                    Swal.fire({
                                        title: response.data.message,
                                        text: "",
                                        icon: "success",
                                        showCancelButton: false,
                                        confirmButtonColor: "#3085d6",
                                        confirmButtonText: "Okay",
                                    }).then((result) => {
                                        self.status = 0;
                                        self.$bvModal.hide("updateRoleFormModal");
                                        self.$emit("update-role");
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
                            })
                            .catch(function (response) {
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
                if (!result.value) {

                }
            });
        }
    }
}
</script>