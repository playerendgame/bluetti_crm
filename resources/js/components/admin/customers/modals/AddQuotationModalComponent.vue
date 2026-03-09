<template>
    <b-modal id="addQuotationForm" title="Add Quotation" size="xl" @ok="submitQuotation" @show="resetForm">
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Customer</label>
                <input type="text" class="form-control" v-model="customer.name" disabled />
            </div>
            <div class="col-6">
                <label class="form-label">Quotation Date</label>
                <input type="date" class="form-control" v-model="quotationDate" disabled />
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-4">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" v-model="customer.number" disabled />
            </div>
            <div class="col-4">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" v-model="customer.email" disabled />
            </div>
            <div class="col-4">
                <label class="form-label">Address</label>
                <input type="text" class="form-control" v-model="customer.address" disabled />
            </div>
        </div>
        <hr>
        <!--Product Lists-->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Quotation Items</h5>
                    <button type="button" class="btn btn-primary" @click="addItem">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>
                <div v-if="items.length === 0" class="text-center py-4">
                    <p class="text-muted">No items added yet. Click "Add Item" to start.</p>
                </div>
                <div v-for="(item, index) in items" :key="index" class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="form-label">Product</label>
                                    <select v-model="item.product_id" class="form-select" @change="updateProductPrice(index)">
                                        <option value="">Select Product ({{ products.length }} available)</option>
                                        <option v-for="product in products" :key="product.id" :value="product.id">
                                            {{ product.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Price</label>
                                    <input type="number" class="form-control" v-model="item.price" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Discount</label>
                                    <input type="number" class="form-control" v-model="item.discount" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control" v-model="item.quantity" min="1">
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger" @click="removeItem(index)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <!--Quotation Description-->
        <div class="row mb-3">
            <div class="col-12">
                <label class="form-label">Description</label>
                <wysiwyg v-model="description" />
            </div>
        </div>
    </b-modal>
</template>

<script>
import axios from 'axios';

export default {
    props: ['customer'],

    data() {
        return {
            items: [],
            products: [],
            description: '',
            quotationDate: new Date().toISOString().split('T')[0]
        };
    },

    mounted() {
        this.fetchProducts();
    },

    methods: {
        resetForm() {
            this.items = [];
            this.description = '';
            this.quotationDate = new Date().toISOString().split('T')[0];
        },

        addItem() {
            this.items.push({
                product_id: '',
                price: 0,
                quantity: 1,
                discount: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        updateProductPrice(index) {
            if (!Array.isArray(this.products)) {
                console.error('Products is not an array:', this.products);
                return;
            }
            
            const productId = this.items[index].product_id;
            const product = this.products.find(p => {
                return String(p.id) === String(productId);
            });
            
            if (product) {
                this.items[index].price = parseFloat(product.price) || 0;
            } else {
                this.items[index].price = 0;
            }
        },

        fetchProducts() {
            axios.get('/ajax/admin/products/api')
                .then(response => {
                    if (Array.isArray(response.data)) {
                        this.products = response.data;
                    } else {
                        this.products = response.data.data || Object.values(response.data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                    this.products = [];
                });
        },

        async submitQuotation(bvModalEvt) {
            bvModalEvt.preventDefault();

            if (this.items.length === 0) {
                this.$bvToast.toast('Please add at least one item', {
                    title: 'Validation Error',
                    variant: 'warning'
                });
                return;
            }

            for (const item of this.items) {
                if (!item.product_id || item.quantity <= 0 || item.price <= 0) {
                    this.$bvToast.toast('Please fill all item details correctly', {
                        title: 'Validation Error',
                        variant: 'warning'
                    });
                    return;
                }
            }

            try {
                const response = await axios.post(`/ajax/admin/customers/${this.customer.id}/quotations`, {
                    items: this.items,
                    description: this.description
                });

                if (response.data.success) {
                    this.$bvToast.toast('Quotation created successfully!', {
                        title: 'Success',
                        variant: 'success'
                    });
                    
                    Swal.fire({
                        title: 'Quotation Created!',
                        text: 'The quotation has been successfully created.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$emit('quotation-created', response.data.quotation);
                            this.$bvModal.hide('addQuotationForm');
                        }
                    });
                } else {
                    this.$bvToast.toast(response.data.message || 'Error creating quotation', {
                        title: 'Error',
                        variant: 'danger'
                    });
                }
            } catch (error) {
                console.error('Error creating quotation:', error);
                this.$bvToast.toast('Error creating quotation: ' + (error.response?.data?.message || error.message), {
                    title: 'Error',
                    variant: 'danger'
                });
            }
        }

    }
}
</script>