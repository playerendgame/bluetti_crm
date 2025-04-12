<template>
    <div>
        <update-product-form-modal-component :product="product" @update-product="refreshAjaxUrl"/>
        <hr>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"
            @editCallBack="editCallBack" @editTextSaved="editTextSaved" @clickToEditSaved="clickToEditSaved" @onDataClicked="onDataClicked"
        />
    </div>
</template>

<script>
export default {
    props: {
        hasPermission: {
            type: Object,
            required: true,
        }
    },
    data: function() {
        let buttons = [];
        if (this.hasPermission.product_update) {
            buttons.push({ name: "Update", method: "updateProduct" });
        }if (this.hasPermission.product_delete) {
            buttons.push({ name: 'Delete', method: 'deleteProduct' });
         }
        return {
            product: null,
            ajaxUrl: "",
            refresh: 0,
            columns: [
                { name: "Date Created", field: "created_at_s", sortable: true, show: true },
                { name: "Name", field: "name", sortable: true, show: true },
                { name: "Category", field: "category", sortable: true, show: true },
                { name: "Alt Name", field: 'alt_name', sortable: true, show: true},
                { name: "Price", field: "price_s", sortable: true, show: true },
                { name: "Stocks Left", field: 'stocks', sortable: true, show: true },
            ],
            buttons: [
                {
                    name: "View Details",
                    method: 'viewProduct',
                    type: "success",
                    kind: "group",
                    buttons: buttons,
                    // [
                       // { name: "Update", method: "updateProduct" },
                    //]
                }
            ]
        }
    },
    mounted() {
        var app = this;
        app.refreshAjaxUrl();
    },
    computed: {
        filteredButtons() {
            if (this.hasPermission.product_update) {
                return this.buttons;
            }
            if (this.hasPermission.product_delete) {
                return this.buttons;
            }
            return [];
        }
    },
    methods: {
        deleteProduct(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to delete this product?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/ajax/admin/products/delete', { id: id })
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire('Deleted!', 'Product has been deleted successfully.', 'success');
                                this.refreshAjaxUrl();
                            } else {
                                Swal.fire('Error', response.data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Failed to delete product.', 'error');
                        });
                }
            });
        },
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/products/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        onButtonClick(method, object) {
            if (method === "viewProduct") {
                window.open('/admin/products/' + object.item.id); 
            } else if (method === 'deleteProduct') {
                this.deleteProduct(object.item.id);
            } else if (method === "updateProduct") {
                this.product = object.item;
                this.$bvModal.show("updateProductFormModal");
            }
        }

    }
}
</script>