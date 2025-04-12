<template>
    <div>
        <b-modal id="viewTargetDateModal" v-if="selectedOrder" size="xl" :hide-footer="true">
            <div class="container">
                <!--Header-->
                <div class="row">
                    <div class="col-6">
                        <h5 class="h3"><b>Bluetti Philippines</b></h5>
                        <p>Pamplona Uno</p>
                        <p>Las Pinas City PH PH-00 1740</p>
                        <p>Philippines</p>
                    </div>
                    <div class="col-6 text-end">
                        <p id="order-date">{{ selectedOrder.order_date_s }}</p>
                        <p>Invoice for <span id="order-number">{{ selectedOrder.order_number }}</span></p>
                    </div>
                </div>

                <hr>
                <!-- Item Details -->
                <div class="row pt-3">
                    <h4>Item Details</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Quantity</th>
                                    <th>Item</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, index) in orderProducts" :key="index">
                                    <td>{{ product.quantity }}x</td>
                                    <td>{{ product.alt_name }}</td>
                                    <td>{{ product.price }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row pt-3">
                    <h4>Payment Details</h4>
                    <div class="container p-4">
                        <div class="subtotal_price row border">
                            <div class="col-6">
                                <p>Subtotal Price:</p>
                            </div>
                            <div class="col-6">
                                <p>{{ selectedOrder.total_price }}</p>
                            </div>
                        </div>
                        <div class="total_tax row border">
                            <div class="col-6">
                                <p>Total Tax:</p>
                            </div>
                            <div class="col-6">
                                <p>₱ 0.00</p><!--Fixed as zero-->
                            </div>
                        </div>
                        <div class="shipping row border">
                            <div class="col-6">
                                <p>Shipping:</p>
                            </div>
                            <div class="col-6">
                                <p>₱ 0.00</p><!--Fixed as zero-->
                            </div>
                        </div>
                        <div class="total_price row border">
                            <div class="col-6">
                                <p><b>Total Price:</b></p>
                            </div>
                            <div class="col-6">
                                <p>{{ selectedOrder.total_price }}</p>
                            </div>
                        </div>
                         <div class="total_paid row border">
                            <div class="col-6">
                                <p><b>Total Paid:</b></p>
                            </div>
                            <div class="col-6">
                                <p>₱ {{ formatCurrency(totalPaid) }}</p><!--Added formatCurrency to fixed format to 000,000.00 -->
                            </div>
                        </div>
                        <div class="outstanding_amount row border">
                            <div class="col-6">
                                <p><b>Outstanding:</b></p>
                            </div>
                            <div class="col-6">
                                <p>₱ {{ formatCurrency(outstandingBalance) }}</p><!--Added formatCurrency to fixed format to 000,000.00 -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <h4 class="pb-4">Shipping Details</h4>
                    <div class="col-12">
                        <textarea class="form-control" disabled style="height: 200px" v-model="selectedOrder.address"></textarea>
                        <p>If you have any questions, please send an email to <span style="text-decoration: underline; color: blue;">hello@bluettiphilippines.com</span></p>
                    </div>
                </div>
                <div class="buttonsRow row">
                    <div class="button d-flex justify-content-center">
                        <button class="btn btn-primary" @click="printModalContent">Print</button> &nbsp;
                        <button class="btn btn-success" @click="saveasJpg">Save as JPG</button>
                    </div>
                </div>


            </div>
    

            <!--
        <div class="row">
            <h4 class="pb-3">Product Details</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <Th>Order Date</Th>
                        <Th>Product Name</Th>
                        <Th>Quantity</Th>
                        <Th>Price</Th>
                        <Th>Discount</Th>
                        <Th>Total Amount</Th>
                    </thead>
                    <Tbody>
                        <tr v-for="(orderProduct, index) in orderProducts" :key="index">
                            <td>{{ orderProduct.created_at_s }}</td>
                            <td>{{ orderProduct.name }}</td>
                            <td>{{ orderProduct.quantity }}</td>
                            <td>{{ orderProduct.price }}</td>
                            <td>{{ orderProduct.discount }}</td>
                            <td>{{ orderProduct.total_amount }}</td>
                        </tr>
                    </Tbody>
                </table>
            </div>
          </div>
          <br> <br>
          <div class="row pb-3">
            <h4 class="pb-3">Order Details</h4>
            <div class="col-6">
                    <label class="form-label">Order Date</label>
                    <input type="text" class="form-control" v-model="selectedOrder.order_date_s" disabled/>
            </div>
            <div class="col-6">
                <label class="form-label">Target Delivery Date</label>
                <input type="text" class="form-control"  v-model="selectedOrder.target_delivery_date_s" disabled/>
            </div>
          </div>
          <div class="row pb-3">
            <div class="col-6">
                <label class="form-label">Order #</label>
                <input type="text" class="form-control" v-model="selectedOrder.order_number" disabled/>
            </div>
            <div class="col-6">
                <label class="form-label">Customer</label>
                <input type="text" class="form-control" v-model="selectedOrder.customer_name" disabled/>
            </div>
          </div>
          <div class="row pb-4">
            <div class="col-4">
                <label class="form-label">Items</label>
                <input type="text" class="form-control" v-model="selectedOrder.items" disabled/>
            </div>
            <div class="col-4">
                <label class="form-label">Total</label>
                <input type="text" class="form-control" v-model="selectedOrder.total_price" disabled/>
            </div>
            <div class="col-4">
                <label class="form-label">Payment Status</label>
                <input type="text" class="form-control" v-model="selectedOrder.mark_as_paid_s" disabled/>
            </div>
          </div>

          <div class=" buttonsRow row">
            <div class="buttons d-flex justify-content-center">
                <button class="btn btn-primary" @click="printModalContent" type="button">Print</button> &nbsp;
                <button class="btn btn-success" @click="saveasJpg" type="button">Save As JPG</button>
            </div>
          </div>
          -->
        </b-modal>
    </div>
</template>

<script>

import axios from 'axios';
import html2canvas from 'html2canvas';

export default {
    props: {
        selectedOrder: {
            type: Object,
            default: null
        }
    },
    data(){
        return{
            orderProducts: [],
            totalPaid: 0,
            outstandingBalance: 0
        };
    },

    watch: {
        selectedOrder: {
            immediate: true,
            handler(newValue){
                if(newValue){
                    this.fetchOrderProducts(newValue.id);
                }
            }
        }
    },

    methods: {
        fetchOrderProducts(orderId) {
        axios.get(`/ajax/admin/dashboard/${orderId}/orders`)
            .then(response => {
                console.log('Fetched order products:', response.data); //Check response data
                this.orderProducts = response.data.orderProducts; //Correctly set order products
                this.totalPaid = response.data.totalPaid; //Correctly set totalPaid
                this.outstandingBalance = response.data.outstandingBalance;//Correctly set outstandingBalance
            })
            .catch(error => {
                console.error('Error fetching customer orders:', error);
            });
        },

        formatCurrency(value){
            return value.toLocaleString('en-us', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        },
        printModalContent() {
        

            const modal = document.getElementById('viewTargetDateModal');
            const modalContent = modal.querySelector('.modal-content');

            //Clone the modal content to preserve its original state
            const modalClone = modalContent.cloneNode(true);

            //Hides the buttons
            const buttonContainer = modalClone.querySelector('.buttonsRow');
            if(buttonContainer){
                buttonContainer.style.display = 'none';
            }

            //Create a print container div to hold the modal content
            const printContainer = document.createElement('div');
            printContainer.style.position = 'fixed';
            printContainer.style.left = '0';
            printContainer.style.top = '0';
            printContainer.style.width = '100%';
            printContainer.style.height = '100%';
            printContainer.style.overflow = 'auto';
            printContainer.style.backgroundColor = '#fff';
            printContainer.style.margin = '0';
            printContainer.style.padding = '40px';

            //Append modal content clone to the print container
            printContainer.appendChild(modalClone);

            //Hide unnecessary elements on the page for printing
            const elementsToHide = document.querySelectorAll('body > *:not(#viewTargetDateModal)');
            elementsToHide.forEach(element => {
                element.style.display = 'none';
            });

            //Append the print container to the body
            document.body.appendChild(printContainer);

            //Call window.print() to initiate the printing process
            window.print();

            //Clean up: remove the print container and show hidden elements
            document.body.removeChild(printContainer);
            elementsToHide.forEach(element => {
                element.style.display = '';
            });
        },

        saveasJpg() {
            // Create a wrapper element to contain the entire modal content
            const modalWrapper = document.createElement('div');
            modalWrapper.style.position = 'fixed';
            modalWrapper.style.left = '0';
            modalWrapper.style.top = '0';
            modalWrapper.style.width = '100%';
            modalWrapper.style.height = '100rem';
            modalWrapper.style.backgroundColor = '#fff';
            modalWrapper.style.zIndex = '9999';

            //Clone the modal content to preserve its original state
            const modal = document.getElementById('viewTargetDateModal');
            const modalContent = modal.querySelector('.modal-content');
            const modalClone = modalContent.cloneNode(true);

            //Hide unnecessary elements within the modal clone
            const buttonContainer = modalClone.querySelector('.buttonsRow');
            if (buttonContainer) {
                buttonContainer.style.display = 'none';
            }

            //Append the cloned modal content to the wrapper
            modalWrapper.appendChild(modalClone);

            //Append the wrapper to the document body
            document.body.appendChild(modalWrapper);

            //Use html2canvas to capture the wrapper (which now contains the entire modal)
            html2canvas(modalWrapper).then(canvas => {
                //Convert canvas to base64 image data
                const imgData = canvas.toDataURL('image/jpeg');

                //Create a link element to trigger download
                const downloadLink = document.createElement('a');
                downloadLink.href = imgData;
                downloadLink.download = 'modal-content.jpg';
                downloadLink.click();

                //Clean up: remove the modal wrapper from the body
                document.body.removeChild(modalWrapper);
            });
        }
    }
};

</script>