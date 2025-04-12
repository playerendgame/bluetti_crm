<template>
    <div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row row-sm">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Date Ad Spent</label>
                                    <input type="date" class="form-control" v-model="daily_ad_spent.date_ad_spent"/>
                                </div>
                                <div class="form-group">
                                    <label>Facebook Ad Spent</label>
                                    <input type="number" class="form-control" v-model="daily_ad_spent.facebook_ad_spent"/>
                                </div>
                                <div class="form-group">
                                    <label>Google / Website Ad Spent</label>
                                    <input type="number" class="form-control" v-model="daily_ad_spent.google_ad_spent"/>
                                </div>
                                <div class="form-group">
                                    <label>Lazada Ad Spent</label>
                                    <input type="number" class="form-control" v-model="daily_ad_spent.lazada_ad_spent"/>
                                </div>
                                <div class="form-group">
                                    <label>Shopee Ad Spent</label>
                                    <input type="number" class="form-control" v-model="daily_ad_spent.shopee_ad_spent"/>
                                </div>
                                <button class="btn btn-primary" @click="addDailyAdSpent">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            daily_ad_spent: {
                date_ad_spent: '',
                facebook_ad_spent: 0,
                google_ad_spent: 0,
                lazada_ad_spent: 0,
                shopee_ad_spent: 0,
            }
        }
    },
    methods: {
        addDailyAdSpent() {
            self = this;

            Swal.fire({
                title: "Add",
                text: "Are you sure you want to add new Daily Ad Spent?",
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
                            url: "/ajax/admin/marketing/daily-ads-audit/create",
                            data: self.daily_ad_spent,
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
                                    window.location.href = "/admin/marketing/daily-ads-audit";
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
        }
    }
}
</script>