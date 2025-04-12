<template>
    <div>
        <b-modal id="addModeOfPaymentFormModal" title="Add Order Mode Of Payment" :hide-footer="true" size="lg">
            <div class="form-group">
                <label>Mode Of Payment</label>
                <select class="form-control" v-model="order_payment_method.payment_method_id">
                    <option value="">--Select Mode Of Payment--</option>
                    <option v-for="(mode_of_payment, index) in mode_of_payments" :key="mode_of_payment.id" :value="mode_of_payment.id">
                        {{ mode_of_payment.name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label>Amount</label>
                <input type="number" class="form-control" v-model="order_payment_method.amount"/>
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea class="form-control" v-model="order_payment_method.notes"></textarea>
            </div>
            <button class="btn btn-primary" @click="addPaymentMethod">Add</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        order: {
            type: Object,
            required: false,
            notes: "model",
        },
    },
    data: function() {
        return {
            mode_of_payments: [],
            order_payment_method: {
                payment_method_id: '',
                amount: 0,
                order_id: '',
            }
        }
    },
    watch: {
        order: {
            handler: function (order) {
                if (order != null) {
                    this.order_payment_method.order_id = this.order.id;
                }
            },
            immediate: true,
        }
    },
    mounted() {
        var app = this;
        app.refreshModeOfPaymentData();
    },
    methods: {
        refreshModeOfPaymentData() {
            var self = this;
            axios.get("/ajax/admin/dropdown/mode-of-payment/api").then(function (resp) {
                self.mode_of_payments = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Mode Of Payments");
            });
        },
        addPaymentMethod() {
            self = this;

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add Order Payment Method?",
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
                            url: "/ajax/admin/orders/add-payment-method",
                            data: self.order_payment_method,
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
                                    self.$emit("add-order-payment-method");
                                    self.$bvModal.hide("addModeOfPaymentFormModal");
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
            this.order_payment_method.order_payment_method_id = '';
            this.order_payment_method.amount = 0;
            this.order_payment_method.notes = "";
        }
    }
}
</script>