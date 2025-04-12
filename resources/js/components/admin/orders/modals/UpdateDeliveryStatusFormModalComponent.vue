<template>
    <div>
        <b-modal id="updateDeliveryStatusFormModal" title="Update Delivery Status" :hide-footer="true">
            <div class="form-group" v-if="order != null">
                <label>Order Date</label>
                <input type="text" class="form-control" v-model="order.order_date_s" disabled />
            </div>
            <div class="form-group" v-if="order != null">
                <label>Name</label>
                <input type="text" class="form-control" v-model="order.customer_name" disabled />
            </div>
            <div class="form-group" v-if="order != null">
                <label>Tracking Number</label>
                <input type="text" class="form-control" v-model="order.tracking_number" />
            </div>
            <div class="form-group" v-if="order != null">
                <label>Target Pick Up / Target Delivery Date</label>
                <input type="date" class="form-control" v-model="order.target_delivery_date" />
            </div>
            <div class="form-group" v-if="order != null">
                <label>Delivery Status</label>
                <select class="form-control" v-model="order.delivery_status">
                    <option value="0">Pending</option>
                    <option value="1">Shipped</option>
                    <option value="2">Delivered</option>
                    <option value="3">RTS</option>
                    <option value="4">Returned</option>
                    <option value="5">Out For Delivery</option>
                    <option value="6">Cancelled</option>
                </select>
            </div>
            <div class="form-group" v-if="order != null && order.delivery_status != 0">
                <label>Dispatch Date</label>
                <input type="date" class="form-control" v-model="order.dispatch_date" />
            </div>
            <div class="form-group" v-if="order != null && order.delivery_status == 2">
                <label>Date Delivered</label>
                <input type="date" class="form-control" v-model="order.date_delivered" />
            </div>
            <div class="form-group" v-if="order != null && (order.delivery_status == 3 || order.delivery_status == 4)">
                <label>Date Returned</label>
                <input type="date" class="form-control" v-model="order.returned_date" />
            </div>
            <button class="btn btn-success" @click="updateOrderDeliveryStatus">Update</button>
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
        }
    },
    data: function() {
        return {

        }
    },
    methods: {
        updateOrderDeliveryStatus() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "are you sure you want to update Order Delivery Status?",
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
                            url: "/ajax/admin/orders/update",
                            data: self.order,
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
                                    self.$emit("update-order-delivery-status");
                                    self.$bvModal.hide("updateDeliveryStatusFormModal");
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