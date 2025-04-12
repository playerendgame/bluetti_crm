<template>
    <b-modal id="updateDistChannel" :hide-footer="true" title="Update Distribution Channel">
        <div class="container">
            <div class="row" v-if="distribution_channel">
                <div class="col-md-12">
                    <label class="form-label">Ditribution Channel</label>
                    <input class="form-control" type="text" v-model="distribution_channel.name">
                </div>
            </div>
            <hr>
            <div class="row" v-if="distribution_channel">
                <div class="col-md-12">
                    <div class="button text-center">
                        <button class="btn btn-success" @click="update">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </b-modal>
</template>
<script>
export default{
    props: {
        distribution_channel:{
            type: Object,
            required: true
        }
    },
    methods: {
        update() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "Are you sure you want to update Distribution Channel?",
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
                            url: "/ajax/admin/dropdown/distribution-channel/update",
                            data: self.distribution_channel,
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
                                    self.$bvModal.hide("updateDistChannel");
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