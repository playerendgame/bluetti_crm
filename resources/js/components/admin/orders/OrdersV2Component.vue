<template>
    <div>
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="content-header">
                <div class="header-controls">
                <div class="filter-controls">
                    <button class="btn btn-primary btn-sm">New</button>
                    <select class="form-select form-select-sm">
                        <option>All Orders</option>
                        <option>Pending</option>
                        <option>Paid</option>
                        <option>Shipped</option>
                    </select>
                    <button class="form-select form-select-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Filter Columns
                    </button>
                    <!--DROP DOWN MENUS-->
                    <div class="dropdown-menu w-25" aria-labelledby="dropdownMenuButton">
                        <div class="container">
                            <div class="row pt-2">
                              <div class="col-md-6">
                                  <span class="fw-bold">Filter Columns</span>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group ml-4">
                                      <input type="checkbox" class="form-check-input"  @change="toggleAllColumnVisibility($event)" :checked="allColumnsSelected">
                                      <label class="form-label label-font-size">All</label>
                                  </div>
                              </div>
                            </div>
                          
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('region')" :checked="columns.region">
                                  <label class="form-label label-font-size">Region</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('province')" :checked="columns.province">
                                  <label class="form-label label-font-size">Province</label>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('city')" :checked="columns.city">
                                  <label class="form-label label-font-size">City</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('attribution')" :checked="columns.attribution"> 
                                  <label class="form-label label-font-size">Attribution</label>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('quantity')" :checked="columns.quantity">
                                  <label class="form-label label-font-size">Quantity</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('totalAmount')" :checked="columns.totalAmount">
                                  <label class="form-label label-font-size">Total Amount</label>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('cogs')" :checked="columns.cogs">
                                  <label class="form-label label-font-size">COGS</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('cogsPercentage')" :checked="columns.cogsPercentage">
                                  <label class="form-label label-font-size">% COGS</label>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('contactNumber')" :checked="columns.contactNumber">
                                  <label class="form-label label-font-size">Contact #</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('email')" :checked="columns.email">
                                  <label class="form-label label-font-size">Email</label>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('address')" :checked="columns.address">
                                  <label class="form-label label-font-size">Address</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('targetDeliveryDate')" :checked="columns.targetDeliveryDate">
                                  <label class="form-label label-font-size">Target Delivery Date</label>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('status')" :checked="columns.status">
                                  <label class="form-label label-font-size">Status</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('dispatchDate')" :checked="columns.dispatchDate">
                                  <label class="form-label label-font-size">Dispatch Date</label>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('returnedDate')" :checked="columns.returnedDate">
                                  <label class="form-label label-font-size">Returned Date</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('deliveredDate')" :checked="columns.deliveredDate" >
                                  <label class="form-label label-font-size">Delivered Date</label>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('trackingNumber')" :checked="columns.trackingNumber">
                                  <label class="form-label label-font-size">Tracking #</label>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('courier')" :checked="columns.courier">
                                  <label class="form-label label-font-size">Courier</label>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group ml-4">
                                  <input type="checkbox" class="form-check-input" @change="toggleColumnVisibility('adminAssign')" :checked="columns.adminAssign">
                                  <label class="form-label label-font-size">Admin Assign</label>
                                </div>
                              </div>
                            </div>
                        </div>

                    </div>
                    <input type="text" class="form-control form-control-sm" placeholder="Search orders...">
                </div>
                <div class="pagination-info">
                    <span>1 to 20 of 127</span>
                    <div class="pagination-controls">
                    <button class="btn btn-sm btn-outline-secondary">‹‹</button>
                    <button class="btn btn-sm btn-outline-secondary">‹</button>
                    <input type="text" class="form-control form-control-sm page-input" value="1">
                    <button class="btn btn-sm btn-outline-secondary">›</button>
                    <button class="btn btn-sm btn-outline-secondary">››</button>
                    </div>
                </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-responsive">
                <table class="table table-hover servicenow-table">
                    <thead>
                    <tr>
                        <th class=
                        "text-center align-content-center"><input type="checkbox" class="form-check-input text-center"></th>
                        <th>Actions</th>
                        <th>Date Created</th>
                        <th>Order Date</th>
                        <th>Order Number</th>
                        <th>Paid Status</th>
                        <th>Date Paid</th>
                        <th>Mode Of Payment</th>
                        <th>Customer Name</th>
                        <th v-if="columns.region">Region</th>
                        <th v-if="columns.province">Province</th>
                        <th v-if="columns.city">City</th>
                        <th v-if="columns.attribution">Attribution</th>
                        <th v-if="columns.quantity">Quantity</th>
                        <th v-if="columns.totalAmount">Total Amount</th>
                        <th v-if="columns.cogs">COGS</th>
                        <th v-if="columns.cogsPercentage">% COGS</th>
                        <th v-if="columns.contactNumber">Contact #</th>
                        <th v-if="columns.email">Email</th>
                        <th v-if="columns.address">Address</th>
                        <th v-if="columns.targetDeliveryDate">Target Delivery Date</th>
                        <th v-if="columns.status">Status</th>
                        <th v-if="columns.dispatchDate">Dispatch Date</th>
                        <th v-if="columns.returnedDate">Returned Date</th>
                        <th v-if="columns.deliveredDate">Delivered Date</th>
                        <th v-if="columns.trackingNumber">Tracking #</th>
                        <th v-if="columns.courier">Courier</th>
                        <th v-if="columns.adminAssign">Admin Assign</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="order in orders" :key="order.orderNumber">
                        <td class="text-center"><input type="checkbox" class="form-check-input"></td>
                        <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-primary">Edit</button>
                            <button class="btn btn-sm btn-outline-secondary">View</button>
                        </div>
                        </td>
                        <td>{{ order.dateCreated }}</td>
                        <td>{{ order.orderDate }}</td>
                        <td class="order-number">{{ order.orderNumber }}</td>
                        <td>
                        <span :class="getPaidStatusClass(order.paidStatus)">
                            {{ order.paidStatus }}
                        </span>
                        </td>
                        <td>{{ order.datePaid }}</td>
                        <td>{{ order.modeOfPayment }}</td>
                        <td class="customer-name">{{ order.customerName }}</td>
                        <td v-if="columns.region">{{ order.region }}</td>
                        <td v-if="columns.province">{{ order.province }}</td>
                        <td v-if="columns.city">{{ order.city }}</td>
                        <td v-if="columns.attribution">{{ order.attribution }}</td>
                        <td  class="text-center" v-if="columns.quantity">{{ order.quantity }}</td>
                        <td class="amount" v-if="columns.totalAmount">{{ order.totalAmount }}</td>
                        <td class="amount" v-if="columns.cogs">{{ order.cogs }}</td>
                        <td class="percentage" v-if="columns.cogsPercentage">{{ order.cogsPercentage }}%</td>
                        <td v-if="columns.contactNumber">{{ order.contactNumber }}</td>
                        <td class="email" v-if="columns.email">{{ order.email }}</td>
                        <td class="address" v-if="columns.address">{{ order.address }}</td>
                        <td v-if="columns.targetDeliveryDate">{{ order.targetDeliveryDate }}</td>
                        <td :class="getStatusClass(order.status)" v-if="columns.status">{{ order.status }}</td>
                        <td v-if="columns.dispatchDate">{{ order.dispatchDate }}</td>
                        <td v-if="columns.returnedDate">{{ order.returnedDate }}</td>
                        <td v-if="columns.deliveredDate">{{ order.deliveredDate }}</td>
                        <td v-if="columns.trackingNumber">{{ order.trackingNumber }}</td>
                        <td v-if="columns.courier">{{ order.courier }}</td>
                        <td v-if="columns.adminAssign">{{ order.adminAssign }}</td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Footer Pagination -->
            <div class="table-footer">
                <div class="footer-actions">
                <select class="form-select form-select-sm">
                    <option>--Actions on selected rows--</option>
                    <option>Export Selected</option>
                    <option>Delete Selected</option>
                    <option>Update Status</option>
                </select>
                </div>
                <div class="pagination-info">
                <span>1 to 20 of 127</span>
                <div class="pagination-controls">
                    <button class="btn btn-sm btn-outline-secondary">‹‹</button>
                    <button class="btn btn-sm btn-outline-secondary">‹</button>
                    <input type="text" class="form-control form-control-sm page-input" value="1">
                    <button class="btn btn-sm btn-outline-secondary">›</button>
                    <button class="btn btn-sm btn-outline-secondary">››</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {

    data(){
        return{

            columns: {
              region: false,
              province: false,
              city: false,
              attribution: false,
              quantity: false,
              totalAmount: false,
              cogs: false,
              cogsPercentage: false,
              contactNumber: false,
              email: false,
              address: false,
              targetDeliveryDate: false,
              status: false,
              dispatchDate: false,
              returnedDate: false,
              deliveredDate: false,
              trackingNumber: false,
              courier: false,
              adminAssign: false,
            }
     
        }
    },
    methods:{
      toggleColumnVisibility(column) {
        this.columns[column] = !this.columns[column];
      },
      toggleAllColumnVisibility(event) {
        const checked = event.target.checked;
        for (const column in this.columns) {
          this.columns[column] = checked;
        }
      }
    },
    computed: {
      allColumnsSelected() {
        for (const column in this.columns) {
          if (!this.columns[column]) {
            return false;
          }
        }
        return true;
      }
    }


}

