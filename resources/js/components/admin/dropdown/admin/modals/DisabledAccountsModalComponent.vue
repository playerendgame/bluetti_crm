<template>
    <div>
        <b-modal id="disabledAccountsModal" title="Disabled Accounts" :hide-footer="true" size="xl">
            <div class="tableDataContainer table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <th>Actions</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Admin Role</th>
                    </thead>
                    <tbody>
                        <tr v-for="admin in disabledAccounts" :key="admin.id">
                            <td>
                                <button class="btn btn-success" @click="confirmRestore(admin.id)">Restore</button><!--Add Restore Functionality-->
                            </td>
                            <td>{{ admin.first_name }}</td>
                            <td>{{ admin.last_name }}</td>
                            <td>{{ admin.email }}</td>
                            <td>{{ admin.admin_role }}</td>
                        </tr>
                
                    </tbody>
                </table>
            </div>
        </b-modal>
    </div>
</template>

<script>
import Swal from 'sweetalert2';



    export default {
        data(){

            return{

                disabledAccounts: []

            };

        },

        mounted(){

            this.fetchDisabledAccounts();
        },

        methods: {

            fetchDisabledAccounts(){
                axios.get('/ajax/admin/dropdown/admin/disabled')
                .then(response => {

                    this.disabledAccounts = response.data.data;
                    if (this.$emit('disabled-admin')) {
                        this.fetchDisabledAccounts();
                    }

                })
                .catch(error => {

                    console.error('Error Fetching Disabled Accounts', 'error');

                })
            },
            

            confirmRestore(adminId){

                Swal.fire({

                    title: 'Are You Sure?',
                    text: 'You Are About To Restore This Admin Account',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    confirmButtonText: 'Restore'

                }).then((result) => {

                    if(result.isConfirmed){
                        this.restoreAccount(adminId)
                    }

                })

            },

            restoreAccount(adminId){

                axios.post(`/ajax/admin/dropdown/admin/revive/${adminId}`)
                .then(response => {

                    if(response.data.success){

                        this.fetchDisabledAccounts();
                        Swal.fire(
                            'Restored!',
                            'The Admin Account Has Been Restored',
                            'success'
                        )
                        this.$bvModal.hide('disabledAccountsModal');//to hide modal if confirmed restored
                        this.$emit('disabled-admin');

                    }else{

                        Swal.fire(
                            'Failed',
                            'Failed To Restore Admin Account',
                            'error'
                        );
                        //this.refreshAjaxUrl();
  
                    }

                })
                .catch(error => {
                    console.error('Error Restoring Admin:', error)
                    Swal.fire(
                        'Failed!',
                        'There Was An Error Restoring Admin',
                        'error'
                        );
           
                  
                });
                
            }

        }

    }

</script>

