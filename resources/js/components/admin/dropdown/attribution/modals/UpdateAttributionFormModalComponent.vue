<template>
    <div>
        <b-modal id="updateAttributionFormModal" :hide-footer="true" title="Update Attribution">
            <div class="form-group" v-if="attribution != null">
                <label>Name</label>
                <input type="text" class="form-control" v-model="attribution.name" />
            </div>
            <div class="form-group" v-if="attribution != null">
                <label>Category</label>
                <select class="form-control" v-model="attribution.category">
                    <option value="null">--Select Category--</option>
                    <option value="1">Facebook</option>
                    <option value="2">Google</option>
                    <option value="3">Lazada</option>
                    <option value="4">Shopee</option>
                    <option value="5">Organic</option>
                    <option value="6">Referral</option>
                </select>
            </div>
            <div class="form-group" v-if="attribution != null && (attribution.category == 1 || attribution.category == 2)">
                <label>Campaign Name</label>
                <input type="text" class="form-control" v-model="attribution.campaign_name" />
            </div>
            <div class="form-group" v-if="attribution != null">
                <label class="form-label">Distribution Channel</label>
                <select class="form-select" v-model="attribution.distribution_channel_id">
                    <option value="null">--Select--</option>
                    <option v-for="distributionChannel in distributionChannels" :value="distributionChannel.id" :key="distributionChannel.id">
                        {{ distributionChannel.name }}
                    </option>
                </select>
            </div>
            <div class="form-group" v-if="attribution != null">
                <label>Is Active</label>
                <select class="form-control" v-model="attribution.is_active">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <button class="btn btn-success" @click="updateAttribution">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        attribution: {
            type: Object,
            required: false,
            notes: "model",
        }
    },
    data: function() {
        return {
            distributionChannels: [],
        }
    },
    mounted(){
        this.fetchDistributionChannel();
    },
    methods: {
        updateAttribution() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "Are you sure you want to update Attribution?",
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
                            url: "/ajax/admin/dropdown/attribution/update",
                            data: self.attribution,
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
                                    self.$bvModal.hide("updateAttributionFormModal");
                                    self.$emit('update-attribution');
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
        fetchDistributionChannel(){
            axios.get('/ajax/admin/dropdown/fetch/distribution-channels/data')
            .then(response => {
                this.distributionChannels = response.data;
            })
            .catch(error => {
                console.log(error);
            })
        },
    }
}
</script>