</script>


<script setup>
import { ref } from 'vue'

//sample datas only
const orders = ref([
  {
    dateCreated: '2024-01-15 14:30',
    orderDate: '2024-01-15',
    orderNumber: 'BLU80070',
    paidStatus: 'Paid',
    datePaid: '2024-01-15',
    modeOfPayment: 'Credit Card',
    customerName: 'John Smith',
    region: 'North America',
    province: 'California',
    city: 'Los Angeles',
    attribution: 'Online Store',
    quantity: 2,
    totalAmount: '₱299.99',
    cogs: '₱180.00',
    cogsPercentage: '60.0',
    contactNumber: '+1-555-0123',
    email: 'john.smith@email.com',
    address: '123 Main St, Los Angeles, CA',
    targetDeliveryDate: '2024-01-22',
    status: 'Shipped',
    dispatchDate: '2024-01-16',
    returnedDate: '',
    deliveredDate: '2024-01-20',
    trackingNumber: 'TRK123456789',
    courier: 'FedEx',
    adminAssign: 'Mike Johnson'
  },
  {
    dateCreated: '2024-01-15 11:45',
    orderDate: '2024-01-15',
    orderNumber: 'BLU80070',
    paidStatus: 'Pending',
    datePaid: '',
    modeOfPayment: 'Bank Transfer',
    customerName: 'Sarah Wilson',
    region: 'Europe',
    province: 'London',
    city: 'London',
    attribution: 'Phone Order',
    quantity: 1,
    totalAmount: '₱149.50',
    cogs: '₱89.70',
    cogsPercentage: '60.0',
    contactNumber: '+44-20-7946-0958',
    email: 'sarah.wilson@email.com',
    address: '456 Oxford St, London, UK',
    targetDeliveryDate: '2024-01-25',
    status: 'Processing',
    dispatchDate: '',
    returnedDate: '',
    deliveredDate: '',
    trackingNumber: '',
    courier: 'DHL',
    adminAssign: 'Emma Davis'
  },
  {
    dateCreated: '2024-01-14 16:20',
    orderDate: '2024-01-14',
    orderNumber: 'BLU80070',
    paidStatus: 'Paid',
    datePaid: '2024-01-14',
    modeOfPayment: 'PayPal',
    customerName: 'Robert Brown',
    region: 'North America',
    province: 'New York',
    city: 'New York City',
    attribution: 'Mobile App',
    quantity: 3,
    totalAmount: '₱449.85',
    cogs: '₱269.91',
    cogsPercentage: '60.0',
    contactNumber: '+1-555-0456',
    email: 'robert.brown@email.com',
    address: '789 Broadway, New York, NY',
    targetDeliveryDate: '2024-01-21',
    status: 'Delivered',
    dispatchDate: '2024-01-15',
    returnedDate: '',
    deliveredDate: '2024-01-19',
    trackingNumber: 'TRK987654321',
    courier: 'UPS',
    adminAssign: 'Alex Chen'
  },
  {
    dateCreated: '2024-01-14 09:15',
    orderDate: '2024-01-14',
    orderNumber: 'BLU80070',
    paidStatus: 'Failed',
    datePaid: '',
    modeOfPayment: 'Credit Card',
    customerName: 'Lisa Anderson',
    region: 'Asia Pacific',
    province: 'NSW',
    city: 'Sydney',
    attribution: 'Social Media',
    quantity: 1,
    totalAmount: '₱199.99',
    cogs: '₱119.99',
    cogsPercentage: '60.0',
    contactNumber: '+61-2-9876-5432',
    email: 'lisa.anderson@email.com',
    address: '321 George St, Sydney, NSW',
    targetDeliveryDate: '2024-01-28',
    status: 'Cancelled',
    dispatchDate: '',
    returnedDate: '',
    deliveredDate: '',
    trackingNumber: '',
    courier: 'Australia Post',
    adminAssign: 'Tom Wilson'
  },
  {
    dateCreated: '2024-01-13 13:30',
    orderDate: '2024-01-13',
    orderNumber: 'BLU80070',
    paidStatus: 'Paid',
    datePaid: '2024-01-13',
    modeOfPayment: 'Stripe',
    customerName: 'David Martinez',
    region: 'North America',
    province: 'Texas',
    city: 'Houston',
    attribution: 'Email Campaign',
    quantity: 2,
    totalAmount: '₱349.98',
    cogs: '₱209.99',
    cogsPercentage: '60.0',
    contactNumber: '+1-555-0789',
    email: 'david.martinez@email.com',
    address: '555 Main St, Houston, TX',
    targetDeliveryDate: '2024-01-20',
    status: 'In Transit',
    dispatchDate: '2024-01-14',
    returnedDate: '',
    deliveredDate: '',
    trackingNumber: 'TRK456789123',
    courier: 'FedEx',
    adminAssign: 'Jessica Lee'
  }
])

