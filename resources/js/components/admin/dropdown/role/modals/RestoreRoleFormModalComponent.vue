<template>
    <div>
        <b-modal id="restoreRoleModal" size="md" title="Disabled Role" :hide-footer="true" >
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <th>Actions</th>
                    <Th>Role</Th>
                    <th>Descriptions</th>
                </thead>
                <tbody>
                    <tr v-for="role in disabledRoles" :key="role.id">
                        <td>
                            <button class="btn btn-success" @click="restore(role.id)">Restore Role</button>
                        </td>
                        <td>{{ role.name }}</td>
                        <td>{{ role.description }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </b-modal>
    </div>
</template>

<script>

export default {

    data(){

        return{

            disabledRoles: []

        };

    },

    mounted(){

        // this.fetchDisabledRoles();
    },

    methods: {

        fetchDisabledRoles(){

                axios.get('/ajax/admin/dropdown/role/disabled')
                .then(response => {

                    this.disabledRoles = response.data.data;

                    //Code to refresh modal table when item is deleted
                    if (this.$emit('disabled-role')) {
                        // this.fetchDisabledRoles();
                    }
           
                })
                .catch(error => {

                    console.error('Error Fetching Disabled Roles', 'error');

                })
            },

            restore(roleId){

            Swal.fire({

                title: 'Are You Sure?',
                text: 'You Are About To Restore This Role',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'green',
                confirmButtonText: 'Restore'

            }).then((result) => {

                if(result.isConfirmed){
                    this.restoreRole(roleId)
                }

            })

            },

            restoreRole(roleId){

            axios.post(`/ajax/admin/dropdown/role/revive/${roleId}`)
            .then(response => {

                if(response.data.success){

                    this.fetchDisabledRoles();
                    Swal.fire(
                        'Restored!',
                        'Role Has Been Restored',
                        'success'
                    )
                    this.$bvModal.hide('restoreRoleModal');//to hide modal if confirmed restored
                    this.$emit('disabled-role');

                }else{

                    Swal.fire(
                        'Failed',
                        'Failed To Restore Role',
                        'error'
                    );
                    this.refreshAjaxUrl();

                }

            })
            .catch(error => {
                console.error('Error Restoring Role:', error)
                Swal.fire(
                    'Failed!',
                    'There Was An Error Restoring Role',
                    'error'
                    );

            
            });

            }

            
            

    }

}

</script>