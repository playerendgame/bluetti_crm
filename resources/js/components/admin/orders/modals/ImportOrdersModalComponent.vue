<template>
    <b-modal id="import-order-data" title="Import Order Data" :hide-footer="true">
        <div class="row p-3">
           <div class="col-12">
                <!-- <label class="form-label">Google Sheet URL or Upload Excel File</label>
                <input 
                    v-model="sheetUrl" 
                    class="form-control" 
                    type="text" 
                    placeholder="Paste Google Sheet URL"
                >
                <span>OR</span><br> -->
                <input 
                    ref="fileInput"
                    class="form-control" 
                    type="file" 
                    accept=".xlsx, .xls, .csv"
                    @change="handleFileUpload"
                >
            </div>
            <div class="col-12">
                <div class="mt-3">
                    <b-button class="btn btn-sm" variant="danger" id="importing_notes">
                        Notes
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-question-diamond-fill" viewBox="0 0 16 16">
                            <path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 0l6.516-6.516c.58-.58.58-1.519 0-2.098zM5.495 6.033a.237.237 0 0 1-.24-.247C5.35 4.091 6.737 3.5 8.005 3.5c1.396 0 2.672.73 2.672 2.24 0 1.08-.635 1.594-1.244 2.057-.737.559-1.01.768-1.01 1.486v.105a.25.25 0 0 1-.25.25h-.81a.25.25 0 0 1-.25-.246l-.004-.217c-.038-.927.495-1.498 1.168-1.987.59-.444.965-.736.965-1.371 0-.825-.628-1.168-1.314-1.168-.803 0-1.253.478-1.342 1.134-.018.137-.128.25-.266.25zm2.325 6.443c-.584 0-1.009-.394-1.009-.927 0-.552.425-.94 1.01-.94.609 0 1.028.388 1.028.94 0 .533-.42.927-1.029.927"/>
                        </svg>
                    </b-button>
                    <b-popover target="importing_notes"  triggers="hover" placement="top">
                        <template #title>Importing Notes:</template>
                        <ul>
                            <small>
                                <li CLASS="text-danger">Make sure to import an excel file with a sheet name "ONLINE TEAM SALES"</li>
                                <li CLASS="text-danger">Delete other sheets to prevent crashing</li>
                                <li CLASS="text-danger">Make sure there's a Sales Admin assigned for every order</li>
                                <li class="text-danger">To assign the specific admin to specific order, the first name or last name should be text accurate. EX: CRM(Ellayza) -> Sheet(Ellayza), not Elay</li>
                                <li CLASS="text-danger">Make sure there's an available products before importing</li>
                                <li CLASS="text-danger">Make the products in excel 1 liner as possible</li>
                            </small>
                        </ul>
                    </b-popover>
                
                    <input 
                        class="form-check-input d-none" 
                        type="checkbox" 
                        id="firstRowHeader" 
                        v-model="firstRowHeader"
                    >
                    <label class="form-check-label d-none" for="firstRowHeader">
                        First row contains headers
                    </label>
                </div>
            </div>
        </div>
        <hr>
        <div class="button d-flex justify-content-center">
            <button class="btn btn-success me-2" @click="submitImport">Submit</button>
            <button class="btn btn-danger" @click="closeModal">Close</button>
        </div>
    </b-modal>
</template>
<script>
export default{
    data(){
        return{
            // sheetUrl: '',
            file: null,
            firstRowHeader: true,
        }
    },

    methods:{
        closeModal(){
            this.$bvModal.hide('import-order-data');
        },
        handleFileUpload(event){
            this.file = event.target.files[0];
        },
        async submitImport(){
            Swal.fire({
                title: 'Importing Orders',
                text: 'Please wait while we process your order data',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            // formData.append('sheet_url', this.sheetUrl);
            formData.append('first_row_header', this.firstRowHeader ? '1' : '0'); // Convert to string
            if(this.file){
                formData.append('file', this.file);
            }

            try{
                const response = await axios.post('/ajax/admin/orders/import', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                
                Swal.fire({
                    icon: 'success',
                    title: 'Import Successful!',
                    text: response.data.message,
                    confirmButtonText: 'OK'
                });

                this.$emit('orders-imported');

                this.closeModal();
            } catch (error) {
                if (error.response?.data?.no_stock_products) {
                    const noStockList = error.response.data.no_stock_products.join('\n');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'No Stock Alert',
                        html: `
                            <div style="text-align: left; margin-top: 10px;">
                                <strong>No stocks left for products:</strong>
                                <pre style="background-color: #f8d7da; padding: 15px; border-radius: 5px; margin-top: 10px; text-align: left; border: 1px solid #f5c6cb;">${noStockList}</pre>
                            </div>
                        `,
                        confirmButtonText: 'OK',
                        width: '600px'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Failed',
                        text: error.response?.data?.message || 'An error occurred during import',
                        confirmButtonText: 'OK'
                    });
                }
            }
        }

    }
}

</script>