<template>
    <div>
        <b-modal id="updateCourierFormModal" :hide-footer="true" title="Update Courier">
            <div class="form-group" v-if="courier != null">
                <label>Name</label>
                <input type="text" class="form-control" v-model="courier.name" />
            </div>
            <div class="form-group" v-if="courier != null">
                <label>Is Active</label>
                <select class="form-control" v-model="courier.is_active">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <button class="btn btn-success" @click="updateCourier">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        courier: {
            type: Object,
            required: false,
            notes: "model",
        }
    },
    methods: {
        updateCourier() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "Are you sure you want to update Courier?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Update",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise(function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/dropdown/courier/update",
                            data: self.courier,
                            config: { headers: {"Content-Type": "application/json"} },
                        })
                        .then(function(response) {
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
                                    self.$bvModal.hide("updateCourierFormModal");
                                    self.$emit('update-courier');
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