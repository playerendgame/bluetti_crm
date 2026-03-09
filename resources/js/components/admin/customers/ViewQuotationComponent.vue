<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Quotation Details</h4>
                    <div class="buttons d-flex justify-content-end text-end">
                        <button class="btn btn-secondary" @click="goBack">
                            <i class="fas fa-arrow-left"></i> Back to Customer
                        </button>
                        <button class="btn btn-success ms-3" @click="printQuotation">Print</button>

                    </div>
                </div>
                <div class="quotation_container">
                    <div class="card-body">
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-6">
                            <img class="img-fluid w-25" src="/bluetti_logo.png" alt="Company Logo">
                            </div>
                            <div class="col-md-6 text-end">
                                <p>
                                    Bluetti Philippines (Officially Distributed by Prime)<br>					
                                    2F EARN Building, Alabang-Zapote Rd., Las Piñas City, 1740<br>				
                                    bluettiphilippines.com<br>
                                    0956-7807-764					
                                </p>
                            </div>
                        </div>
                        <div class="row mb-4 text-center">
                            <h3 class="h3 fw-bold">Energy Independence, Starts Here	</h3>
                            <h5 class="h5"> Together, we make anything possible	</h5>
                        </div>
                        <div class="row mb-4">
                            <h3 class="fw-bold" style="color: #2a97d0;">
                                QUOTATION: {{ quotation.reference_number }}
                            </h3>
                        </div>

                        <div class="row p-4 mb-3" style="background-color: #2a97d0;"></div>

                        <!-- Customer Informations -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="fw-bold">Customer Information</h5>
                                <p class="mb-1"><strong>Client/Company/Contact Person:</strong> {{ quotation.customers?.name || 'N/A' }}</p>
                                <p class="mb-1"><strong>Contact Number:</strong> {{ quotation.customers?.number || 'N/A' }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ quotation.customers?.email || 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <br>
                                <p class="mb-1"><strong>Quotation Date:</strong> {{ formatDate(quotation.created_at) }}</p>
                            </div>
                        </div>

                        <!-- Quotation Items -->
                        <div class="row mb-2">
                            <div class="col-12">
                                <h5>Quotation Items</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="background-color: #2a97d0; color: white;">Quantity</th>
                                                <th style="background-color: #2a97d0; color: white;">Product</th>
                                                <th style="background-color: #2a97d0; color: white;">Unit Price</th>
                                                <th style="background-color: #2a97d0; color: white;">Units Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in quotation.quotation_products" :key="index">
                                                <td>{{ item.quantity }}</td>
                                                <td>{{ item.products?.name || 'N/A' }}</td>
                                                <td>{{ formatPrice(item.price) }}</td>
                                                <td>{{ formatPrice(item.price * item.quantity) }}</td>
                                            </tr>
                                            <tr v-if="quotation.quotation_products?.length === 0">
                                                <td colspan="4" class="text-center">No items found</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Quotation Description -->
                        <div class="row pb-4" v-if="quotation.description">
                            <div class="col-12">
                                <div class="border rounded-4 p-3" v-html="quotation.description"></div>
                            </div>
                        </div>
                        <!--DISCOUNTED PRICE GOES HERE-->
                        <div class="row d-flex ms-1 me-1 align-items-center">
                            <div class="col-md-10 text-end text-light" style="background-color: #2a97d0;">
                                <h5>Total Discount:</h5>
                            </div>
                            <div class="col-md-2 text-center border">
                                <h5 class="fw-bold">{{ formatPrice(calculateTotalDiscount()) }}</h5>
                            </div>
                        </div>
                        <div class="row d-flex ms-1 me-1 align-items-center pb-3">
                            <div class="col-md-10 text-end text-light" style="background-color: #2a97d0;">
                                <h5>Total Amount(VAT Inclusive):</h5>
                            </div>
                            <div class="col-md-2 text-center border">
                                <h5 class="fw-bold">{{ formatPrice(calculateGrandTotal()) }}</h5>
                            </div>
                        </div>
                        <div class="row pb-4">
                            <span>Officially Distributed by:</span>
                            <img class="img-fluid" style="width: 10rem;" src="/primetech_horizontal.png"></img>
                        </div>
                    </div>
                </div>
            
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import html2canvas from 'html2canvas';

export default {
    props: ['quotationId'],

    data() {
        return {
            quotation: {}
        };
    },

    mounted() {
        if (this.quotationId && this.quotationId !== 'null') {
            this.fetchQuotation();
        }
    },

    methods: {
        goBack() {
            window.history.back();
        },

        async fetchQuotation() {
            try {
               
                const id = parseInt(this.quotationId);
                if (isNaN(id)) {
                    throw new Error('Invalid quotation ID');
                }
                
                const response = await axios.get(`/admin/api/customers/quotation/${id}`);
                if (response.data.success) {
                    this.quotation = response.data.quotation;
                    console.log('Quotation loaded:', this.quotation); // Debug log
                } else {
                    this.$bvToast.toast(response.data.message || 'Error fetching quotation', {
                        title: 'Error',
                        variant: 'danger'
                    });
                }
            } catch (error) {
                console.error('Error fetching quotation:', error);
                this.$bvToast.toast('Error fetching quotation: ' + (error.response?.data?.message || error.message), {
                    title: 'Error',
                    variant: 'danger'
                });
            }
        },

        
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        formatPrice(price) {
            if (!price) return '₱0.00';
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(price);
        },

        calculateGrandTotal() {
            return this.calculateSubtotal() - this.calculateTotalDiscount();
        },
        calculateTotalDiscount(){
            if (!this.quotation.quotation_products) return 0;
            return this.quotation.quotation_products.reduce((total, item) => {
                return total + (item.discount * item.quantity);
            }, 0); 
        },
        calculateSubtotal() {
            if (!this.quotation.quotation_products) return 0;
            return this.quotation.quotation_products.reduce((total, item) => {
                return total + (item.price * item.quantity);
            }, 0);
        },

        async printQuotation() {
            const quotationElement = document.querySelector('.quotation_container');
            
            if (!quotationElement) {
                this.$bvToast.toast('Quotation content not found', {
                    title: 'Error',
                    variant: 'danger'
                });
                return;
            }

            try {
                //Show loading state
                const loadingComponent = this.$loading.show();

                //Capture the element as canvas
                const canvas = await html2canvas(quotationElement, {
                    scale: 2,
                    useCORS: true,
                    logging: false
                });

                //Open a new window for printing
                const printWindow = window.open('', '_blank');
                
                //Write HTML content for printing
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Print Quotation</title>
                            <style>
                                body { 
                                    margin: 0; 
                                    padding: 1px; 
                                    font-family: Arial, sans-serif;
                                }
                                img { 
                                    width: 100%; 
                                    height: auto; 
                                }
                                @media print {
                                    body { 
                                        padding: 0; 
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <img src="${canvas.toDataURL('image/png')}" onload="window.print();window.close()" />
                        </body>
                    </html>
                `);
                
                printWindow.document.close();
                
                //Hide loading
                loadingComponent.hide();
            } catch (error) {
                console.error('Error generating print:', error);
                this.$bvToast.toast('Error generating print: ' + error.message, {
                    title: 'Error',
                    variant: 'danger'
                });
                
                //Hide loading if it was shown
                if (typeof this.$loading !== 'undefined') {
                    this.$loading.hide();
                }
            }
        }
    }
}
</script>
