<template>
    <div>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Select Start Date</label>
                    <input type="date" class="form-control" v-model="currentFilters.start_date"/>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Select End Date</label>
                    <input type="date" class="form-control" v-model="currentFilters.end_date"/>
                </div>
            </div>
            <div class="col-md-2 col-sm-12">
                <button class="btn btn-success" @click="refreshData">Update</button>
            </div>
        </div>
        <div class="row mt-3" v-if="refresh > 1">
            <div class="col-md-3 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">Start Customers</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.start_customers }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">End Customers</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.end_customers }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">New Customers</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.new_customers }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="card-item">
                            <div class="card-item-title mb-2">
                                <label class="main-content-label tx-13 font-weight-bold mb-1">%</label>
                            </div>
                            <div class="card-item-body">
                                <div class="card-item stat">
                                    <h4 class="font-weight-bold">{{ data.percentage }}</h4>
                                </div>
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
            currentFilters: {
                start_date: 0,
                end_date: 0,
            },
            data: {},
            refresh: 0,
        }
    },
    mounted() {
        this.refreshData();
    },
    methods: {
        refreshData() {
            var self = this;
            let loader = this.$loading.show({
                container: null,
                canCancel: false,
                onCancel: null,
            });

            var url = "/ajax/admin/customers/dashboard?";
            this.refresh++;
            if (this.currentFilters.start_date != null && this.currentFilters.end_date != null) {
                url +=
                    "start_date=" +
                    this.currentFilters.start_date +
                    "&end_date=" +
                    this.currentFilters.end_date +
                    "&refresh=" + this.refresh;
            }

            axios.get(url).then(function (resp) {
                loader.hide();
                self.data = resp.data.data;
            })["catch"](function (resp) {
                loader.hide();
                alert("Could not load Data");
            });
        }
    }
}
</script>