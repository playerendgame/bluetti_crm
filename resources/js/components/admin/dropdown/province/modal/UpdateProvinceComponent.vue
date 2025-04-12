<template>
    <b-modal id="updateProvince" :hide-footer="true" title="Update Province">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label">Province</label>
                    <input type="text" class="form-control" v-model="province.name">
                </div>
            </div>
            <div class="row pt-3">
                <div class="col-md-12">
                    <label class="form-label">Province</label>
                    <select class="form-select" v-model="province.region_id">
                        <option v-for="region in regions" :value="region.id" :key="region.id">
                            {{ region.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row pt-3">
                <div class="col-md-12">
                    <label class="form-label">Is Active</label>
                    <select class="form-select" v-model="province.is_active">
                        <option value="0">No</option>
                        <option value="1" selected>Yes</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="button text-center">
                    <button class="btn btn-success" @click="update">Save Changes</button>
                </div>
            </div>
        </div>
    </b-modal>
</template>
<script>
export default{

    props: {
        province:{
            type: Object,
            required: true
        }
    },

    data(){
        return{
            regions: []
        }
    },
    mounted(){
        this.fetchRegions();
    },  
    methods: {
        fetchRegions(){
            axios.get('/ajax/admin/orders/fetch-regions')
            .then(response => {
                this.regions = response.data;
            })
            .catch(error => {
                console.log(error);
            })
        },
        update() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "Are you sure you want to update Province??",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Update",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise(function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/dropdown/province/update",
                            data: self.province,
                            config: { headers: {"Content-Type": "application/json"} },
                        })
                        .then(function(response) {
                            if (response.data.success) {
                                Swal.fire({
                                    title: response.data.message,
                                    text: "",
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "Okay",
                                }).then((result) => {
                                    self.$bvModal.hide("updateProvince");
                                    self.$emit('refreshAjaxUrl');
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
                        })
                        .catch(function (response) {
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
                if (!result.value) {

                }
            });
        },
    }
}
</script>