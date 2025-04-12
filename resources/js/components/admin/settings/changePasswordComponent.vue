<template>
    <div>

        <div class="container-md">

            <div class="change-password-settings text-center pt-5">

                <div class="row border rounded-4 bg-white" style="padding: 20px 300px 20px 300px">

                    <!-- <form @submit.prevent="changePassword"> -->
                
                        <Div class="col-md-12 pb-3">    
                            <label class="form-label">Enter Current Password</label>
                            <div class="input-group">
                                <input :type="currentEnterPassword" class="form-control w-50" v-model="currentPassword" id="currentPassword" required>
                                <button type="password" @click="currentPasswordVisiblity" class="btn btn-primary">
                                    <span v-if="currentEnterPassword === 'password'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                        <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
                                        <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
                                        </svg>
                                    </span>
                                    <span v-else>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        
                        </Div>

                        <Div class="col-md-12 pb-3">

                            <label class="form-label">Enter New Password</label>
                            <div class="input-group">

                                <input :type="NewPasswordFieldType" class="form-control w-50 m-auto" v-model="newPassword" id="newPassword" required>
                                <button type="password" @click="newPasswordVisibility" class="btn btn-primary">
                                    <span v-if="NewPasswordFieldType === 'password'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                        <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
                                        <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
                                        </svg>
                                    </span>
                                    <span v-else>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                        </svg>
                                    </span>
                                </button>

                            </div>
                         
                        </Div>

                        <Div class="col-md-12 pb-3">

                            <label class="form-label">Confirm New Password</label>
                            <div class="input-group">

                                <input :type="confirmNewPasswordFieldType" class="form-control w-25 m-auto" v-model="confirmNewPassword" id="confirmNewPassword" required>
                                <button type="button" class="btn btn-primary" @click="confirmPasswordVisibility">
                                    <span v-if="confirmNewPasswordFieldType === 'password'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                        <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
                                        <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
                                        </svg>
                                    </span>
                                    <span v-else>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                        </svg>
                                    </span>
                                </button>

                            </div>
                     
                        </Div>

                        <div class="changePassButton">

                            <button class="btn btn-success" @click="changePassword">Change Password</button>
                            
                        </div>

                    <!-- </form> -->

                </div>

            </div>

        </div>

    </div>
</template>

<script>
import Swal from 'sweetalert2';

    export default{
     
        data(){

            return {

                currentPassword: '',
                newPassword: '',
                confirmNewPassword: '',
                NewPasswordFieldType: 'password',
                confirmNewPasswordFieldType: 'password',
                currentEnterPassword: 'password'

            };

        },
        methods: {
            async changePassword() {
                if (this.newPassword !== this.confirmNewPassword) {
                    Swal.fire('Error', 'Your New Password & And Confirm Password Does Not Match', 'error');
                    return;
                }

                try {
                    let response = await axios.post('/ajax/admin/change-password', {
                        current_password: this.currentPassword,
                        new_password: this.newPassword,
                        new_password_confirmation: this.confirmNewPassword
                    });

                    if (response.data.success) {
                        Swal.fire('Success', 'Password changed successfully', 'success');
                        this.$emit('password-changed');
                        this.currentPassword = '';
                        this.newPassword = '';
                        this.confirmNewPassword = '';
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'An error occurred while changing the password', 'error');
                }
            },

            newPasswordVisibility(){

                this.NewPasswordFieldType = this.NewPasswordFieldType === 'password' ? 'text' : "password";

            },
            confirmPasswordVisibility(){

                this.confirmNewPasswordFieldType = this.confirmNewPasswordFieldType === 'password' ? 'text' : "password";


            },
            currentPasswordVisiblity(){

                this.currentEnterPassword = this.currentEnterPassword === 'password' ? 'text' : "password";

            }
            
        }

        
    }

</script>