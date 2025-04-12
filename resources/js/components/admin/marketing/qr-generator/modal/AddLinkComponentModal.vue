<template>
    <div>
        <b-modal id="addQRLink" title="Add Link" :hide-footer="true">
            <div class="form-group">
                <label>Input Link</label>
                <input type="text" class="form-control" v-model="link"/>
            </div>
            <button class="btn btn-primary" @click="addLink">Generate QR</button>
            <hr>
            <div class="container" v-if="qrCode">
                <div class="row">
                    <img :src="qrCode" class="img-fluid" />
                </div>
            </div>
        </b-modal>
    </div>
</template>
<script>
export default{

    data(){
        return{
            link: '',
            qrCode: null
        }
    },

    methods:{
        addLink() {
            self = this;
            
            Swal.fire({
                title: "Add",
                text: "Are you sure you want to generate this link?",
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
                            url: "/ajax/admin/marketing/qr-generator/add-qr-link",
                            data: {
                                link: self.link
                            },
                            config: { headers: { "Content-Type": "application/json" } },
                        }).then(function (response) {
                            self.$emit("refresh-link-data-table");
                            self.clearForm();
                            self.qrCode = response.data.qrCode;
                            if(response.data.success) {
                                Swal.fire({
                                    title: response.data.message,
                                    text: "",
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "Okay",
                                })
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
        clearForm(){
            this.link = '';
        }

    }

}

</script>