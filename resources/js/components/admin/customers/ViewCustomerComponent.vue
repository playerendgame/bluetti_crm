<template>
  <div>
    <!--Customer details-->
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card custom-card">
          <div class="card-body">
            <div class="row row-sm">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Date Created</label>
                  <input type="text" class="form-control" v-model="customer.created_at_s" disabled />
                </div>
                <div class="form-group">
                  <label>Customer Name</label>
                  <input type="text" class="form-control" v-model="customer.name" disabled />
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input type="text" class="form-control" v-model="customer.email" disabled />
                </div>
                <div class="form-group">
                  <label>Phone Number #</label>
                  <input type="text" class="form-control" v-model="customer.number" disabled />
                </div>
                <div class="form-group">
                  <label>Address</label>
                  <input type="text" class="form-control" v-model="customer.address" disabled />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!--Orders table-->
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card custom-card p-4">
          <div class="upper-header pb-3">
            <div class="row">
              <div class="col-6">
                <span class="h4 pb-4">Customer Orders</span>
              </div>
              <div class="col-6 d-flex justify-content-end">
                <input type="search" class="form-control w-50" placeholder="Search" v-model="search" @input="filterOrders">
              </div>
            </div>
          </div>
          <!--Orders table-->
          <div class="orders-table">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th @click="sort('created_at')">Order Date</th>
                  <th @click="sort('name')">Product Name</th>
                  <th @click="sort('quantity')">Quantity</th>
                  <th @click="sort('price')">Price</th>
                  <th @click="sort('discount')">Discount</th>
                  <th @click="sort('total_amount')">Total Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(order, index) in sortedAndPaginatedOrders" :key="index">
                  <td>{{ order.created_at_s }}</td>
                  <td>{{ order.name }}</td>
                  <td>{{ order.quantity }}</td>
                  <td>{{ order.price }}</td>
                  <td>{{ order.discount }}</td>
                  <td>{{ order.total_amount }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <!--Pagination-->
          <nav aria-label="Page navigation">
            <div class="float-start">
              Showing {{ startIndex }} - {{ endIndex }} of {{ filteredOrders.length }} entries
            </div>
            <ul class="pagination float-end">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" aria-label="Previous" @click.prevent="previousPage">
                  <span>Previous</span>
                  <span class="sr-only">Previous</span>
                </a>
              </li>
              <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
                <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" aria-label="Next" @click.prevent="nextPage">
                  <span>Next</span>
                  <span class="sr-only">Next</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Quotations Section -->
        <div class="card custom-card p-4">
          <div class="row">
            <h3 class="h3 bold">Activities</h3>
          </div>
          <div class="row">
            <nav class="navbar navbar-expand-lg">
              <div class="container-fluid">
                <div class="collapse navbar-collapse" id="navbarNav">
                  <ul class="navbar-nav">
                    <li class="nav-item">
                      <button class="nav-link active" aria-current="page"  @click="quotationTab">Quotation</button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link" aria-current="page" @click="sampleTab">Others coming soon...</button>
                    </li>
                  </ul>
                </div>
              </div>
            </nav>
          </div>
          <hr>

          <!--Quotations-->
          <div class="quotation_container mt-4" v-if="isQuotation">
            <div class="upper-header pb-3">
              <div class="row">
                <div class="col-6 d-flex">
                  <span class="h4 pb-4">Quotations</span>
                  <div class="button ms-3">
                    <button class="btn btn-primary" @click="addQuotation">
                      <i class="fas fa-plus"></i> Add Quotation
                    </button>
                  </div>
                </div>
                <div class="col-6 d-flex justify-content-end">
                  <input type="search" class="form-control w-50" placeholder="Search quotations" v-model="searchQuotation" @input="filterQuotations">
                </div>
              </div>
            </div>
            
            <!-- Quotations Table -->
            <div v-if="quotations.length > 0" class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Reference Number</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(quotation, index) in filteredQuotations" :key="index">
                    <td>{{ formatDate(quotation.created_at) }}</td>
                    <td>{{ quotation.reference_number }}</td>
                    <td>{{ quotation.quotation_products ? quotation.quotation_products.length : 0 }} item(s)</td>
                    <td>{{ formatPrice(calculateQuotationTotal(quotation)) }}</td>
                    <td>
                      <button class="btn btn-sm btn-info me-1" @click="viewQuotation(quotation)">
                        <i class="fas fa-eye"></i> View
                      </button>
                      <!-- <button class="btn btn-sm btn-warning" @click="editQuotation(quotation)">
                        <i class="fas fa-edit"></i> Edit
                      </button> -->
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <div v-else class="text-center py-4">
              <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
              <p class="text-muted">No quotations found for this customer</p>
              <button class="btn btn-primary" @click="addQuotation">
                <i class="fas fa-plus"></i> Create First Quotation
              </button>
            </div>
          </div>

          <!--Other activity coming soon..-->
          <div class="sample_container mt-4" v-if="isSample">
            Coming Soon...
          </div>
        </div>
      </div>
    </div>

    <!-- Add Quotation Modal -->
    <add-quotation-modal-component 
      :customer="customer"
      @quotation-created="onQuotationCreated"
    />
  </div>
</template>

<script>
import axios from 'axios';
import AddQuotationModalComponent from './modals/AddQuotationModalComponent.vue';
import ViewQuotationCompoennt from './ViewQuotationComponent.vue';
import { useListeners } from 'vue';

export default {
  components: {
    AddQuotationModalComponent,
    ViewQuotationCompoennt
  },

  props: ['customer'],

  data() {
    return {
      orders: [],
      quotations: [],
      currentSortField: null,
      currentSortOrder: 'asc',
      currentPage: 1,
      ordersPerPage: 5,
      search: '',
      searchQuotation: '',
      quotationsPerPage: 10,
      quotationsCurrentPage: 1,

      isQuotation: true,
      isSample: false
    };
  },

  mounted() {
    this.fetchCustomerOrders();
    this.fetchCustomerQuotations();
  },

  computed: {
    //Filtered orders based on search query
    filteredOrders() {
      return this.orders.filter(order =>
        order.name.toLowerCase().includes(this.search.toLowerCase())
      );
    },

    //Sorted and paginated orders
    sortedAndPaginatedOrders() {
      const startIndex = (this.currentPage - 1) * this.ordersPerPage;
      const endIndex = startIndex + this.ordersPerPage;
      return this.filteredOrders.slice(startIndex, endIndex);
    },

    //Total pages for pagination
    totalPages() {
      return Math.ceil(this.filteredOrders.length / this.ordersPerPage);
    },

    //Starting index of displayed entries
    startIndex() {
      return (this.currentPage - 1) * this.ordersPerPage + 1;
    },

    //Ending index of displayed entries
    endIndex() {
      const end = this.currentPage * this.ordersPerPage;
      return end > this.filteredOrders.length ? this.filteredOrders.length : end;
    },

    //Filtered quotations
    filteredQuotations() {
      if (!this.searchQuotation) {
        return this.quotations;
      }
      const searchLower = this.searchQuotation.toLowerCase();
      return this.quotations.filter(quotation => 
        quotation.reference_number.toLowerCase().includes(searchLower) ||
        quotation.quotation_products.some(qp => 
          qp.products && qp.products.name.toLowerCase().includes(searchLower)
        )
      );
    },

    //Paginated quotations
    paginatedQuotations() {
      const startIndex = (this.quotationsCurrentPage - 1) * this.quotationsPerPage;
      const endIndex = startIndex + this.quotationsPerPage;
      return this.filteredQuotations.slice(startIndex, endIndex);
    }
  },

  methods: {
    async fetchCustomerOrders() {
      try {
        const response = await axios.get(`/ajax/admin/customers/${this.customer.id}/orders`);
        this.orders = response.data;
      } catch (error) {
        console.error('Error fetching customer orders:', error);
      }
    },

    async fetchCustomerQuotations() {
      try {
        const response = await axios.get(`/ajax/admin/customers/${this.customer.id}/quotations`);
        if (response.data.success) {
          this.quotations = response.data.quotations;
        }
      } catch (error) {
        console.error('Error fetching customer quotations:', error);
      }
    },

    addQuotation() {
      this.$bvModal.show('addQuotationForm');
    },

    onQuotationCreated(quotation) {
      this.quotations.unshift(quotation);
      this.$bvToast.toast('Quotation created successfully!', {
        title: 'Success',
        variant: 'success'
      });
    },

    viewQuotation(quotation) {
        if (quotation && quotation.id) {
            window.location.href = `/admin/customers/quotation/${quotation.id}`;
        } else {
            this.$bvToast.toast('Invalid quotation data', {
                title: 'Error',
                variant: 'danger'
            });
        }
    },


    editQuotation(quotation) {
      this.$bvToast.toast(`Editing quotation: ${quotation.reference_number}`, {
        title: 'Info',
        variant: 'info'
      });
    },

    calculateQuotationTotal(quotation) {
      if (!quotation.quotation_products) return 0;
      return quotation.quotation_products.reduce((total, qp) => {
        return total + (qp.price * qp.quantity);
      }, 0);
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
      }).format(price || 0);
    },

    sort(field) {
      if (this.currentSortField === field) {
        this.currentSortOrder = this.currentSortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        this.currentSortField = field;
        this.currentSortOrder = 'asc';
      }
    },

    changePage(page) {
      this.currentPage = page;
    },

    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },

    previousPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },

    //Filter orders based on search query
    filterOrders() {
      this.currentPage = 1; //Reset current page when filtering
    },

    filterQuotations() {
      this.quotationsCurrentPage = 1;
    },

    quotationTab(){
      this.isQuotation = true;
      this.isSample = false
    },
    sampleTab(){
      this.isQuotation = false;
      this.isSample = true;
    }
  }
}
</script>