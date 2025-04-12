<template>
    <div>
        <b-modal id="editDailyAdsAuditFormModal" title="Update Daily Ads Audit" :hide-footer="true">
            <div class="form-group" v-if="daily_audit != null">
                <label>Date Ads Spent</label>
                <input type="date" class="form-control" v-model="daily_audit.date_ad_spent"/>
            </div>
            <div class="form-group" v-if="daily_audit != null">
                <label>Facebook Ads Spent</label>
                <input type="number" class="form-control" v-model="daily_audit.facebook_ad_spent" />
            </div>
            <div class="form-group" v-if="daily_audit != null">
                <label>Google / Website Ad Spent</label>
                <input type="number" class="form-control" v-model="daily_audit.google_ad_spent" />
            </div>
            <div class="form-group" v-if="daily_audit != null">
                <label>Lazada Ad Spent</label>
                <input type="number" class="form-control" v-model="daily_audit.lazada_ad_spent" />
            </div>
            <div class="form-group" v-if="daily_audit != null">
                <label>Shopee Ad Spent</label>
                <input type="number" class="form-control" v-model="daily_audit.shopee_ad_spent" />
            </div>
            <button class="btn btn-success" @click="updateDailyAdsAudit">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        daily_audit: {
            type: Object,
            required: false,
            notes: "model",
        }
    },
    data: function() {
        return {

        }
    },
    methods: {
        updateDailyAdsAudit() {
            self = this;

            Swal.fire({
                title: "Update",
                text: "Are you sure you want to update?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Update",
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/marketing/daily-ads-audit/update",
                            data: self.daily_audit,
                            config: { headers: { "Content-Type": "application/json" } },
                        })
                            .then(function (response) {
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
                                        self.$bvModal.hide("editDailyAdsAuditFormModal");
                                        self.$emit("update-daily-ads-audit");
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
        }
    }
}
</script>