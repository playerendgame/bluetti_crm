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
                                    <input type="text" class="form-control" v-model="product.name" disabled />
                                    <label class="pt-2">Alternative Name</label>
                                    <input type="text" class="form-control" v-model="product.alt_name" disabled />
                                </div>
                                <div class="form-group">
                                    <label>Category</label>
                                    <select class="form-select" v-model="product.category_id" disabled>
                                            <option v-for="prodCategory in prodCategories" :key="prodCategory.id" :value="prodCategory.id">
                                                {{ prodCategory.name }}
                                            </option>
                                        </select>                                
                                    </div>
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="text" class="form-control" v-model="product.price_s" disabled />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <!-- <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="row">
                                <div class="row mb-3">
                                    <div class="col-2">
                                        <label>Purchase Date</label>
                                        <input type="text" class="form-control" disabled />
                                    </div>
                                    <div class="col-2">
                                        <label>Purchase ID</label>
                                        <input type="text" class="form-control" disabled />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
            <div class="row">
                <h3>Purchase Order</h3>
                <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="buttons" tableID="orders-table"
                    v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
                    @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
                />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['product'],
    data: function() {
        return {
            refresh: 0,
            ajaxUrl: "",
            columns: [
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
                { name: "Purchase Date", field: "purchase_date_s", sortable: true, show: true },
                { name: "PO ID", field: "po_id", sortable: true, show: true },
                { name: "Quantity", field: "quantity", sortable: true, show: true },
                { name: "COGS", field: "cogs_s", sortable: true, show: true },
                { name: "Total Cost", field: "total_cost", sortable: true, show: true },
                { name: "Stocks Left", field: "stocks_left", sortable: true, show: true },
            ],
            prodCategories: [],
        }
    },
    mounted() {
        var app = this;
        app.refreshAjaxUrl();
        app.fetchCategories();
    },
    methods: {
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/products/" + this.product.id + "?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
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