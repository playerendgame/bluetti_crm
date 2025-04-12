<template>
    <div>
        <b-modal id="updateRetailOrderFormModal" title="Update Order" hide-footer="true">
            <div class="form-group" v-if="order">
                <label>Date Order</label>
                <input type="date" class="form-control" v-model="order.date_order"/>
            </div>
            <div class="form-group" v-if="order">
                <label>Sales</label>
                <select class="form-control" v-model="order.sales_admin">
                    <option :value="null">--Select--</option>
                    <option value="1">Kettony Tan</option>
                    <option value="2">Edelyn Aro</option>
                    <option value="3">Rhox & Ket</option>
                    <option value="4">Roxelle</option>
                    <option value="5">Sarah</option>
                    <option value="6">Carl John</option>
                    <option value="7">Blesidy</option>
                    <option value="8">Ronnel</option>
                    <option value="9">Rosemarie</option>
                </select>
            </div>
            <button class="btn btn-success" @click="updateOrder">Update</button>
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
    methods: {
        updateOrder() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "Are you sure you want to update Order?",
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
                            url: "/ajax/admin/retails/order/update",
                            data: self.order,
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
                                    self.$bvModal.hide("updateRetailOrderFormModal");
                                    self.$emit('update-retail-order');
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