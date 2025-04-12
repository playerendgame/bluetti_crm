<template>
    <div>
        <b-modal id="addModeOfPaymentFormModal" title="Add Mode Of Payment" :hide-footer="true">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="mode_of_payment.name"/>
            </div>
            <button class="btn btn-primary" @click="addModeOfPayment">Add</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            mode_of_payment: {
                name: '',
            }
        }
    },
    methods: {
        addModeOfPayment() {
            self = this;

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Mode Of Payment?",
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
                            url: "/ajax/admin/dropdown/mode-of-payment/create",
                            data: self.mode_of_payment,
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
                                    self.$bvModal.hide("addModeOfPaymentFormModal");
                                    self.$emit("add-new-mode-of-payment");
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
            this.mode_of_payment.name = '';
        }
    }
}
</script>