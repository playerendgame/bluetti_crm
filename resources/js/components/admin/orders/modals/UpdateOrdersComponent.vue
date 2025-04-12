<template>
    <div>
        <b-modal id="updateOrderFormModal" title="Update" :hide-footer="true">
            <div class="form-group" v-if="order != null">
                <label>Order Date</label>
                <input type="date" class="form-control" v-model="order.order_date" />
            </div>
            <div class="form-group" v-if="order != null">
                <label>Name</label>
                <input type="text" class="form-control" v-model="order.customer_name" disabled />
            </div>
            <div class="form-group" v-if="order != null">
                <label>Order Number</label>
                <input type="text" class="form-control" v-model="order.order_number" />
            </div>
            <div class="form-group" v-if="order != null">
                <label>Attribution</label>
                <select class="form-control" v-model="order.attribution_id">
                    <option v-for="(attribution, index) in attributions" :key="attribution.id" :value="attribution.id">
                        {{ attribution.name }}
                    </option>
                </select>
            </div>
            <div class="form-group" v-if="order != null">
                <label>Sales Assign</label>
                <select class="form-control" v-model="order.admin_id">
                    <option v-for="(admin, index) in admins" :key="admin.id" :value="admin.id">
                        {{ admin.first_name }} {{ admin.last_name }}
                    </option>
                </select>
            </div>
            <div class="form-group" v-if="order != null">
                <label>Address</label>
                <textarea class="form-control" v-model="order.address"></textarea>
            </div>
            <button class="btn btn-success" @click="updateOrder">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        order: {
            type: Object,
            required: false,
            notes: "model",
        }
    },
    data: function() {
        return {
            attributions: {},
            admins: {},
        }
    },
    mounted() {
        var app = this;
        app.refreshAttributions();
        app.refreshAdmins();
    },
    methods: {
        refreshAttributions() {
            var self = this;
            axios.get("/ajax/admin/dropdown/attribution/api").then(function (resp) {
                self.attributions = resp.data.data;
            })["catch"](function (resp) {
                alert("Could not load Attributions");
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
        updateOrder() {
            var self = this;

            Swal.fire({
                title: "Update",
                text: "are you sure you want to update Order?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Update",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise (function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/orders/update",
                            data: self.order,
                            config: { headers: { "Content-Type" : "application/json" } },
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
                                    self.$emit("update-order");
                                    self.$bvModal.hide("updateOrderFormModal");
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
                            if (response.response.status == 422) {
                                var key = Object.keys(response.response.data.errors)[0];
                                var errorMessage = response.response.data.errors[key][0];
                                Swal.fire({
                                    title: errorMessage,
                                    text: "",
                                    icon: "error",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                }).then((result) => {
                                    
                                });
                            }
                        });
                    });
                },
            }).then((result) => {
                if (!result.isConfirmed) {
                }
            });
        }
    }
}
</script>