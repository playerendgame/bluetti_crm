<template>
    <b-modal id="addRegion" :hide-footer="true" title="Add Region">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label">Region Name</label>
                    <input class="form-control" type="text" v-model="region.name">
                </div>
            </div>
            <div class="row pt-3">
                <div class="col-md-12">
                    <label class="form-label">Is Active</label>
                    <select class="form-select" v-model="region.is_active">
                        <option value="0">No</option>
                        <option value="1" selected>Yes</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="buttons text-center">
                        <button class="btn btn-success" @click="create">Save Changes</button>
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
            region: {
                name: '',
                is_active: '',
            }
        }
    },
    methods:{
        create() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "Are you sure you want to add Region?",
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
                            url: "/ajax/admin/dropdown/region/create",
                            data: self.region,
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
                                    self.status = 0;
                                    self.$bvModal.hide("addRegion");
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