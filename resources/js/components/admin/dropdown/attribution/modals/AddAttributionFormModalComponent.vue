<template>
    <div>
        <b-modal id="addAttributionFormModal" title="Add Attribution" :hide-footer="true">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="attribution.name" />
            </div>
            <div class="form-group">
                <label>Category</label>
                <select class="form-control" v-model="attribution.category">
                    <option value="">--Select Category--</option>
                    <option value="1">Facebook</option>
                    <option value="2">Website</option>
                    <option value="3">Lazada</option>
                    <option value="4">Shopee</option>
                    <option value="5">Organic</option>
                    <option value="6">Referral</option>
                </select>
            </div>
            <div class="form-group" v-if="attribution.category == 1 || attribution.category == 2">
                <label>Campaign Name</label>
                <input type="text" class="form-control" v-model="attribution.campaign_name"/>
            </div>
            <div class="form-group">
                <label class="form-label">Distribution Channel</label>
                <select class="form-select" v-model="attribution.distribution_channel_id">
                    <option value="" selected>--Choose Distribution Channel--</option>
                    <option v-for="distributionChannel in distributionChannels" :value="distributionChannel.id" :key="distributionChannel.id">
                        {{ distributionChannel.name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label>Is Active</label>
                <select class="form-control" v-model="attribution.is_active">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <button class="btn btn-primary" @click="addAttribution">Add</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            attribution: {
                name: '',
                category: '',
                campaign_name: '',
                distribution_channel_id: '',
                is_active: 1,
            },
            distributionChannels: [],
        }
    },
    mounted(){
        this.fetchDistributionChannel();
    },
    methods: {
        addAttribution() {
            self = this;
            
            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Attribution?",
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
                            url: "/ajax/admin/dropdown/attribution/create",
                            data: self.attribution,
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
                                    self.$bvModal.hide("addAttributionFormModal");
                                    self.$emit("add-new-attribution");
                                    self.clearForm();
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
        fetchDistributionChannel(){
            axios.get('/ajax/admin/dropdown/fetch/distribution-channels/data')
            .then(response => {
                this.distributionChannels = response.data;
            })
            .catch(error => {
                console.log(error);
            })
        },
        clearForm() {
            this.attribution.name = '';
            this.attribution.category = '';
            this.attribution.campaign_name = '';
            this.attribution.is_active = 1;
        }
    }
}
</script>