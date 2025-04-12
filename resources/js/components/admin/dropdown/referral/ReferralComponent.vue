<template>
    <div>
        <add-referrals-form-modal-component @add-referral="refreshAjaxUrl"/>
        <button class="btn btn-primary" @click="addReferral">Add</button>
        <hr>
        <datatable-component :fetch-url="ajaxUrl" :columns="columns" :buttons="filteredButtons" tableID="orders-table"
            v-on:button-click="onButtonClick" :defaultSortIndex="0" defaultSortOrder="desc"/>
    </div>
</template>

<script>
export default {
    props: {
        hasPermission: {
            type: Object,
            required: true,
        }
    },
    data: function() {
        let buttons = [];
        return {
            ajaxUrl: "",
            refresh: 0,
            referral: null,
            columns: [
                { name: "Name", field: 'name', sortable: true, show: true },
                { name: "Email", field: 'email', sortable: true, show: true },
                { name: "Date Created", field: 'created_at_s', sortable: true, show: true },
            ],
        }
    },
    mounted() {
        this.refreshAjaxUrl();
    },
    methods: {
        refreshAjaxUrl() {
            this.refresh++;
            let url = "/ajax/admin/referral/list?refresh=" + this.refresh + "&";
            this.ajaxUrl = url;
        },
        addReferral() {
            this.$bvModal.show('addReferralFormModal');
        }
    }
}
</script>