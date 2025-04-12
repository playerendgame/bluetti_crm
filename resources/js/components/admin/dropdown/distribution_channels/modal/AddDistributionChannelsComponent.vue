<template>
    <b-modal id="addDistributionChannels" :hide-footer="true" title="Add Distribution Channels">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label">Distribution Channel</label>
                    <input class="form-control" type="text" v-model="name">
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="button">
                        <button class="btn btn-success" @click="addDistChannel">Create</button>
                    </div>
                </div>
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
        addDistChannel(){
            let data = {
                name: this.name
            };
            axios.post('/ajax/admin/dropdown/add/distribution-channel', data)
            .then(response => {
                console.log(response);
                this.$emit('refreshAjaxUrl');
                this.$bvModal.hide('addDistributionChannels');
                this.name = '';
                Swal.fire({
                    icon: 'success',
                    text: 'Distribution Channel Added Successfully!',
                    title: 'Distribution Channel has been added successfully!'
                })
            })
            .catch(error => {
                console.log(error)
                Swal.fire({
                    icon: 'error',
                    title: 'Error Adding Distribution Channel',
                    text: 'There has been an error adding distribution channe!'
                })
            })
        }
    }

}

</script>