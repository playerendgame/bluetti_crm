<template>
    <div>
        <div class="row">
            <div class="showOrdersComponent-buttons pb-3 d-flex justify-content-end">
                <button v-if="hasPermission.view_orders_update" :class="isEditing ? 'btn btn-danger' : 'btn btn-success'" @click="toggleEdit">{{ isEditing ? 'Cancel' : 'Edit Order' }}</button>
                <button class="btn btn-primary ml-2" v-if="isEditing" @click="saveChanges">Save Changes</button>
                <button v-if="hasPermission && hasPermission.view_orders_delete && !isEditing" class="btn btn-danger ml-2" @click="confirmDeleteOrder">Delete Order</button>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Order Number</label>
                                    <input type="text" class="form-control" v-model="order.order_number" :disabled="!isEditing" />
                                </div>
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <select v-model="order.customer" class="form-control" disabled>
                                        <option v-for="customer in customers" :key="customer.id" :value="customer">{{ customer.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Contact #</label>
                                    <input type="text" class="form-control" v-model="order.contact_number" :disabled="!isEditing"/>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" class="form-control" v-model="order.email" :disabled="!isEditing" />
                                </div>
                                <div class="form-group">
                                    <div class="regions_provinces_cities_container">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Region</label>
                                                <select class="form-control" v-model="selectedRegionId" :disabled="!isEditing">
                                                    <option :value="null">--Select Region--</option>
                                                    <option v-for="region in regions" :key="region.id" :value="region.id">
                                                        {{ region.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Province</label>
                                                <select class="form-control" v-model="selectedProvinceId" :disabled="!isEditing">
                                                    <option :value="null">--Select Province--</option>
                                                    <option v-for="province in filterProvinces" :key="province.id" :value="province.id">
                                                        {{ province.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>City</label>
                                                <select class="form-control" v-model="order.city_id" :disabled="!isEditing">
                                                    <option :value="null">--Select City--</option>
                                                    <option v-for="city in filterCities" :key="city.id" :value="city.id">
                                                        {{ city.name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control" v-model="order.address" :disabled="!isEditing"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Attribution</label>
                                    <select v-model="order.attribution" class="form-control" :disabled="!isEditing">
                                        <option v-for="attr in attribution" :key="attr.id" :value="attr">{{ attr.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Referral</label>
                                    <input type="text" class="form-control" v-model="order.referral_name" disabled/>
                                </div>
                                <div class="form-group">
                                    <label>Sales Assign</label>
                                    <select v-model="order.admin" class="form-control" :disabled="!isEditing">
                                        <option v-for="adm in admin" :key="adm.id" :value="adm">{{ adm.first_name }} {{ adm.last_name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Target Date Delivery</label>
                                    <input type="date" class="form-control" v-model="order.target_delivery_date" :disabled="!isEditing" />
                                </div>
                                <div class="form-group">
                                    <label>Delivery Status</label>
                                    <select v-model="order.delivery_status" class="form-control" :disabled="!isEditing">
                                        <option v-for="status in delivery_status" :key="status.id" :value="status.id">{{ status.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <label>Dispatch Date</label>
                                <input type="date" class="form-control" v-model="order.dispatch_date" :disabled="!isEditing"/>
                            </div>
                            <div class="col-6 mb-2">
                                <label>Delivered Date</label>
                                <input type="date" class="form-control" v-model="order.date_delivered" :disabled="!isEditing"/>
                            </div>
                            <div class="form-group">
                                <label>Tracking Number</label>
                                <input type="text" class="form-control" v-model="order.tracking_number" :disabled="!isEditing" />
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" v-model="order.notes" :disabled="!isEditing"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Order Date</label>
                                    <input type="date" class="form-control" v-model="order.order_date" :disabled="!isEditing" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Date Paid</label>
                                    <input type="date" class="form-control" v-model="order.date_paid_s" disabled />
                                </div>
                            </div>
                            <div class="row">
                                <h3>Items</h3>
                                <div class="row mb-3" v-for="(product, index) in order.order_products" :key="index">
                                    <div class="col-2">
                                        <label>Product Name</label>
                                        <select v-model="product.id" class="form-control" :disabled="!isEditing" @change="updateProduct(index)">
                                            <option v-for="prod in products" :key="prod.id" :value="prod.id">{{ prod.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-1">
                                        <label>Quantity</label>
                                        <input type="text" class="form-control" v-model="product.pivot.quantity" :disabled="!isEditing" @input="calculateSummary"/>
                                    </div>
                                    <div class="col-2">
                                        <label>Price</label>
                                        <input type="text" class="form-control" 
                                            :value="product.pivot.price" 
                                            @input="updateNumbers($event, product)" 
                                            :disabled="!isEditing" />                                   
                                    </div>
                                    <div class="col-2">
                                        <label>Discount</label>
                                        <input type="text" class="form-control" v-model="product.pivot.discount" :disabled="!isEditing" @input="calculateSummary"/>
                                    </div>
                                    <div class="col-2">
                                        <label>Total Amount</label>
                                        <input type="text" class="form-control" :value="calculateTotalAmount(product)" disabled/>
                                    </div>
                                    <div class="col-2">
                                        <label>COGS</label>
                                        <input type="text" class="form-control" v-model="product.pivot.cogs" :disabled="!isEditing" />
                                    </div>
                                    <div class="col-1">
                                        <label>% COGS</label>
                                        <input type="text" class="form-control" :value="conversionCogs(product)" disabled/>
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
                                                        <td>{{ getTotalItems() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Sub Total:</th>
                                                        <td>{{ getTotalSubTotal() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Discount:</th>
                                                        <td>{{ getTotalDiscount() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total:</th>
                                                        <td>{{ sumTotalAmount() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total COGS:</th>
                                                        <td>{{ totalCogs() }}</td>
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
            <div class="col-lg-12 col-md-12 col-sm-12">
                <datatable-component :fetch-url="ajaxUrl" :columns="columns" tableID="orders-table":defaultSortIndex="0" defaultSortOrder="desc" />
            </div>
        </div>
    </div>
</template>

 <script>
import Swal from 'sweetalert2';
import axios from 'axios';

export default {

    props: {
         // Array of Orders
        order: {
            type: Object,
            required: true,
        },
        // Array of customers
        customers: {
            type: Array,
            required: true,
        },
         // Array of attributions
        attribution: {
            type: Array,
            required: true,
        },
         // Array of Admins
        admin: {
            type: Array,
            required: true,
        },
        // An Object for role permissions handling
        hasPermission: {
            type: Object,
            required: true,
        }
    },
    // Component data
    data() {
        return {
            ajaxUrl: "",
            refresh: 0,
            isEditing: false,
            regions: [],
            provinces: [],
            cities: [],
            selectedRegionId: null,
            selectedProvinceId: null,
            columns: [
                { name: "Date", field: "created_at_s", sortable: true, show: true },
                { name: "Description", field: "description", sortable: true, show: true },
                { name: "Notes", field: "notes", sortable: true, show: true },
                { name: "Admin", field: "admin_name", sortable: true, show: true },
            ],
            // Array of delivery statuses
            delivery_status: [
                { id: 0, name: 'Pending' },
                { id: 1, name: 'Shipped' },
                { id: 2, name: 'Delivered' },
                { id: 3, name: 'RTS' },
                { id: 4, name: 'Returned' },
                { id: 5, name: 'Out For Delivery' },
                {id: 6, name: 'Cancelled'}
            ],
            products: [],
        }
    },
    mounted() {
        this.calculateSummary();
        this.refreshAjaxUrl();
        this.initializeOrder();
        this.refreshProducts();
    },
    watch: {
        'order.order_products': {
            handler: function (newProducts, oldProducts) {
                this.calculateSummary();
            },
            deep: true,
        },
        order: {
            handler: function (order) {
                this.selectedRegionId = order.region_id;
                this.selectedProvinceId = order.province_id;
                if (order.province_id) {
                    const province = this.provinces.find(p => p.id === order.province_id);
                    if (province) {
                        this.selectedRegionId = order.region_id;
                    }
                }

                if (order.city_id) {
                    const city = this.cities.find(c => c.id === order.city_id);
                    if (city) {
                        this.selectedProvinceId = this.selectedProvinceId;
                    }
                }

                this.loadRegions();
                this.loadProvinces();
                this.loadCities();
            },
            immediate: true,
        },
        selectedRegionId: {
            handler: function(newRegionId, oldRegionId) {
                if (newRegionId !== oldRegionId) {
                    this.selectedProvinceId = null;
                    this.order.region_id = newRegionId;
                }
            },
            deep: true,
        },
        selectedProvinceId: {
            handler: function(newProvinceId, oldProvinceId) {
                if (newProvinceId !== oldProvinceId) {
                    this.order.city_id = null;
                    this.order.province_id = newProvinceId;
                }
            },
            deep: true,
        }
    },
    computed: {
        filterProvinces() {
            if (!this.selectedRegionId) return [];
            return this.provinces.filter(province => province.region_id === this.selectedRegionId);
        },
        filterCities() {
            if (!this.selectedProvinceId) return [];
            return this.cities.filter(city => city.province_id === this.selectedProvinceId);
        }
    },
    methods: {
        loadRegions() {
            axios.get('/ajax/admin/dropdown/region/api').then(({data}) => this.regions = data.data);
        },
        loadProvinces() {
            axios.get('/ajax/admin/dropdown/province/api').then(({data}) => this.provinces = data.data);
        },
        loadCities() {
            axios.get('/ajax/admin/dropdown/city/api').then(({data}) => this.cities = data.data);
        },
        totalCogs() {
            let totalCogs = 0;
            this.order.order_products.forEach(product => {
            const cogs = parseFloat(product.pivot.cogs) || 0;
            totalCogs += cogs;
        });
            return '₱ ' + parseFloat(totalCogs).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        refreshProducts() {
           // Make AJAX request to fetch products
           axios.get('/ajax/admin/products/api')
                .then(response => {
                    // Update products array with response data
                    this.products = response.data.data;
                })
                .catch(error => {
                    // Handle error fetching products
                    console.error('Error fetching products:', error);
                    alert("Could not load Products");
                });
        },
        // Toggle edit mode
        toggleEdit() {
            // Toggle isEditing flag
            this.isEditing = !this.isEditing;
            // If not editing, initialize order data
            if (!this.isEditing) {
                this.initializeOrder();
            }
        },
        // Save changes to order
        saveChanges() {
            self = this;
            let data = {
                ...this.order,
                customer_id: this.order.customer ? this.order.customer.id : null,
                attribution_id: this.order.attribution ? this.order.attribution.id : null,
                admin_id: this.order.admin ? this.order.admin.id : null,
                province_id: this.selectedProvinceId,
                region_id: this.selectedRegionId,
                city_id: this.order.city_id,                
                delivery_status: this.order.delivery_status,
                order_products: this.order.order_products.map(product => ({
                    id: product.id,
                    product_id: product.id,
                    quantity: product.pivot.quantity,
                    price: product.pivot.price,
                    discount: product.pivot.discount,
                    cogs: product.pivot.cogs,
                })),
            };

            Swal.fire({
                title: "Update",
                text: "Are you sure you want to update this Order?",
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
                            url: "/ajax/admin/orders/update",
                            data: data,
                            config: { headers: { "Content-Type": "application/json" } },
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
                                    window.location.href = '/admin/orders/' + self.order.id;
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
                            if(response.response.status === 422) {
                                var key = Object.keys(response.response.data.errors)[0];
                                var errorMessage = response.response.data.errors[key][0];
                                Swal.fire({
                                    title: errorMessage,
                                    text: "",
                                    type: "error",
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
        // Refresh AJAX URL
        refreshAjaxUrl() {
            this.refresh++;
            this.ajaxUrl = `/ajax/admin/order/history/${this.order.id}?refresh=${this.refresh}&`;
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
            axios.delete(`/ajax/admin/orders/${this.order.id}/destroy`)
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
                            window.location.href = '/admin/orders';
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
        updateProduct(index) {
            const selectedProductId = this.order.order_products[index].id;
            const selectedProduct = this.products.find(product => product.id === selectedProductId);

            if (selectedProduct) {
                // Set the price based on the selected product
                this.$set(this.order.order_products[index].pivot, 'price', selectedProduct.price);
                this.calculateSummary();
            }
        },
        calculateTotalAmount(product) {
            const quantity = product.pivot.quantity || 0;
            const price = product.pivot.price || 0;
            const discount = product.pivot.discount || 0;

            const totalAmount = quantity * price - discount;
            const formattedTotal = '₱ ' + Number(totalAmount).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            return formattedTotal;
        },
        getTotalItems() {
            return this.order.order_products.reduce((total, product) => total + parseInt(product.pivot.quantity), 0);
        },
        getTotalSubTotal() {
            let totalAmount = 0;

            this.order.order_products.forEach(product_order => {
                if (product_order.product_id !== '') {
                    const quantity = parseFloat(product_order.pivot.quantity) || 0;
                    const price = parseFloat(product_order.pivot.price) || 0;

                    // Calculate the total amount for the current product order
                    const productTotal = (quantity * price);

                    totalAmount += productTotal;
                }
            });

            return '₱ ' + parseFloat(totalAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalDiscount() {
            let discount = 0;

            this.order.order_products.forEach(product_order => {
                discount += parseFloat(product_order.pivot.discount) || 0;
            });

            return '- ₱ ' + parseFloat(discount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        sumTotalAmount() {
            let totalAmount = 0;

            this.order.order_products.forEach(product_order => {
                if (product_order.product_id !== '') {
                    const quantity = parseFloat(product_order.pivot.quantity) || 0;
                    const price = parseFloat(product_order.pivot.price) || 0;
                    const discount = parseFloat(product_order.pivot.discount) || 0;

                    // Calculate the total amount for the current product order
                    const productTotal = (quantity * price) - discount;

                    // Add the total amount for the current product order to the overall total
                    totalAmount += productTotal;
                }
            });

            return '₱ ' + parseFloat(totalAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        conversionCogs(product) {
            return Number((product.pivot.cogs / ((product.pivot.quantity * product.pivot.price) - (product.pivot.quantity * product.pivot.discount))) * 100).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "%";
        },
        formatPrice(price){
            return "₱ " + Number(price).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },  
        updateNumbers(event, product) {
            product.pivot.price = event.target.value.replace(/[₱]/g, ''); // remove peso sign
            this.calculateSummary();
        },
        calculateSummary() {
            this.summary = {
                totalItems: this.getTotalItems(),
                totalSubTotal: this.getTotalSubTotal(),
                totalDiscount: this.getTotalDiscount(),
                sumTotalAmount: this.sumTotalAmount(),
                totalCogs: this.totalCogs
            };
        },
        initializeOrder() {
            // Initialize order_products with correct pivot structure
            this.order.order_products = this.order.order_products.map(product => ({
                ...product,
                pivot: {
                    ...product.pivot,
                    quantity: Number(product.pivot.quantity),
                    price: Number(product.pivot.price),
                    discount: Number(product.pivot.discount),
                    cogs: Number(product.pivot.cogs),
                }
            }));

            // Initialize customer, attribution, and admin
            if (this.order.customer_id) {
                this.order.customer = this.customers.find(c => c.id === this.order.customer_id) || null;
            } else {
                this.order.customer = null;
            }
            if (this.order.attribution_id) {
                this.order.attribution = this.attribution.find(a => a.id === this.order.attribution_id) || null;
            } else {
                this.order.attribution = null;
            }
            if (this.order.admin_id) {
                this.order.admin = this.admin.find(a => a.id === this.order.admin_id) || null;
            } else {
                this.order.admin = null;
            }

        },
    }
};
</script> 





 <!-- <script>
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
    props: ['order', 'customers', 'attribution', 'admin'],
    data() {
        return {
            ajaxUrl: "",
            refresh: 0,
            isEditing: false,
            columns: [
                { name: "Date", field: "created_at_s", sortable: true, show: true },
                { name: "Description", field: "description", sortable: true, show: true },
                { name: "Notes", field: "notes", sortable: true, show: true },
                { name: "Admin", field: "admin_name", sortable: true, show: true },
            ],
            delivery_status: [
                { id: 0, name: 'Pending' },
                { id: 1, name: 'Shipped' },
                { id: 2, name: 'Delivered' },
                { id: 3, name: 'RTS' },
                { id: 4, name: 'Returned' },
                { id: 5, name: 'Out For Delivery' },
            ],
            products: [],
        }
    },
    mounted() {
        this.calculateSummary();
        this.refreshAjaxUrl();
        this.initializeOrder();
        this.refreshProducts();
    },
    watch: {
       'order.order_products': {
           handler: function (newProducts, oldProducts) {
               this.calculateSummary();
           },
            deep: true,
        }
    },
    methods: {
        refreshProducts() {
            axios.get('/ajax/admin/products/api')
                .then(response => {
                    this.products = response.data.data;
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                    alert("Could not load Products");
                });
        },
        toggleEdit() {
            this.isEditing = !this.isEditing;
            if (!this.isEditing) {
                this.initializeOrder();
            }
        },
        saveChanges() {
            // Preparing the data to send
            const updatedOrder = {
                ...this.order,
                customer_id: this.order.customer ? this.order.customer.id : null,
                attribution_id: this.order.attribution ? this.order.attribution.id : null,
                admin_id: this.order.admin ? this.order.admin.id : null,
                delivery_status: this.order.delivery_status,
                order_products: this.order.order_products.map(product => ({
                    product_id: product.id,
                    quantity: product.pivot.quantity,
                    price: product.pivot.price,
                    discount: product.pivot.discount,
                    cogs: product.pivot.cogs,
                })),
            };

            axios.put(`/ajax/admin/orders/${this.order.id}/order-update`, updatedOrder)
            .then(response => {
                if (response.data.success) {
                    this.$emit('orderUpdated', response.data.order);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Order updated successfully',
                    });                    
                    setTimeout(() => {
                        this.isEditing = false;
                        window.location.reload();
                    }, 1000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response.data.message || 'Failed to save changes.',
                });
            });
        },
        refreshAjaxUrl() {
            this.refresh++;
            this.ajaxUrl = `/ajax/admin/order/history/${this.order.id}?refresh=${this.refresh}&`;
        },
        confirmDeleteOrder() {
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
                    this.deleteOrder();
                }
            });
        },
        deleteOrder() {
            axios.delete(`/admin/orders/${this.order.id}/destroy`)
            .then(response => {
                if (response.data.status === 'success') {
                    this.$emit('orderDeleted', this.order.id);
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Order has been deleted.',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data.message || 'Failed to delete order.',
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response.data.message || 'Failed to delete order.',
                });
            });
        },
        // updateProductPrice(index) {
        //     const selectedProductId = this.order.order_products[index].id;
        //     const selectedProduct = this.products.find(product => product.id === selectedProductId);

        //     if (selectedProduct) {
        //         this.$set(this.order.order_products[index].pivot, 'price', selectedProduct.price);
        //         this.$set(this.order.order_products[index].pivot, 'cogs', selectedProduct.cogs);
        //     }
        // },
        calculateTotalAmount(product) { 
            const quantity = product.pivot.quantity || 0;
            const price = product.pivot.price || 0;
            const discount = product.pivot.discount || 0;

            const totalAmount = quantity * price - discount;
            const formattedTotal = '₱ ' + Number(totalAmount).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            return formattedTotal;
         },
        // pricePesoSign(product) {
        //     return "₱ " + Number(product.pivot.price).toLocaleString(undefined, {
        //         minimumFractionDigits: 2,
        //         maximumFractionDigits: 2
        //     });
        // },
        //  discountPesoSign(product) {
        //     return "₱ " + Number((product.pivot.discount * product.pivot.quantity)).toLocaleString(undefined, {
        //         minimumFractionDigits: 2,
        //         maximumFractionDigits: 2
        //     });
        // },
        conversionCogs(product) {
            return Number((product.pivot.cogs / ((product.pivot.quantity * product.pivot.price) - (product.pivot.quantity * product.pivot.discount))) * 100).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + "%";
        },
        getTotalItems() {
            return this.order.order_products.length;
        },
        getTotalSubTotal() {
            let totalAmount = 0;

            this.order.order_products.forEach(product_order => {
                if (product_order.product_id !== '') {
                    const quantity = parseFloat(product_order.pivot.quantity) || 0;
                    const price = parseFloat(product_order.pivot.price) || 0;

                    // Calculate the total amount for the current product order
                    const productTotal = (quantity * price);

                    totalAmount += productTotal;
                }
            });

            return '₱ ' + parseFloat(totalAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        getTotalDiscount() {
            let discount = 0;

            this.order.order_products.forEach(product_order => {
                discount += parseFloat(product_order.pivot.discount) || 0;
            });

            return '- ₱ ' + parseFloat(discount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        sumTotalAmount() {
            let totalAmount = 0;

            this.order.order_products.forEach(product_order => {
                if (product_order.product_id !== '') {
                    const quantity = parseFloat(product_order.pivot.quantity) || 0;
                    const price = parseFloat(product_order.pivot.price) || 0;
                    const discount = parseFloat(product_order.pivot.discount) || 0;

                    // Calculate the total amount for the current product order
                    const productTotal = (quantity * price) - discount;

                    // Add the total amount for the current product order to the overall total
                    totalAmount += productTotal;
                }
            });

            return '₱ ' + parseFloat(totalAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        updateProduct(index) {
            const selectedProductId = this.order.order_products[index].id;
            const selectedProduct = this.products.find(product => product.id === selectedProductId);

            if (selectedProduct) {
                // Set the price based on the selected product
                this.$set(this.order.order_products[index].pivot, 'price', selectedProduct.price);
                this.calculateSummary();
            }
        },
        calculateSummary() {
            let totalItems = 0;
            let totalSubTotal = 0;
            let totalDiscount = 0;
            let sumTotalAmount = 0;

            this.order.order_products.forEach(product => {

                const quantity = parseFloat(product.pivot.quantity) || 0;
                const price = parseFloat(product.pivot.price) || 0;
                const discount = parseFloat(product.pivot.discount) || 0;
                const totalAmount = this.calculateTotalAmount(product);

                totalItems += quantity;
                totalSubTotal += price * quantity;
                totalDiscount += discount * quantity;
                sumTotalAmount += parseFloat(totalAmount);

            });

            this.summary = {
                totalItems,
                totalSubTotal: totalSubTotal.toFixed(2),
                totalDiscount: totalDiscount.toFixed(2),
                sumTotalAmount: sumTotalAmount.toFixed(2),
            }
        },
        initializeOrder() {
            // Ensure that order.customer, order.attribution, and order.admin are properly set.
            if (this.order.customer_id) {
                this.order.customer = this.customers.find(c => c.id === this.order.customer_id) || null;
            }
            if (this.order.attribution_id) {
                this.order.attribution = this.attribution.find(a => a.id === this.order.attribution_id) || null;
            }
            if (this.order.admin_id) {
                this.order.admin = this.admin.find(a => a.id === this.order.admin_id) || null;
            }
        },
    },
}
</script>  -->

