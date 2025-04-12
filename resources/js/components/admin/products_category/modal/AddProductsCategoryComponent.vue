<template>
    <b-modal id="addProductCategory" :hide-footer="true" title="Add Category">
        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Category Name</label>
                <input class="form-control" type="text" v-model="name">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="button text-center">
                <button class="btn btn-primary" @click="addCategory">Create</button>
            </div>
        </div>
    </b-modal>
</template>
<script>
export default{

    data(){
        return{
            name: '', 
        }
    },
    methods: {
        addCategory(){
            let data = {
                name: this.name
            };

            axios.post('/ajax/admin/products/add/category', data)
            .then(response => {
                this.name = '';
                this.$emit('refreshAjaxUrl');
                this.$root.$bvModal.hide('addProductCategory');
                console.log(response.data)
                Swal.fire({
                    icon: 'success',
                    title: 'Category Has Been Added!',
                    text: 'Category has been added successfully!',
                })
            })
            .catch(error => {
                console.log(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error Adding Category',
                    text: 'There has been an error adding category',
                })
            })
        }
    }


}

</script>