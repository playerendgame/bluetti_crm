<template>
    <div>
        <b-modal id="addRetailStoreFormModal" :hide-footer="true" title="Add Store">
            <div class="form-group">
                <label>Name</label>
                <input type="name" class="form-control" v-model="store.name"/>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" v-model="store.is_active">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <buttton class="btn btn-primary" @click="addStore">Add</buttton>
        </b-modal>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            store: {
                name: "",
                is_active: 1,
            }
        }
    },
    methods: {
        addStore() {
            self = this;

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add Store?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Add",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise(function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/retails/dropdown/store/create",
                            data: self.store,
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
                                    self.$bvModal.hide("addRetailStoreFormModal");
                                    self.$emit('add-retail-store');
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