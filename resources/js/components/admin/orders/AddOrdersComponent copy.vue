<template>
    <div>
        <create-customer-form-modal-component @add-customer="refreshCustomers"/>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Order Number</label>
                                    <input type="text" class="form-control" v-model="order.order_number" />
                                </div>
                                <div class="form-group">
                                    <label>Order Date</label>
                                    <input type="date" class="form-control" v-model="order.order_date"/>
                                </div>
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <multiselect v-model="select" :options="formattedOptions" :close-on-select="true"
                                        :clear-on-select="false" :searchable="true" label="name" :taggable="true" track-by="id" @select="handleCustomerSelection">
                                    </multiselect>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" class="form-control" v-model="order.number" />
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" class="form-control" v-model="order.email" />
                                </div>
                                <div class="form-group">
                                    <div class="regions_provinces_cities_container">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Region</label>
                                                <select class="form-select" @change="handleRegionSelection($event.target)" v-model="order.region_id">
                                                    <option value="null" selected>Select Region</option>
                                                    <option :value="region.id" v-for="(region, index) in regions" :key="index">
                                                        {{ region.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Province</label>
                                                <select class="form-select" @change="handleProviceSelection($event.target)" v-model="order.province_id">
                                                    <option value="null" selected>==Select Province==</option>
                                                    <option :value="province.id" v-for="(province, index) in provinces" :key="index">
                                                        {{ province.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>City</label>
                                                <select class="form-select" v-model="order.city_id">
                                                    <option value="null" selected>==Select City==</option>
                                                    <option v-for="city in cities" :value="city.id">
                                                        {{ city.name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control" v-model="order.address"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Attribution</label>
                                    <select class="form-control" v-model="order.attribution_id">
                                        <option value="">--Select Attribution--</option>
                                        <option v-for="(attribution, index) in attributions" :key="attribution.id" :value="attribution.id">
                                            {{ attribution.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Sales Assign</label>
                                    <select class="form-control" v-model="order.admin_id">
                                        <option value="">--Select--</option>
                                        <option v-for="(admin, index) in admins" :key="admin.id" :value="admin.id">
                                            {{ admin.first_name }} {{ admin.last_name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Target Pick Up / Target Delivery Date</label>
                                    <input type="date" class="form-control" v-model="order.target_delivery_date" />
                                </div>
                                <div class="form-group">
                                    <label>Delivery Status</label>
                                    <select class="form-control" v-model="order.delivery_status">
                                        <option value="0">Pending</option>
                                        <option value="1">Shipped</option>
                                        <option value="2">Delivered</option>
                                        <option value="3">RTS</option>
                                        <option value="4">Returned</option>
                                        <option value="5">Out For Delivery</option>
                                    </select>
                                </div>
                                <div class="form-group" v-if="order.delivery_status != 0">
                                    <label>Dispatch Date</label>
                                    <input type="date" class="form-control" v-model="order.dispatch_date" />
                                </div>
                                <div class="form-group" v-if="order.delivery_status == 3 || order.delivery_status == 4">
                                    <label>Returned Date</label>
                                    <input type="date" class="form-control" v-model="order.returned_date" />
                                </div>
                                <div class="form-group" v-if="order.delivery_status == 2 || order.delivery_status == 4">
                                    <label>Date Delivered</label>
                                    <input type="date" class="form-control" v-model="order.date_delivered" />
                                </div>
                                <div class="form-group">
                                    <label>Courier</label>
                                    <select class="form-control" v-model="order.courier_id">
                                        <option value="">--Select--</option>
                                        <option v-for="courier in couriers" :key="courier.id" :value="courier.id">
                                            {{ courier.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tracking Number</label>
                                    <input type="text" class="form-control" v-model="order.tracking_number" />
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea class="form-control" v-model="order.notes"></textarea>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <h2 class="col-12">Items ({{ product_orders.length }})</h2>
                            <div class="col-md-12 mt-2 mb-2">
                                <button class="btn btn-md btn-primary" @click="addNewLineProductOrder"><i class="fas fa-solid fa-plus"></i> Add New Item</button>
                            </div>
                            <div class="row ml-3 mb-2" v-if="isGeneratedDiscount">
                                <br>
                                <b-form-checkbox v-model="isAddCustomOrderDiscount" class="mt-2">Add Custom Order Discount</b-form-checkbox>
                                <br>
                                <br>
                                <hr>
                                <div class="col-2 mb-2">
                                    <label v-if="isAddCustomOrderDiscount">Discount Type</label>
                                    <select class="form-control" v-if="isAddCustomOrderDiscount" v-model="amountType">
                                        <option value="1">Amount</option>
                                        <option value="2">Percentage</option>
                                    </select>
                                </div>
                                <div class="col-2 mb-2" v-if="isAddCustomOrderDiscount && amountType == 1">
                                    <label>Amount (PHP)</label>
                                    <input type="number" class="form-control" v-model="discountAmount">
                                </div>
                                <div class="col-2 mb-2" v-if="isAddCustomOrderDiscount && amountType == 2">
                                    <label>Percentage (%)</label>
                                    <input type="number" class="form-control" v-model="discountPercentage">
                                </div>
                                <div class="col-2" v-if="isAddCustomOrderDiscount">
                                    <label>.</label>
                                    <button class="btn btn-primary form-control" @click="applyDiscount">Apply</button>
                                </div>
                                <br>
                                <br>
                            </div>
                            <hr>
                            <div class="row mb-3" v-for="(product_order, index) in product_orders" :key="index">
                                <div class="col-md">
                                    <label>Product Name</label>
                                    <select class="form-control" v-model="product_order.product_id" @change="updateProductPrice(index)">
                                        <option value=''>--Select--</option>
                                        <option v-for="product in products" :key="product.id" :value="product.id">
                                            {{ product.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md">
                                    <label>Quantity</label>
                                    <input type="number" class="form-control" v-model="product_order.quantity"/>
                                </div>
                                <div class="col-md">
                                    <label>Price</label>
                                    <input type="text" class="form-control" v-model="product_order.price"/>
                                </div>
                                <div class="col-md">
                                    <label>Discount</label>
                                    <input type="text" class="form-control" v-model="product_order.discount" :disabled="isAddCustomOrderDiscount"/>
                                </div>
                                <div class="col-2">
                                    <label>COGS</label>
                                    <input type="number" class="form-control" v-model="product_order.cogs" />
                                </div>
                                <div class="col-md">
                                    <label>Amount</label>
                                    <input type="text" class="form-control" :value="getTotalAmount(index)" disabled />
                                </div>
                                <div class="col-md">
                                    <label>Total COGS</label>
                                    <input type="text" class="form-control" :value="getTotalCogs(index)" disabled />
                                </div>
                                <div class="col-md">
                                    <label>Action</label>
                                    <button class="form-control btn btn-danger" @click="removeProduct(index)">Remove</button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card custom-card mt-3">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                                                <tr>
                                                    <th># of Items:</th>
                                                    <td>{{ this.getTotalItem() }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sub Total:</th>
                                                    <td>{{ this.getTotalSubTotal() }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Discount:</th>
                                                    <td>{{ this.getTotalDiscount() }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total:</th>
                                                    <td>{{ this.sumTotalAmount() }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total COGS:</th>
                                                    <td>{{ this.totalCogsAmount() }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <h2 class="col-12">Payment Method</h2>
                            <hr>
                            <div class="col-3">
                                <label>Payment Status</label>
                                <select class="form-control" v-model="order.mark_as_paid">
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label>Mode Of Payment</label>
                                <select class="form-control" v-model="order.mode_of_payment_id ">
                                    <option value="">--Select--</option>
                                    <option v-for="(mode_of_payment, index) in mode_of_payments" :key="mode_of_payment.id" :value="mode_of_payment.id">
                                        {{ mode_of_payment.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label>Amount</label>
                                <input type="number" class="form-control" v-model="order.payment_amount"/>
                            </div>
                            <div class="col-3">
                                <label>Notes</label>
                                <textarea class="form-control" v-model="order.payment_notes"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-primary" @click="saveCustomerOrder">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import 'vue-multiselect/dist/vue-multiselect.min.css'
import Multiselect from 'vue-multiselect'

export default {
    components: {
        Multiselect
    },
    data: function() {
        return {
            customers: [],
            select: '',
            order: {
                mode_of_payment_id: '',
                tracking_number: '',
                mark_as_paid: 0,
                number: '',
                email: '',
                region_id: '',
                province_id: '',
                city_id: '',
                admin_id: '',
                order_date: new Date().toISOString().substr(0, 10),
                is_new_customer: 0,
                customer_id: '',
                address: '',
                attribution_id: '',
                delivery_status: 0,
                dispatch_date: '',
                target_delivery_date: '',
                date_delivered: '',
                order_number: '',
                courier_id: '',
                notes: '',
                payment_amount: 0,
                payment_notes: '',
                payment_method_id: '',
                returned_date: '',
            },
            mode_of_payments: [],
            couriers: [],
            products: [],
            admins: [],
            product_orders: [],
            attributions: [],
            regions: [],
            provinces: [],
            cities: [],
            isGeneratedDiscount: false,
            isAddCustomOrderDiscount: false,
            amountType: 1,
            discountPercentage: 0,
            discountAmount: 0,
        }
    },
    watch: {
            'order.attribution_id': function(newAttributionId) {
            // Find the selected attribution based on its ID
            const selectedAttribution = this.attributions.find(attribution => attribution.id === newAttributionId);
            
            // Check if the selected attribution matches 'Own Delivery' or 'Walk In'
            if (selectedAttribution && (selectedAttribution.name.toLowerCase() === 'own delivery' || selectedAttribution.name.toLowerCase() === 'walk in')) {
                
                // Find the corresponding courier for 'Own Delivery' or 'Walk In'
                const correspondingCourier = this.couriers.find(courier => courier.name.toLowerCase() === selectedAttribution.name.toLowerCase());
                
                // Set the courier_id in the order object
                if (correspondingCourier) {
                    this.order.courier_id = correspondingCourier.id;
                } else {
                    // Handle case where no matching courier found
                    this.order.courier_id = ''; // or any default value you prefer
                }
            } else {
                // Reset courier_id if attribution is not 'Own Delivery' or 'Walk In'
                this.order.courier_id = '';
            }
        },
        select: {
            handler: function(selectedOption) {
                // Update the contact number when a customer is selected
                if (selectedOption) {
                    this.order.customer_id = selectedOption.id;
                    this.order.number = selectedOption.number;
                    this.order.email = selectedOption.email;
                    
                } else {
                    this.order.number = ''; // Reset the contact number if no customer is selected
                    this.order.email = '';
                }
            },
            immediate: true,
        },
    },
    computed: {
        selectedAttribution() {
            const selectedAttribution = this.attributions.find(attribution => attribution.id === this.order.attribution_id);
            return selectedAttribution ? selectedAttribution.category : null;
        },
        formattedOptions() {
            // Create a new array with the option for creating a new contact at the beginning
            const formattedOptions = this.customers.filter(customer => customer.id !== this.order.customer_id);
            formattedOptions.unshift({ id: null, name: 'Create New Contact' });
            return formattedOptions;
        },
    },
    mounted() {
        var app = this;
        app.refreshCustomers();
        app.refreshProducts();
        app.refreshAdmins();
        app.refreshAttributions();
        app.refreshCouriers();
        app.refreshModeOfPaymentData();
        app.fetchRegions();
        app.fetchCities();
        app.fetchProvinces();
    },
    methods: {
        totalCogsAmount() {
            let totalCogs = 0;
            this.product_orders.forEach(product_order => {
                const quantity = parseFloat(product_order.quantity) || 0;
                const cogs = parseFloat(product_order.cogs) || 0;
                totalCogs += quantity * cogs;
            });
            return '₱ ' + parseFloat(totalCogs).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        refreshModeOfPaymentData() {
            var self = this;
            axios.get("/ajax/admin/dropdown/mode-of-payment/api").then(function (resp) {
                self.mode_of_payments = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Mode Of Payments");
            });
        },
        getTotalItem() {
            let totalQuantity = 0;
            
            this.product_orders.forEach(product_order => {
                if (product_order.product_id !== '') {
                    const quantity = parseFloat(product_order.quantity) || 0;
                    totalQuantity += quantity;
                }
            });

            return totalQuantity;
        },
        fetchRegions(){
            axios.get('/ajax/admin/orders/fetch-regions')
            .then(response => {
                this.regions = response.data;
            })
            .catch(error => {
                console.log(error);
            })
        },
        fetchProvinces(regionId){
            axios.get('/ajax/admin/orders/fetch-provinces/' + regionId)
            .then(response => {
                this.provinces = response.data;
            })
            .catch(error => {
                console.log(error);
            })
        },
        fetchCities(provinceId){
            axios.get('/ajax/admin/orders/fetch-cities/' + provinceId)
            .then(response => {
                this.cities = response.data;
            })
            .catch(error => {
                console.log(error);
            }) 
        },
        handleRegionSelection(selectedRegion) {
            if (selectedRegion && selectedRegion.value != 'null') {
                const selectedRegionId = selectedRegion.value;
                this.fetchProvinces(selectedRegionId);
            }
        },
        handleProviceSelection(selectedProvice){
            if(selectedProvice && selectedProvice.value != 'null'){
                const selectedProvinceId = selectedProvice.value;
                this.fetchCities(selectedProvinceId);
            }
        },
        handleCustomerSelection(selectedOption) {
            if (selectedOption && selectedOption.id === null) {
                // Open modal to add new contact
                this.$bvModal.show("addCustomerFormModal");
            }
        },
        sumTotalAmount() {
            let totalAmount = 0;

            this.product_orders.forEach(product_order => {
                if (product_order.product_id !== '') {
                    const quantity = parseFloat(product_order.quantity) || 0;
                    const price = parseFloat(product_order.price) || 0;
                    const discount = parseFloat(product_order.discount) || 0;

                    // Calculate the total amount for the current product order
                    const productTotal = (quantity * price) - discount;

                    // Add the total amount for the current product order to the overall total
                    totalAmount += productTotal;
                }
            });

            return '₱ ' + parseFloat(totalAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalSubTotal() {
            let totalAmount = 0;

            this.product_orders.forEach(product_order => {
                if (product_order.product_id !== '') {
                    const quantity = parseFloat(product_order.quantity) || 0;
                    const price = parseFloat(product_order.price) || 0;

                    // Calculate the total amount for the current product order
                    const productTotal = (quantity * price);

                    totalAmount += productTotal;
                }
            });

            return '₱ ' + parseFloat(totalAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalDiscount() {
            let discount = 0;

            this.product_orders.forEach(product_order => {
                discount += parseFloat(product_order.discount) || 0;
            });

            return '- ₱ ' + parseFloat(discount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        applyDiscount() {
            // Count the total number of orders
            const totalOrders = this.product_orders.length;

            // Calculate the total amount of all orders
            let totalAmount = 0;
            this.product_orders.forEach(product_order => {
                totalAmount += product_order.price * product_order.quantity;
            });

            // Apply the discount amount or percentage per order
            this.product_orders.forEach(product_order => {
                if (this.amountType == 1) {
                    // Apply discount as an amount
                    product_order.discount = (product_order.price * product_order.quantity / totalAmount) * this.discountAmount;
                } else if (this.amountType == 2) {
                    // Apply discount as a percentage
                    const discountAmountPerOrder = (product_order.price * product_order.quantity * this.discountPercentage) / 100;
                    product_order.discount = discountAmountPerOrder;
                }
            });
        },
        refreshAttributions() {
            var self = this;
            axios.get("/ajax/admin/dropdown/attribution/api").then(function (resp) {
                self.attributions = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Attributions");
            });
        },
        removeProduct(index) {
            this.product_orders.splice(index, 1);
        },
        addNewLineProductOrder() {
            this.product_orders.push({
                product_id: '',
                price: 0,
                discount: 0,
                quantity: 1,
                cogs: 0,

            });
            this.isGeneratedDiscount = true;
        },
        refreshCouriers() {
            var self = this;
            axios.get('/ajax/admin/dropdown/courier/api').then(function (resp) {
                self.couriers = resp.data.data;
            })['catch'](function (resp) {
                alert("Could not load Couriers");
            });
        },
        refreshCustomers() {
            var self = this;
            axios.get("/ajax/admin/customers/api").then(function (resp) {
                self.customers = resp.data.data;
            })['catch'](function (resp) {
                alert("Could not load Customers");
            });
        },
        refreshAdmins() {
            var self = this;
            axios.get("/ajax/admin/dropdown/admin/api").then(function (resp) {
                self.admins = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Admins");
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
        updateProductPrice(index) {
            const selectedProductId = this.product_orders[index].product_id;
            const selectedProduct = this.products.find(product => product.id === selectedProductId);

            if (selectedProduct) {
                // Set the price based on the selected product
                this.$set(this.product_orders, index, {
                ...this.product_orders[index],
                price: selectedProduct.price,
                });
            }
        },
        getTotalAmount(index) {
                const productOrder = this.product_orders[index];
                const quantity = parseFloat(productOrder.quantity) || 0;
                const price = parseFloat(productOrder.price) || 0;
                const discount = parseFloat(productOrder.discount) || 0;

                return "₱ " + Number(((price * quantity) - discount), 2).toFixed(2);
            },
            getTotalCogs(index) {
            const product_order = this.product_orders[index];
            const quantity = parseFloat(product_order.quantity) || 0;
            const cogs = parseFloat(product_order.cogs) || 0;
            const totalCogs = quantity * cogs;
            return '₱ ' + totalCogs.toFixed(2);
        },

        getTotalCogsAmount() {
            let totalCogsAmount = 0;

            this.product_orders.forEach(product_order => {
                if (product_order.product_id !== '') {
                    const quantity = parseFloat(product_order.quantity) || 0;
                    const cogs = parseFloat(product_order.cogs) || 0;

                    //Calculate the total COGS for the current product order
                    const productTotalCogs = quantity * cogs;

                    //Add the total COGS for the current product order to the overall total
                    totalCogsAmount += productTotalCogs;
                }
            });

            return '₱ ' + parseFloat(totalCogsAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        saveCustomerOrder() {
            self = this;

            //Calculate total COGS for all product orders
            let totalCogsAmount = this.product_orders.reduce((total, product_order) => {
                const quantity = parseFloat(product_order.quantity) || 0;
                const cogs = parseFloat(product_order.cogs) || 0;
                return total + (quantity * cogs);
            }, 0);

            var data = {
                ...this.order,
                    product_orders: this.product_orders.map(order => ({
                    product_id: order.product_id,
                    quantity: order.quantity,
                    price: order.price,
                    discount: order.discount,
                    cogs: order.cogs
                })),
                total_cogs: totalCogsAmount,  //Add total COGS to data object
                mode_of_payment_id: this.order.mode_of_payment_id, 
                amount: this.order.payment_amount, 
                notes: this.order.payment_notes 
            }

            if (this.is_new_customer == 0) {
                data.name = this.order.name;
                data.number = this.order.number;
                data.email = this.order.email;

                data.customer_id = this.order.customer_id;
                data.number = this.order.number;
                data.email = this.order.email;
                data.address = this.order.address;
                data.order_date = this.order.order_date;
                data.admin_id = this.order.admin_id;
            } else if (this.is_new_customer == 1) {
                data.customer_id = this.order.customer_id;

                data.number = this.order.number;
                data.email = this.order.email;
                data.address = this.order.address;
                data.order_date = this.order.order_date;
                data.admin_id = this.order.admin_id;
            }

            data.amount = this.order.payment_amount;
            data.notes = this.order.payment_notes;

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
                            url: "/ajax/admin/orders/create",
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
                                    window.location.href = "/admin/orders";
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