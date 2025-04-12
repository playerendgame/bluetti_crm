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
                                    <input type="text" class="form-control" v-model="purchase.created_at_s" disabled />
                                </div>
                                <div class="form-group">
                                    <label>Ref Code</label>
                                    <input type="text" class="form-control" v-model="purchase.ref_code" disabled />
                                </div>
                            </div>
                            <div class="row">
                                <h3>Items</h3>
                                <div class="row mb-3" v-for="(purchase, index) in purchase.purchase_orders" :key="index">
                                    <div class="col-4">
                                        <label>Product Name</label>
                                        <input type="text" class="form-control" v-model="purchase.name" disabled />
                                    </div>
                                    <div class="col-2">
                                        <label>Quantity</label>
                                        <input type="text" class="form-control" v-model="purchase.pivot.quantity" disabled />
                                    </div>
                                    <div class="col-2">
                                        <label>Stocks Left</label>
                                        <input type="text" class="form-control" v-model="purchase.pivot.stocks_left" disabled />
                                    </div>
                                    <div class="col-2">
                                        <label>Distributor Price</label>
                                        <input type="text" class="form-control" :value="pricePesoSign(purchase)" disabled />
                                    </div>
                                    <div class="col-2">
                                        <label>Total Cost</label>
                                        <input type="text" class="form-control" :value="calculateTotalAmount(purchase)" disabled />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card custom-card mt-3">
                                        <div class="card-body">
                                            <div>
                                                <h5><b>Summary</b></h5>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                                    <tr>
                                                        <th># of Items:</th>
                                                        <td>{{ this.getTotalItems() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th><b>Total:</b></th>
                                                        <td><b>{{ this.sumTotalAmount() }}</b></td>
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
    </div>
</template>

<script>
export default {
    props: ['purchase'],
    methods: {
        getTotalItems() {
            let totalQuantity = 0;

            this.purchase.purchase_orders.forEach(purchase_order => {
                const quantity = parseFloat(purchase_order.pivot.quantity) || 0;
                totalQuantity += quantity;
            });

            return totalQuantity;
        },
        sumTotalAmount() {
            let totalAmount = 0;

            this.purchase.purchase_orders.forEach(purchase_order => {
                if (purchase_order.product_id !== '') {
                    const quantity = parseFloat(purchase_order.pivot.quantity) || 0;
                    const price = parseFloat(purchase_order.pivot.distributor_price) || 0;

                    // Calculate the total amount for the current product order
                    const productTotal = (quantity * price);

                    // Add the total amount for the current product order to the overall total
                    totalAmount += productTotal;
                }
            });

            return '₱ ' + parseFloat(totalAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        pricePesoSign(purchase) {
            return "₱ " + Number(purchase.pivot.distributor_price).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
        formatCurrency(amount) {
            return parseFloat(amount).toFixed(2);
        },
        calculateTotalAmount(purchase) {
            return "₱ " + Number(this.formatCurrency(parseFloat(purchase.pivot.quantity * purchase.pivot.distributor_price))).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
    }
}
</script>