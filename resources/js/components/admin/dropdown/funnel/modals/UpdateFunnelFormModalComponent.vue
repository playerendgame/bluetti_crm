<template>
    <div>
        <b-modal id="updateFunnelFormModal" title="Update Funnel" :hide-footer="true">
            <div class="form-group" v-if="currentFunnel">
                <label>Name</label>
                <input type="text" class="form-control" v-model="currentFunnel.name" />
            </div>
            <div class="form-group" v-if="currentFunnel">
                <label>Attribution</label>
                <select class="form-control" v-model="currentFunnel.attribution_id">
                    <option :value="null">--Select--</option>
                    <option v-for="(attribution, index) in attributions" :key="attribution.id" :value="attribution.id">
                        {{ attribution.name }}
                    </option>
                </select>
            </div>
            <div class="form-group" v-if="currentFunnel">
                <label>Campaign Name</label>
                <input type="text" class="form-control" v-model="currentFunnel.campaign_name" />
            </div>
            <div class="form-group" v-if="currentFunnel">
                <label>Is Active</label>
                <select class="form-control" v-model="currentFunnel.is_active">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <button class="btn btn-success" @click="updateFunnel">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        funnel: {
            type: Object,
            required: false,
            notes: "model",
        }
    },
    data: function() {
        return {
            currentFunnel: null,
            attributions: [],
        }
    },
    watch: {
        funnel: {
            handler: function (funnel) {
                this.currentFunnel = JSON.parse(JSON.stringify(funnel));
            },
            immediate: true,
        }
    },
    mounted() {
        this.refreshAttributions();
    },
    methods: {
        updateFunnel() {
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
                            url: "/ajax/admin/dropdown/funnel/update",
                            data: self.currentFunnel,
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
                                    self.$bvModal.hide("updateFunnelFormModal");
                                    self.$emit('update-funnel');
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
        refreshAttributions() {
            var self = this;
            axios.get("/ajax/admin/dropdown/attribution/api").then(function (resp) {
                self.attributions = resp.data.data.filter(attribution => attribution.campaign_name != null);
            })["catch"](function (resp) {
                alert("Could not load Attributions");
            });
        },
    }
}
</script>