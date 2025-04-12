<template>
  <b-modal id="updatePaymentMethods" title="Update Payment Method" :hide-footer="true">
    <div class="col-12" v-if="order">
      <label>Payment Status</label>
      <select class="form-control" v-model="order.mark_as_paid">
        <option value="0">Unpaid</option>
        <option value="1">Paid</option>
      </select>
    </div>
    <div class="col-12 pt-3" v-if="order">
      <label>Mode Of Payment</label>
      <select class="form-control" v-model="order.mode_of_payment_id">
        <option value="">--Select--</option>
        <option v-for="(mode_of_payment, index) in mode_of_payments" :key="mode_of_payment.id" :value="mode_of_payment.id">
          {{ mode_of_payment.name }}
        </option>
      </select>
    </div>
    <div class="col-12 pt-3" v-if="order">
      <label>Amount</label>
      <input type="number" class="form-control" v-model="order.payment_amount" />
    </div>
    <div class="col-12 pt-3" v-if="order">
      <label>Notes</label>
      <textarea class="form-control" v-model="order.payment_notes" style="height: 8rem"></textarea>
    </div>
    <div class="col-12 pt-3" v-if="order">
      <label>Date Paid</label>
      <input type="date" class="form-control" v-model="order.date_paid" />
    </div>
    <div class="button text-center pt-4">
      <button class="btn btn-success" @click="updatePaymentDetails">Save Changes</button>
    </div>
  </b-modal>
</template>

<script>
export default {
  props: {
    order: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      payment_amount: '',
      payment_notes: '',
      mode_of_payments: []
    }
  },
  watch: {
    order: {
      immediate: true,
      handler() {
        this.fetchPaymentDetails();
        this.fetchPaymentMethods();
      }
    }
  },
  methods: {
    // Fetching payment details
    fetchPaymentDetails() {
      axios.get(`/ajax/admin/orders/${this.order.id}/payment-method`)
        .then(response => {
          if (response.data.error) {
            alert(response.data.error);
          } else {
            this.payment_amount = response.data.payment_amount;
            this.mode_of_payment_id = response.data.mode_of_payment_id;
            this.payment_notes = response.data.payment_notes
          }
        })
        .catch(error => {
          console.error(error)
        });
    },
    // Fetching payment methods
    fetchPaymentMethods() {
      var self = this;
      axios.get("/ajax/admin/dropdown/mode-of-payment/api")
        .then(function (resp) {
          self.mode_of_payments = resp.data.data;
        })["catch"](function (resp) {
          alert("Could not load Mode Of Payments");
        });
    },
    updatePaymentDetails() {
      axios.post('/ajax/admin/orders/update-payment-method', {
        order_id: this.order.id,
        id: this.order.mode_of_payment_id,
        mode_of_payment_id: this.order.mode_of_payment_id,
        payment_amount: this.order.payment_amount,
        payment_notes: this.order.payment_notes,
        mark_as_paid: this.order.mark_as_paid,
        date_paid: this.order.date_paid
      })
      .then(response => {
        if(response.data.success){
            Swal.fire({
            icon: 'success',
            title: 'Success!',
            message: response.data.message
          }).then(response => {
            this.$bvModal.hide("updatePaymentMethods");
            this.$emit('update-payment');
          });
        }else{
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: response.data.message
          })
        }
      })
    }
  }
}
</script>
