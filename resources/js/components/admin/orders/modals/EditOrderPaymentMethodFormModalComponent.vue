<template>
    <div>
        <b-modal id="editOrderPaymentMethodFormModalComponent" title="Edit Order Payment Method" :hide-footer="true">
            <div class="form-group" v-if="currentOrderPaymentMethod != null">
                <label>Date Created</label>
                <input type="text" class="form-control" v-model="currentOrderPaymentMethod.created_at_s" disabled />
            </div>
            <div class="form-group" v-if="currentOrderPaymentMethod != null">
                <label>Payment Method</label>
                <select class="form-control" v-model="currentOrderPaymentMethod.payment_method_id">
                    <option v-for="(mode_of_payment, index) in mode_of_payments" :key="mode_of_payment.id" :value="mode_of_payment.id">
                        {{ mode_of_payment.name }}
                    </option>
                </select>
            </div>
            <div class="form-group" v-if="currentOrderPaymentMethod != null">
                <label>Amount</label>
                <input type="number" class="form-control" v-model="currentOrderPaymentMethod.amount"/>
            </div>
            <div class="form-group" v-if="currentOrderPaymentMethod != null">
                <label>Notes</label>
                <textarea class="form-control" v-model="currentOrderPaymentMethod.notes"></textarea>
            </div>
            <button class="btn btn-success" @click="updateOrderPaymentMethod">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        orderPaymentMethod: {
            type: Object,
            required: false,
            notes: "model",
        }
    },
    data: function() {
        return {
            currentOrderPaymentMethod: null,
        }
    },
    watch: {
        orderPaymentMethod: {
            handler: function (orderPaymentMethod) {
                this.currentOrderPaymentMethod = JSON.parse(JSON.stringify(orderPaymentMethod));
                this.refreshModeOfPaymentData();
            },
            immediate: true,
        },
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
        updateOrderPaymentMethod() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "are you sure you want to update Order Payment Method?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Update",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise (function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/orders/update-payment-method",
                            data: self.currentOrderPaymentMethod,
                            config: { headers: { "Content-Type" : "application/json" } },
                        }).then(function (response) {
                            if (response.data.success) {
                                Swal.fire({
                                    title: response.data.message,
                                    text: "",
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "Okay",
                                }).then((result) => {
                                    self.$emit("update-order-payment-method");
                                    self.$bvModal.hide("editOrderPaymentMethodFormModalComponent");
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
                            if (response.response.status == 422) {
                                var key = Object.keys(response.response.data.errors)[0];
                                var errorMessage = response.response.data.errors[key][0];
                                Swal.fire({
                                    title: errorMessage,
                                    text: "",
                                    icon: "error",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                }).then((result) => {
                                    
                                });
                            }
                        });
                    });
                },
            }).then((result) => {
                if (!result.isConfirmed) {
                }
            });
        }
    }
}
</script>