// Helper methods for styling
const getPaidStatusClass = (status) => {
  const classes = {
    'Paid': 'badge bg-success',
    'Pending': 'badge bg-warning text-dark',
    'Failed': 'badge bg-danger'
  }
  return classes[status] || 'badge bg-secondary'
}

const getStatusClass = (status) => {
  const classes = {
    'Delivered': 'badge bg-success',
    'Shipped': 'badge bg-info',
    'In Transit': 'badge bg-primary',
    'Processing': 'badge bg-warning text-dark',
    'Cancelled': 'badge bg-danger'
  }
  return classes[status] || 'badge bg-secondary'
}
</script>

<style scoped>

/* Main Content Styles */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  background-color: #ffffff;
}

.content-header {
  background: white;
  border-bottom: 1px solid #dee2e6;
  padding: 1rem 1.5rem;
}

.header-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.filter-controls {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.filter-controls .form-select,
.filter-controls .form-control {
  width: auto;
  min-width: 120px;
}

.pagination-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 0.875rem;
  color: #6c757d;
}

.pagination-controls {
  display: flex;
  gap: 0.25rem;
}

.page-input {
  width: 50px;
  text-align: center;
}

/* Table Styles */
.table-container {
  flex: 1;
  padding: 0 1.5rem;
  overflow: auto;
}

