<template>
    <div>
        <b-modal id="updateRoleAdminFormModal" title="Update Role" :hide-footer="true" size="lg">
            <div class="form-group" v-if="currentAdminRole != null">
                <label>Name</label>
                <input type="text" class="form-control" v-model="currentAdminRole.fullName" disabled/>
            </div>
            <div class="form-group" v-if="currentAdminRole != null">
                <label>Role</label>
                <div class="table-responsive">
                    <table class="table text-nowrap text-md-nowrap table-bordered table-striped mg-b-0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" @change="selectAll" v-model="allSelected"/>Select All</th>
                                <th>Name</th>
                                <!--
                                <th>Create</th>
                                <th>Read</th>
                                <th>Update</th>
                                <th>Delete</th>
                                -->
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tr v-for="role in roles" :key="role.id">
                                <td><input type="checkbox" v-model="selected" :value="role.id"/></td>
                                <td>{{role.name}}</td>
                                <!--
                                <td v-if="isSelected(role.id)"><b-form-checkbox name="checkbox1" value="1" unchecked-value="0" v-model="getAdminPermission(role.id).create"></b-form-checkbox></td>
                                <td v-if="isSelected(role.id)"><b-form-checkbox name="checkbox2" value="1" unchecked-value="0" v-model="getAdminPermission(role.id).read"></b-form-checkbox></td>
                                <td v-if="isSelected(role.id)"><b-form-checkbox name="checkbox3" value="1" unchecked-value="0" v-model="getAdminPermission(role.id).update"></b-form-checkbox></td>
                                <td v-if="isSelected(role.id)"><b-form-checkbox name="checkbox4" value="1" unchecked-value="0" v-model="getAdminPermission(role.id).delete"></b-form-checkbox></td>
                                -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <button class="btn btn-success" @click="updateRoleAdmin">Update</button>
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        admin: {
            type: Object,
            required: false,
            notes: "model",
        }
    },
    data: function() {
        return {
            roles: [],
            selected: [],
            allSelected: false,
            admin_role: {},
            admin_permissions: {},
            currentAdminRole: null,
        }
    },
    watch: {
        admin: {
            handler: function (admin) {
                this.currentAdminRole = JSON.parse(JSON.stringify(admin));
                this.initializeAdminPermissions();
            },
            immediate: true,
        }
    },
    mounted() {
        var app = this;
        app.loadRoles();
        app.initializeAdminPermissions();
    },
    methods: {
        isSelected(roleId) {
            return this.selected.includes(roleId);
        },
        getAdminPermission(roleId) {
            // Get or create admin permission object for the given role
            if (!this.admin_permissions[roleId]) {
                this.$set(this.admin_permissions, roleId, {
                    create: 0,
                    read: 0,
                    update: 0,
                    delete: 0,
                });
            }
            return this.admin_permissions[roleId];
        },
        initializeAdminPermissions() {
            // Initialize admin permissions based on currentAdminRole
            this.admin_permissions = {};
            if (this.currentAdminRole) {
                this.selected = this.currentAdminRole.admin_roles.map(role => role.id); // Replace existing selected roles
                this.currentAdminRole.admin_roles.forEach((role) => {
                    const roleId = role.id;
                    // this.selected.push(roleId); // Corrected line to add role ID to the array
                    this.$set(this.admin_permissions, roleId, {
                        create: role.pivot.create,
                        read: role.pivot.read,
                        update: role.pivot.update,
                        delete: role.pivot.delete,
                    });
                    this.isSelected(roleId);
                });
            }
        },
        loadRoles() {
            self = this;
            axios.get("/ajax/admin/dropdown/role/roleApi?").then(function (resp) {
                self.roles = resp.data.data;
                self.selectAll();
            })["catch"](function (resp) {
                alert("Could not load roles");
            });
        },
        selectAll() {
            if (this.allSelected) {
                const selected = this.roles.map((a) => a.id);
                this.selected = selected;
            } else {
                this.selected = [];
            }
        },
        updateRoleAdmin() {
            var self = this;

            var data = {
                admin_id: self.admin.id,
                admin_roles: self.selected,
                admin_permissions: self.admin_permissions,
            };

            Swal.fire({
                title: "Update",
                text: "are you sure you want to update admin role permission?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8ad919",
                cancelButtonColor: "#d33",
                confirmButtonText: "Update",
                showLoaderOnConfirm: true,
                preConfirm: function() {
                    return new Promise (function (resolve) {
                        axios({
                            method: "post",
                            url: "/ajax/admin/dropdown/admin/update/admin-role-permission",
                            data: data,
                            config: { headers: { "Content-Type" : "application/json" } },
                        }).then(function (response) {
                            if (response.data.success) {
                                Swal.fire({
                                    title: response.data.message,
                                    text: "",
                                    icon: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "Okay",
                                }).then((result) => {
                                    self.$emit("update-admin-role-permission");
                                    self.$bvModal.hide("updateRoleAdminFormModal");
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
                            if (response.response.status == 422) {
                                var key = Object.keys(response.response.data.errors)[0];
                                var errorMessage = response.response.data.errors[key][0];
                                Swal.fire({
                                    title: errorMessage,
                                    text: "",
                                    icon: "error",
                                    showCancelButton: false,
                                    confirmButtonText: "Okay",
                                }).then((result) => {
                                    
                                });
                            }
                        });
                    });
                },
            }).then((result) => {
                if (!result.isConfirmed) {
                }
            });
        }
    }
}
</script>