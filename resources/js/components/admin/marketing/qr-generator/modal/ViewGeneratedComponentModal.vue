<template>
    <div>
        <b-modal :id="`view-generated-link-${linkId}`" title="Generated QR" :hide-footer="true">
            <div class="form-group">
                <label>Link</label>
                <input type="text" class="form-control" disabled v-model="qr_links.link"/>
            </div>
            <hr>
            <div class="container" v-if="qrCodeBase64">
                <div class="row">
                    <img :src="qrCodeBase64" class="img-fluid" />
                </div>
            </div>
        </b-modal>
    </div>
</template>
<script>
export default{
    props: {
        linkId: {
            type: Number,
            required: true
        }
    },

    data(){
        return{
            qr_links: {},
            qrCode: '',
            qrCodeBase64: ''
        }
    },
    mounted(){
        this.fetchQrLink();
    },
    methods: {
        fetchQrLink(){
            const linkId = this.linkId;
            axios.get(`/ajax/admin/marketing/fetch-qr-links/${linkId}`)
            .then(response => {
                this.qr_links = response.data.qr_links;
                this.qrCodeBase64 = response.data.qrCodeBase64;
            })
            .catch(error => {
                console.error(error);
            })
        },
    }

}

</script>