<template>
    <div>
        <b-modal id="addTargetFormModal" :hide-footer="true" title="Add Target">
            <div class="form-group">
                <label>Month</label>
                <input type="month" class="form-control" v-model="target.date"/>
            </div>
            <div class="form-group">
                <label>Sales Target</label>
                <input type="number" class="form-control" v-model="target.sales_target" />
            </div>
            <button class="btn btn-primary" @click="addTarget">Add</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            target: {
                date: '',
                sales_target: 0,
            }
        }
    },
    methods: {
        addTarget() {
            self = this;

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Target?",
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
                            url: "/ajax/admin/dropdown/target/create",
                            data: self.target,
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
                                    self.$bvModal.hide("addTargetFormModal");
                                    self.$emit("add-target");
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
        }
    }
}
</script>