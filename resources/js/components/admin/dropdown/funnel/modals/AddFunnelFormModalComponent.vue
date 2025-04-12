<template>
    <div>
        <b-modal id="addFunnelFormModal" title="Add Funnel" :hide-footer="true">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="funnel.name" />
            </div>
            <div class="form-group">
                <label>Attribution</label>
                <select class="form-control" v-model="funnel.attribution_id">
                    <option value=''>--Select--</option>
                    <option v-for="(attribution, index) in attributions" :key="attribution.id" :value="attribution.id">
                        {{ attribution.name }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label>Campaign Name</label>
                <input type="text" class="form-control" v-model="funnel.campaign_name"/>
            </div>
            <div class="form-group">
                <label>Is Active</label>
                <select class="form-control" v-model="funnel.is_active">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <button class="btn btn-primary" @click="addFunnel">Add</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            funnel: {
                name: '',
                campaign_name: '',
                is_active: 1,
                attribution_id: '',
            },
            attributions: [],
        }
    },
    mounted() {
        var app = this;
        app.refreshAttributions();
    },
    methods: {
        addFunnel() {
            self = this;

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Funnel?",
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
                            url: "/ajax/admin/dropdown/funnel/create",
                            data: self.funnel,
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
                                    self.$bvModal.hide("addFunnelFormModal");
                                    self.$emit("add-funnel");
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
        clearForm() {
            this.funnel.name = '';
            this.funnel.campaign_name = '';
            this.funnel.is_active = 1;
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