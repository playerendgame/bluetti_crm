<template>
    <div>
        <b-modal id="updateProductFormModal" title="Update Product" :hide-footer="true">
            <div class="form-group" v-if="product != null">
                <label>Product Name</label>
                <input type="text" class="form-control" v-model="product.name" />
                <label class="pt-2">Alternative Name</label>
                <input type="text" class="form-control" v-model="product.alt_name" />
            </div>
            <div class="form-group" v-if="product != null">
                <label class="form-label">Category</label>
                <select class="form-select" v-model="product.category_id">
                    <option value="null">--Select--</option>
                    <option v-for="prodCategory in prodCategories" :key="prodCategory.id" :value="prodCategory.id">
                        {{ prodCategory.name }}
                    </option>
                </select>
            </div>
            <div class="form-group" v-if="product != null">
                <label>Price</label>
                <input type="number" class="form-control" v-model="product.price" />
            </div>
            <button class="btn btn-success" @click="updateProduct">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        product: {
            type: Object,
            required: false,
            notes: "model"
        },
    },
    data(){
        return{
            prodCategories: [],
        }
    },
    mounted(){
        this.fetchCategories();
    },
    methods: {
        updateProduct() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "are you sure you want to update Product?",
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
                            url: "/ajax/admin/products/update",
                            data: self.product,
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
                                    self.$emit("update-product");
                                    self.$bvModal.hide("updateProductFormModal");
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
        },
        fetchCategories(){
            axios.get('/ajax/admin/products/category/all')
            .then(response => {
                this.prodCategories = response.data
            })
            .catch(error => {
                console.log(error);
            })
        }
    }
}
</script>