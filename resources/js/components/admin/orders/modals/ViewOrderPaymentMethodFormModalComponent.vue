<template>
    <div>
        <add-order-payment-method-form-modal-component :order="order" @add-order-payment-method="refreshAjaxUrl"/>
        <update-order-payment-method-form-modal-component :orderPaymentMethod="editOrderPaymentMethod" @update-order-payment-method="refreshAjaxUrl"/>
        <b-modal id="viewOrderPaymentMethodFormModal" title="View Payment Method" :hide-footer="true" size="xl">
            <div class="form-group" v-if="order != null">
                <label>Order Number</label>
                <input type="text" class="form-control" v-model="order.order_number" disabled />
            </div>
            <div class="form-group" v-if="order != null">
                <label>Customer Name</label>
                <input type="text" class="form-control" v-model="order.customer_name" disabled />
            </div>
            <br>
            <button class="btn btn-primary" @click="addPaymentMethod">Add Payment Method</button>
            <hr>
            <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="buttons" tableID="orders-table"
                v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
                @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
            />
            <button v-if="order != null && order.mark_as_paid == 0" class="btn btn-success" @click="markAsPaid">Mark as Paid</button>
            <button v-else-if="order != null && order.mark_as_paid == 1" class="btn btn-danger" @click="markAsUnpaid">Mark as Unpaid</button>
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
            columns: [
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
                { name: "Payment Method", field: "payment_method_name", sortable: true, show: true },
                { name: "Amount", field: "amount_s", sortable: true, show: true },
                { name: "Notes", field: "notes", sortable: true, show: true },
                { name: "Admin", field: "admin_name", sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View",
                    method: "",
                    kind: "group",
                    type: "success",
                    buttons: [
                        { name: "Edit", method: "editPaymentMethod" },
                    ]
                }
            ],
            refresh: 0,
            ajaxUrl: '',
            editOrderPaymentMethod: null,
            orderPaymentMethod: null,
        }
    },
    watch: {
        order: {
            handler: function (order) {
                if (order != null) {
                    this.refreshAjaxUrl();
                }
            },
            immediate: true,
        }
    },
    methods: {
        addPaymentMethod() {
            this.$bvModal.show("addModeOfPaymentFormModal");
        },
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/orders/" + this.order.id + "/payment-method?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        onButtonClick(method, object) {
            if (method === "editPaymentMethod") {
                this.editOrderPaymentMethod = object.item;
                this.$bvModal.show('editOrderPaymentMethodFormModalComponent');
            }
        },
        markAsPaid() {
            self = this;

            var data = {
                id: this.order.id,
                mark_as_paid: 1,
            };

            Swal.fire({
                title: "Update",
                text: "are you sure you want to update Order Mark as Paid?",
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
                            url: "/ajax/admin/orders/update/mark-as-paid",
                            data: data,
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
                                    self.$emit("update-order-mark-as-paid");
                                    self.$bvModal.hide("viewOrderPaymentMethodFormModal");
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
        },
        markAsUnpaid() {
            self = this;

            var data = {
                id: this.order.id,
                mark_as_paid: 0,
            };

            Swal.fire({
                title: "Update",
                text: "are you sure you want to update Order Mark as Unpaid?",
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
                            url: "/ajax/admin/orders/update/mark-as-paid",
                            data: data,
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
                                    self.$emit("update-order-mark-as-unpaid");
                                    self.$bvModal.hide("viewOrderPaymentMethodFormModal");
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