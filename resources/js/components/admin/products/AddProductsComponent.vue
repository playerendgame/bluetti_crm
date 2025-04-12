<template>
    <div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" class="form-control" v-model="product.name" />
                                    <label class="pt-2">Alternative Name</label>
                                    <input type="text" class="form-control" v-model="product.alt_name" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" v-model="product.category_id">
                                        <option value="" selected>==Select Category==</option>
                                        <option v-for="prodCategory in prodCategories" :key="prodCategory.id" :value="prodCategory.id">
                                            {{ prodCategory.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="number" class="form-control" v-model="product.price" />
                                </div>
                                <button class="btn btn-primary" @click="addProduct">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            product: {
                name: '',
                category_id: '',
                alt_name: '',
                price: 0,
            },

            prodCategories: [],
        }
    },
    mounted(){
        this.fetchCategories();
    },
    methods: {
        addProduct() {
            self = this;

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Product?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Save",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise(function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/products/create",
                            data: self.product,
                            config: { headers: { "Content-Type": "application/json" } },
                        }).then(function (response) {
                            if(response.data.success) {
                                Swal.fire({
                                    title: response.data.message,
                                    text: "",
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "Okay",
                                }).then((result) => {
                                    window.location.href = "/admin/products/items";
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
                            if (response.response.status === 422) {
                                var key = Object.keys(response.response.data.errors)[0];
                                var errorMessage = response.response.data.errors[key][0];
                                Swal.fire({
                                    title: errorMessage,
                                    text: "",
                                    icon: "error",
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