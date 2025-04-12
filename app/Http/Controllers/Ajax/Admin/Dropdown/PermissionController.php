<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Permissions;
use App\Models\Role; 
use App\Models\ActivityLogs;

class PermissionController extends Controller
{
    public function addRolePermissions(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        // Create a new Permissions instance
        $rolePermission = new Permissions();
        $rolePermission->name = $validatedData['name'];
        $rolePermission->description = $validatedData['description'];
        $rolePermission->save();

        $this->logActivity('rolePermission', $rolePermission);

        return response()->json(['success' => true, 'message' => 'Role permission added successfully']);
    }

    //For the Permission activity log create
    private function logActivity($activity, $rolePermission){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Permission ' . $rolePermission->name . ' w/ Role Description ' . $rolePermission->description;

        ActivityLogs::create([
            'source' => 'Permission',
            'admin_id' => $admin_id,
            'name' => $rolePermission->name,
            'activity' => $activity
        ]);

    }

    public function getRolePermissions($roleId)
    {
        // Fetch all permissions
        $permissions = Permissions::all();

        // Fetch selected permissions for the role (assuming a role_permission table exists)
        $selectedPermissions = DB::table('role_permission')
            ->where('role_id', $roleId)
            ->pluck('permission_id');

        return response()->json(['permissions' => $permissions, 'selectedPermissions' => $selectedPermissions]);
    }

   

      public function assignPermissions(Request $request)
    {
        $validatedData = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($validatedData['role_id']);

        // Sync permissions for the role
        $role->permissions()->sync($validatedData['permissions']);

        return response()->json(['success' => true, 'message' => 'Permissions assigned to role successfully']);
    }


    
    public function updatePermission(Request $request, $roleId, $permissionId)
    {
        $request->validate([
            'checked' => 'required|boolean',
        ]);
    
        $role = Role::findOrFail($roleId);
        $permission = Permissions::findOrFail($permissionId);
    
        if ($request->input('checked')) {
            $role->permissions()->attach($permissionId);
            $this->assignRolelogActivity("Assigned permission '{$permission->name}' to role '{$role->name}'", $role);//For assigning permission to role then logs to activty log
        } else {
            $role->permissions()->detach($permissionId);
            $this->assignRolelogActivity("Revoked permission '{$permission->name}' from role '{$role->name}'", $role);//For revoking permission to role then logs to activity log 
        }
    
        return response()->json(['success' => true, 'message' => 'Permission updated successfully']);
    }
    
    
    //For the Permission activity log assigning permission to role
    private function assignRolelogActivity($activity, $role)
    {
        $admin_id = auth()->guard('admins')->id();
        ActivityLogs::create([
            'source' => 'Permission',
            'admin_id' => $admin_id,
            'name' => $role->name,
            'activity' => $activity
        ]);
    }
    
    

}
