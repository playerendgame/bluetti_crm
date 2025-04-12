<template>
    <div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Order Date</label>
                                    <input type="date" class="form-control" v-model="order.date_order"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Sales</label>
                                    <select class="form-control" v-model="order.sales_admin">
                                        <option value="">--Select--</option>
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
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Store</label>
                                    <select class="form-control" v-model="selectedStoreId">
                                        <option value="">--Select Store--</option>
                                        <option v-for="(store, index) in stores" :key="store.id" :value="store.id">
                                            {{ store.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Branch</label>
                                    <select class="form-control" v-model="order.branch_id">
                                        <option value="">--Select Branch--</option>
                                        <option v-for="branch in filteredBranches" :key="branch.id" :value="branch.id">
                                            {{ branch.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="row mt-2">
                            <h2 class="col-12">Items</h2>
                            <div class="col-md-12 mt-2 mb-3">
                                <button class="btn btn-md btn-primary" @click="addNewLineProductOrder"><i class="fas fa-solid fa-plus"></i> Add New Item</button>
                            </div>
                            <div class="row mb-3" v-for="(retail_order_product, index) in retail_order_products" :key="index">
                                <div class="col-4 mb-2">
                                    <label>Product Name</label>
                                    <select class="form-control" v-model="retail_order_product.product_id" @change="updateProductPrice(index)">
                                        <option value="">--Select Item--</option>
                                        <option v-for="product in products" :key="product.id" :value="product.id">
                                            {{ product.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-2 mb-2">
                                    <label>MSRP</label>
                                    <input type="number" class="form-control" v-model="retail_order_product.price"/>
                                </div>
                                <div class="col-2 mb-2">
                                    <label>Quantity</label>
                                    <input type="number" class="form-control" v-model="retail_order_product.quantity"/>
                                </div>
                                <div class="col-2 mb-2">
                                    <label>Comms%</label>
                                    <input type="number" class="form-control" v-model="retail_order_product.comms"/>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label>COGS Amount</label>
                                    <input type="number" class="form-control" v-model="retail_order_product.cogs"/>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label>Comms Amount</label>
                                    <input type="text" class="form-control" :value="getCommsAmount(index)" disabled/>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label>Gross Profit</label>
                                    <input type="text" class="form-control" :value="getGrossProfitAmount(index)" disabled />
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label>Total Cogs</label>
                                    <input type="text" class="form-control" :value="getTotalCogsAmount(index)" disabled />
                                </div>
                                <div class="col-md-1 mb-2">
                                    <label>Cogs %</label>
                                    <input type="name" class="form-control" :value="getCogsPercentage(index)" disabled />
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label>Net Profit</label>
                                    <input type="text" class="form-control" :value="getNetProfit(index)" disabled />
                                </div>
                                <div class="col-md-12 mt-2 mb-3">
                                    <button class="btn btn-danger" @click="removeProduct(index)">Remove</button>
                                </div>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="card custom-card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                                <tr>
                                                    <th># of Items:</th>
                                                    <th>{{ this.getTotalItem() }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Commissions</th>
                                                    <th>{{ this.getCommissionAmount() }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Gross Profit</th>
                                                    <th>{{ this.getTotalGrossProfitAmount() }}</th>
                                                </tr>
                                                <tr>
                                                    <th>COGS Amount</th>
                                                    <th>{{ this.getCogsAmount() }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Net Profit</th>
                                                    <th>{{ this.getTotalNetProfit() }}</th>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-primary" @click="saveOrder">Save</button>
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
            retail_order_products: [],
            products: [],
            stores: [],
            branches: [],
            selectedStoreId: null,
            order: {
                store_id: '',
                branch_id: '',
                date_order: '',
                sales_admin: '',
            }
        }
    },
    watch: {
        order: {
            handler: function (order) {
                this.selectedStoreId = order.store_id;

                if (order.branch_id) {
                    const branch = this.branches.find(b => b.id === branch.city_id);
                    if (branch) {
                        this.selectedStoreId = this.selectedStoreId;
                    }
                }
            },
            immediate: true,
        },
        selectedStoreId: {
            handler: function(newStoreId, oldStoreId) {
                if (newStoreId !== oldStoreId) {
                    this.order.branch_id = '';
                    this.order.store_id = newStoreId;
                }
            },
            deep: true,
        },
    },
    computed: {
        filteredBranches() {
            if (!this.selectedStoreId) return [];
            return this.branches.filter(branch => branch.store_id === this.selectedStoreId);
        }
    },
    mounted() {
        this.refreshProducts();
        this.refreshStores();
        this.refreshBranches();
    },
    methods: {
        refreshStores() {
            var self = this;
            axios.get('/ajax/admin/retails/dropdown/store/api').then(function (resp) {
                self.stores = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Stores");
            });
        },
        refreshBranches() {
            var self = this;
            axios.get('/ajax/admin/retails/dropdown/branch/api').then(function (resp) {
                self.branches = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Branches");
            });
        },  
        addNewLineProductOrder() {
            this.retail_order_products.push({
                product_id: '',
                price: 0,
                quantity: 1,
                cogs: 0,
                comms: 25,
            })
        },
        refreshProducts() {
            var self = this;
            axios.get('/ajax/admin/products/api').then(function (resp) {
                self.products = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Products");
            });
        },
        removeProduct(index) {
            this.retail_order_products.splice(index, 1);
        },
        updateProductPrice(index) {
            const selectedProductId = this.retail_order_products[index].product_id;
            const selectedProduct = this.products.find(product => product.id === selectedProductId);

            if (selectedProduct) {
                this.$set(this.retail_order_products, index, {
                ...this.retail_order_products[index],
                price: selectedProduct.price,
                });
            }
        },
        getCommsAmount(index) {
            const productOrder = this.retail_order_products[index];
            const price = parseFloat(productOrder.price) || 0;
            const comms = parseFloat(productOrder.comms) || 0;
            const quantity = parseFloat(productOrder.quantity) || 0;

            const comms_amount = (((price * quantity) * comms) / 100);

            return '₱ ' + parseFloat(comms_amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            // return "₱ " + Number((((price * quantity) * comms) / 100), 2).toFixed(2);
        },
        getGrossProfitAmount(index) {
            const productOrder = this.retail_order_products[index];
            const price = parseFloat(productOrder.price) || 0;
            const comms = parseFloat(productOrder.comms) || 0;
            const quantity = parseFloat(productOrder.quantity) || 0;

            const gross = (price * quantity) - (((price * quantity) * comms) / 100);

            return '₱ ' + parseFloat(gross).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalCogsAmount(index) {
            const productOrder = this.retail_order_products[index];
            const cogs = parseFloat(productOrder.cogs) || 0;
            const quantity = parseFloat(productOrder.quantity) || 0;

            const cogs_amount = (cogs * quantity);

            return '₱ ' + parseFloat(cogs_amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getCogsPercentage(index) {
            const productOrder = this.retail_order_products[index];
            const price = parseFloat(productOrder.price) || 0;
            const cogs = parseFloat(productOrder.cogs) || 0;
            const quantity = parseFloat(productOrder.quantity) || 0;
            const comms = parseFloat(productOrder.comms) || 0;

            const gross = (price * quantity) - (((price * quantity) * comms) / 100);
            const cogs_amount = (cogs * quantity);

            const cogs_percentage = (cogs_amount / gross) * 100;

            return Number(cogs_percentage, 2).toFixed(2);
        },
        getNetProfit(index) {
            const productOrder = this.retail_order_products[index];
            const price = parseFloat(productOrder.price) || 0;
            const cogs = parseFloat(productOrder.cogs) || 0;
            const quantity = parseFloat(productOrder.quantity) || 0;
            const comms = parseFloat(productOrder.comms) || 0;

            const gross = (price * quantity) - (((price * quantity) * comms) / 100);
            const cogs_amount = (cogs * quantity);

            const net_profit = gross - cogs_amount;

             return '₱ ' + parseFloat(net_profit).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalItem() {
            let totalQuantity = 0;
            
            this.retail_order_products.forEach(product_order => {
                if (product_order.product_id !== '') {
                    const quantity = parseFloat(product_order.quantity) || 0;
                    totalQuantity += quantity;
                }
            });

            return totalQuantity;
        },
        getCommissionAmount() {
            let commissionAmount = 0;

            this.retail_order_products.forEach(product_order => {
                if (product_order.product !== '') {
                    const price = parseFloat(product_order.price) || 0;
                    const comms = parseFloat(product_order.comms) || 0;
                    const quantity = parseFloat(product_order.quantity) || 0;

                    const commission = ((price * quantity) * comms) / 100;

                    commissionAmount += commission;
                }
            });

            return '₱ ' + parseFloat(commissionAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalGrossProfitAmount() {
            let totalGrossProfitAmount = 0;

            this.retail_order_products.forEach(product_order => {
                if (product_order.product !== '') {
                    const price = parseFloat(product_order.price) || 0;
                    const comms = parseFloat(product_order.comms) || 0;
                    const quantity = parseFloat(product_order.quantity) || 0;

                    const gross = (price * quantity) - (((price * quantity) * comms) / 100);

                    totalGrossProfitAmount += gross;
                }
            });

            return '₱ ' + parseFloat(totalGrossProfitAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getCogsAmount() {
            let totalCogsAmount = 0;

            this.retail_order_products.forEach(product_order => {
                if (product_order.product !== '') {
                    const cogs = parseFloat(product_order.cogs) || 0;
                    const quantity = parseFloat(product_order.quantity) || 0;

                    const cogs_amount = (cogs * quantity);

                    totalCogsAmount += cogs_amount;
                }
            });

            return '₱ ' + parseFloat(totalCogsAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalNetProfit() {
            let totalNetProfit = 0;

            this.retail_order_products.forEach(product_order => {
                if (product_order.product !== '') {
                    const price = parseFloat(product_order.price) || 0;
                    const cogs = parseFloat(product_order.cogs) || 0;
                    const quantity = parseFloat(product_order.quantity) || 0;
                    const comms = parseFloat(product_order.comms) || 0;

                    const gross = (price * quantity) - (((price * quantity) * comms) / 100);
                    const cogs_amount = (cogs * quantity);

                    const net_profit = gross - cogs_amount;

                    totalNetProfit += net_profit;
                }
            });

            return '₱ ' + parseFloat(totalNetProfit).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        saveOrder() {
            self = this;

            let totalCogsAmount = this.retail_order_products.reduce((total, product_order) => {
                const quantity = parseFloat(product_order.quantity) || 0;
                const cogs = parseFloat(product_order.cogs) || 0;
                return total + (quantity * cogs);
            }, 0);

            var data = {
                ...this.order,
                retail_order_products: this.retail_order_products.map(order => ({
                    product_id: order.product_id,
                    quantity: order.quantity,
                    price: order.price,
                    cogs: order.cogs,
                    comms: order.comms,
                })),
                total_cogs: totalCogsAmount, // Add total COGS to data object
            }

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Order?",
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
                            url: "/ajax/admin/retails/dropdown/order/create",
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
                                    window.location.href = "/admin/retails/orders";
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