<template>
    <div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Purchase Date</label>
                                    <input type="date" class="form-control" v-model="purchase.purchase_date"/>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <h2 class="col-12">Items</h2>
                            <div class="col-md-12 mt-2 mb-3">
                                <button class="btn btn-md btn-primary" @click="addNewLinePurchaseOrder"><i class="fas fa-solid fa-plus"></i> Add New Item</button>
                            </div>
                            <div class="row mb-3" v-for="(purchase_order, index) in purchase_orders" :key="index">
                                <div class="col-4">
                                    <label>Item Name</label>
                                    <select class="form-control" v-model="purchase_order.product_id">
                                        <option value=''>--Select--</option>
                                        <option v-for="product in products" :key="product.id" :value="product.id">
                                            {{ product.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label>Quantity</label>
                                    <input type="number" class="form-control" v-model="purchase_order.quantity"/>
                                </div>
                                <div class="col-2">
                                    <label>Distributor Price</label>
                                    <input type="number" class="form-control" v-model="purchase_order.distributor_price"/>
                                </div>
                                <div class="col-2">
                                    <label>Total Amount Cost</label>
                                    <input type="text" class="form-control" :value="getTotalAmount(index)" disabled />
                                </div>
                                <div class="col-2">
                                    <label>Action</label>
                                    <button class="form-control btn btn-danger" @click="removePurchaseOrder(index)">Remove</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary" @click="savePurchaseOrder">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            purchase: {
                purchase_date: new Date().toISOString().substr(0, 10),
            },
            purchase_orders: [],
        }
    },
    mounted() {
        var app = this;
        app.refreshProducts();
    },
    methods: {
        getTotalAmount(index) {
            const purchaseOrder = this.purchase_orders[index];
            const quantity = parseFloat(purchaseOrder.quantity) || 0;
            const distributor_price = parseFloat(purchaseOrder.distributor_price)  || 0;

            return "₱ " + Number((distributor_price * quantity), 2).toFixed(2);
        },
        addNewLinePurchaseOrder() {
            this.purchase_orders.push({
                purchase_id: '',
                product_id: '',
                distributor_price: 0,
                quantity: 1,
            });
        },
        removePurchaseOrder(index) {
            this.purchase_orders.splice(index, 1);
        },
        refreshProducts() {
            var self = this;
            axios.get('/ajax/admin/products/api').then(function (resp) {
                self.products = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Products");
            });
        },
        savePurchaseOrder() {
            self = this;

            var data = {
                ...this.purchase,
                purchase_orders: this.purchase_orders.map(purchase => ({
                    product_id: purchase.product_id,
                    quantity: purchase.quantity,
                    distributor_price: purchase.distributor_price,
                })),
            }

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Purchase Order?",
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
                            url: "/ajax/admin/finance/purchase-order/create",
                            data: data,
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
                                    window.location.href = "/admin/finance/purchase-order";
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