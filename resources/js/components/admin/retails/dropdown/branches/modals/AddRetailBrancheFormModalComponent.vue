<template>
    <div>
        <b-modal id="addRetailBranchFormModal" :hide-footer="true" title="Add Branch">
            <div class="form-group">
                <label>Name</label>
                <input type="name" class="form-control" v-model="branch.name" />
            </div>
            <div class="form-group">
                <label>Store</label>
                <select class="form-control" v-model="branch.store_id">
                    <option value="">--Select Store--</option>
                    <option v-for="store in stores" :key="store.id" :value="store.id">
                        {{ store.name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" v-model="branch.is_active">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button class="btn btn-primary" @click="addBranch">Add</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            branch: {
                name: '',
                store_id: '',
                is_active: 1,
            },
            stores: [],
        }
    },
    mounted() {
        this.refreshStores();
    },
    methods: {
        refreshStores() {
            var self = this;
            axios.get("/ajax/admin/retails/dropdown/store/api").then(function (resp) {
                self.stores = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Store");
            });
        },
        addBranch() {
            self = this;

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add Branch?",
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
                            url: "/ajax/admin/retails/dropdown/branch/create",
                            data: self.branch,
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
                                    self.$bvModal.hide("addRetailBranchFormModal");
                                    self.$emit('add-retail-branch');
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