.servicenow-table {
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 0;
}

.servicenow-table thead {
  background: linear-gradient(180deg, #495057 0%, #343a40 100%);
  color: white;
}

.servicenow-table thead th {
  border: none;
  padding: 1rem 1.5rem;
  font-weight: 600;
  font-size: 0.875rem;
  white-space: nowrap;
  position: sticky;
  top: 0;
  z-index: 10;
}

.servicenow-table tbody tr {
  transition: background-color 0.15s ease;
}

.servicenow-table tbody tr:nth-child(even) {
  background-color: #f8f9fa;
}

.servicenow-table tbody tr:hover {
  background-color: #e3f2fd !important;
}

.servicenow-table tbody td {
  padding: 0.75rem;
  border-top: 1px solid #dee2e6;
  font-size: 0.875rem;
  white-space: nowrap;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Specific Column Styles */
.order-number {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: #0066cc;
}

.customer-name {
  font-weight: 500;
  color: #2c3e50;
}

.amount {
  text-align: right;
  font-weight: 600;
  color: #28a745;
}

.percentage {
  text-align: right;
  font-weight: 600;
}

.email {
  color: #0066cc;
  max-width: 180px;
}

.address {
  max-width: 200px;
}

.tracking {
  font-family: 'Courier New', monospace;
  font-size: 0.8rem;
}

.action-buttons {
  display: flex;
  gap: 0.25rem;
}


.label-font-size{
  font-size: 0.85rem;
  font-weight: normal !important;
}

.action-buttons .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

/* Badge Styles */
.badge {
  font-size: 0.75rem;
  padding: 0.35em 0.65em;
}

/* Table Footer */
.table-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  background: white;
  border-top: 1px solid #dee2e6;
}

.footer-actions .form-select {
  min-width: 200px;
}


</style>