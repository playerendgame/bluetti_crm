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
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: ['customer'],

  data() {
    return {
      orders: [],
      currentSortField: null,
      currentSortOrder: 'asc',
      currentPage: 1,
      ordersPerPage: 5,
      search: '',
    };
  },

  mounted() {
    this.fetchCustomerOrders();
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
    }
  },

  methods: {
    fetchCustomerOrders() {
      axios.get(`/ajax/admin/customers/${this.customer.id}/orders`)
        .then(response => {
          this.orders = response.data;
        })
        .catch(error => {
          console.error('Error fetching customer orders:', error);
        });
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
    }
  }
}
</script>
