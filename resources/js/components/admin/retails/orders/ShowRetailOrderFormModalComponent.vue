<template>
    <div>
        <div class="row">
            <div class="pb-3 d-flex justify-content-end">
                <button class="btn btn-danger ml-2" @click="confirmDeleteOrder">Delete Order</button>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Order Date</label>
                                    <input type="text" class="form-control" v-model="order.date_order" disabled />
                                </div>
                                <div class="form-group">
                                    <label>Sales</label>
                                    <input type="text" class="form-control" v-model="order.sales_name" disabled />
                                </div>
                                <div class="form-group">
                                    <label>Store</label>
                                    <input type="text" class="form-control" v-model="order.store_name" disabled />
                                </div>
                                <div class="form-group">
                                    <label>Branch</label>
                                    <input type="text" class="form-control" v-model="order.branch_name" disabled />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="row">
                                <h3>Items</h3>
                                <hr>
                                <div class="row mb-3" v-for="(product, index) in order.retail_order_products" :key="index">
                                    <div class="col-4 mb-2">
                                        <label>Product Name</label>
                                        <input type="text" class="form-control" :value="product.name" disabled />
                                    </div>
                                    <div class="col-2 mb-2">
                                        <label>MSRP</label>
                                        <input type="text" class="form-control" :value="getProductMSRP(product)" disabled />
                                    </div>
                                    <div class="col-2 mb-2">
                                        <label>Quantity</label>
                                        <input type="text" class="form-control" :value="product.pivot.quantity" disabled />
                                    </div>
                                    <div class="col-2 mb-2">
                                        <label>Comms</label>
                                        <input type="text" class="form-control" :value="getProductComms(product)" disabled />
                                    </div>
                                    <div class="col-2 mb-2">
                                        <label>Cogs Amount</label>
                                        <input type="text" class="form-control" :value="getProductCogsAmount(product)" disabled />
                                    </div>
                                    <div class="col-3 mb-2">
                                        <label>Comms Amount</label>
                                        <input type="text" class="form-control" :value="getProductCommsAmount(product)" disabled />
                                    </div>
                                    <div class="col-3 mb-2">
                                        <label>Gross Profit</label>
                                        <input type="text" class="form-control" :value="getProductTotalGrossProfitAmount(product)" disabled />
                                    </div>
                                    <div class="col-2 mb-2">
                                        <label>Total Cogs</label>
                                        <input type="text" class="form-control" :value="getProductTotalCogsAmount(product)" disabled />
                                    </div>
                                    <div class="col-1 mb-2">
                                        <label>Cogs %</label>
                                        <input type="text" class="form-control" :value="getProductCogsPercentage(product)" disabled />
                                    </div>
                                    <div class="col-3 mb-2">
                                        <label>Net Profit</label>
                                        <input type="text" class="form-control" :value="getProductTotalNetProfit(product)" disabled />
                                    </div>
                                    <hr class="mt-3">
                                </div>
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
                                                    <th>{{ this.getTotalCommissions() }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Gross Profit</th>
                                                    <th>{{ this.getTotalGrossProfitAmount() }}</th>
                                                </tr>
                                                <tr>
                                                    <th>COGS Amount</th>
                                                    <th>{{ this.getTotalCogsAmount() }}</th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        order: {
            type: Object,
            required: false,
        }
    },
    methods: {
        getProductMSRP(product) {
            if (product && product.pivot && product.pivot.price) {
                const msrp = parseFloat(product.pivot.price);
                return '₱ ' + msrp.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return '₱ 0.00';
        },
        getProductComms(product) {
            if (product && product.pivot && product.pivot.comms) {
                const comms = parseFloat(product.pivot.comms);
                return comms.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%';
            }
            return '0%';
        },
        getProductCogsAmount(product) {
            if (product && product.pivot && product.pivot.cogs) {
                const cogs = parseFloat(product.pivot.cogs);
                return '₱ ' + cogs.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return '₱ 0.00';
        },
        getProductCommsAmount(product) {
            if (product && product.pivot && product.pivot.cogs) {
                const price = parseFloat(product.pivot.price);
                const comms = parseFloat(product.pivot.comms);
                const quantity = parseFloat(product.pivot.quantity);

                const commissionAmount = ((price * quantity) * comms) / 100;

                return '₱ ' + commissionAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return '₱ 0.00';
        },
        getProductTotalGrossProfitAmount(product) {
            if (product && product.pivot && product.pivot.price) {
                const price = parseFloat(product.pivot.price);
                const comms = parseFloat(product.pivot.comms);
                const quantity = parseFloat(product.pivot.quantity);

                const totalGrossProfitAmount = (price * quantity) - (((price * quantity) * comms) / 100);

                return '₱ ' + totalGrossProfitAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return '₱ 0.00';
        },
        getProductTotalCogsAmount(product) {
            if (product && product.pivot && product.pivot.cogs) {
                const cogs = parseFloat(product.pivot.cogs);
                const quantity = parseFloat(product.pivot.quantity);

                const cogs_amount = (cogs * quantity);

                return '₱ ' + cogs_amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return '₱ 0.00';
        },
        getProductCogsPercentage(product) {
            if (product && product.pivot && product.pivot.cogs) {
                const price = parseFloat(product.pivot.price);
                const cogs = parseFloat(product.pivot.cogs);
                const quantity = parseFloat(product.pivot.quantity);
                const comms = parseFloat(product.pivot.comms);

                const gross = (price * quantity) - (((price * quantity) * comms) / 100);
                const cogs_amount = (cogs * quantity);

                const cogs_percentage = (cogs_amount / gross) * 100;

                return cogs_percentage.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%';
            }
            return '0%';
        },
        getProductTotalNetProfit(product) {
            if (product && product.pivot && product.pivot.price) {
                const price = parseFloat(product.pivot.price);
                const cogs = parseFloat(product.pivot.cogs);
                const quantity = parseFloat(product.pivot.quantity);
                const comms = parseFloat(product.pivot.comms);

                const gross = (price * quantity) - (((price * quantity) * comms) / 100);
                const cogs_amount = (cogs * quantity);

                const net_profit = gross - cogs_amount;

                return '₱ ' + net_profit.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return '₱ 0.00';
        },
        getTotalItem() {
            let totalQuantity = 0;
            
            this.order.retail_order_products.forEach(product_order => {
                const quantity = parseFloat(product_order.pivot.quantity) || 0;
                totalQuantity += quantity;
            });

            return totalQuantity;
        },
        getTotalCommissions() {
            let commissionAmount = 0;
            this.order.retail_order_products.forEach(product_order => {
                const quantity = parseFloat(product_order.pivot.quantity) || 0;
                const comms = parseFloat(product_order.pivot.comms) || 0;
                const price = parseFloat(product_order.pivot.price) || 0;

                const commission = ((price * quantity) * comms) / 100;

                commissionAmount += commission;
            });

            return '₱ ' + parseFloat(commissionAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalGrossProfitAmount() {
            let totalGrossProfitAmount = 0;
            this.order.retail_order_products.forEach(product_order => {
                const quantity = parseFloat(product_order.pivot.quantity) || 0;
                const comms = parseFloat(product_order.pivot.comms) || 0;
                const price = parseFloat(product_order.pivot.price) || 0;

                const gross = (price * quantity) - (((price * quantity) * comms) / 100);

                totalGrossProfitAmount += gross;
            });

            return '₱ ' + parseFloat(totalGrossProfitAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalCogsAmount() {
            let totalCogsAmount = 0;

            this.order.retail_order_products.forEach(product_order => {
                const quantity = parseFloat(product_order.pivot.quantity) || 0;
                const cogs = parseFloat(product_order.pivot.cogs) || 0;

                const cogs_amount = (cogs * quantity);

                totalCogsAmount += cogs_amount;
            });

            return '₱ ' + parseFloat(totalCogsAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalNetProfit() {
            let totalNetProfit = 0;

            this.order.retail_order_products.forEach(product_order => {
                const quantity = parseFloat(product_order.pivot.quantity) || 0;
                const cogs = parseFloat(product_order.pivot.cogs) || 0;
                const comms = parseFloat(product_order.pivot.comms) || 0;
                const price = parseFloat(product_order.pivot.price) || 0;

                const gross = (price * quantity) - (((price * quantity) * comms) / 100);
                const cogs_amount = (cogs * quantity);

                const net_profit = gross - cogs_amount;

                totalNetProfit += net_profit;
            });

            return '₱ ' + parseFloat(totalNetProfit).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        // Confirm deletion of order
        confirmDeleteOrder() {
            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure you want to delete this order?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Delete order
                    this.deleteOrder();
                }
            });
        },
        // Delete order
        deleteOrder() {
            // Make AJAX DELETE request to delete order
            axios.delete(`/ajax/admin/retails/order/${this.order.id}/destroy`)
                .then(response => {
                    // Handle successful response
                    if (response.data.status === 'success') {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.data.message,
                        });
                        // Delay and redirect to orders list
                        setTimeout(() => {
                            window.location.href = '/admin/retails/orders';
                        }, 1000);
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message,
                        });
                    }
                })
                .catch(error => {
                    // Handle error deleting order
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response.data.message || 'Failed to delete order.',
                    });
                });
        },
    }
}
</script>