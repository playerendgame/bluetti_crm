<template>
    <div>
        <b-modal id="addCourierFormModal" title="Add Courier" :hide-footer="true">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="courier.name"/>
            </div>
            <div class="form-group">
                <label>Is Active</label>
                <select class="form-control" v-model="courier.is_active">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <button class="btn btn-primary" @click="addCourier">Save</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            courier: {
                name: '',
                is_active: 1,
            }
        }
    },
    methods: {
        addCourier() {
            self = this;
            
            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Courier?",
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
                            url: "/ajax/admin/dropdown/courier/create",
                            data: self.courier,
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
                                    self.$bvModal.hide("addCourierFormModal");
                                    self.$emit("add-new-courier");
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
            this.courier.name = '';
            this.courier.is_active = 1;
        }
    }
}
</script>