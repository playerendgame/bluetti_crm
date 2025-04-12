<template>
    <div>
        <b-modal id="updateMOPModal" title="Update Mode Of Payment" :hide-footer="true">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="mop.name"/>
            </div>
            <button class="btn btn-primary" @click="updateMOP">Save Changes</button>
        </b-modal>
    </div>
</template>
<script>
export default{
    props: {
        mop: {
            type: Object,
            require: false,
            notes: "model",
        }
    },

    data: function(){
        return {
            mop: {}
        }
    },
    mounted() {
        this.fetchMOP();
    },
    methods: {
        fetchMOP(){
            axios.get(`/ajax/admin/dropdown/mode-of-payment/fetch/${this.mopID}`)
            .then(response => {
                this.mop = response.data.data;
            })
            .catch(error => {
                console.error(error);
            })
        },
        updateMOP(){
            axios.put(`/ajax/admin/dropdown/update/mode-of-payment/${this.mop.id}`, {
                name: this.mop.name
            })
            .then(response => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.data.message
                })
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.response.data.message
                })
            })
        }
    }
}

</script>