<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\AdminRole;
use App\Models\Role;
use App\Models\ActivityLogs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if ($column === "created_at_s") {
            $column = "created_at";
        } elseif ($column === "first_name") {
            $column = "first_name";
        }

        $adminQuery = Admin::orderBy($column, $request->order);

        if (isset($input["keyword"])) {
            $adminQuery->where(function ($query) use ($input) {
                $query->where("first_name", "like", "%" . $input["keyword"] . "%")
                      ->orWhere("last_name", "like", "%" . $input["keyword"] . "%")
                      ->orWhere("email", "like", "%" . $input["keyword"] . "%");
            });
        }

        $admins = $adminQuery->paginate($request->per_page);

        foreach ($admins as $admin) {
            $admin->created_at_s = Carbon::parse($admin->created_at)->toDayDateTimeString();
            $admin->fullName = $admin->fullName();
            $admin->admin_role = $admin->admin_roles->pluck('name')->implode(',');
        }

        return [
            "success" => true,
            "message" => "",
            "data" => $admins
        ];
    }
    
    public function create(Request $request)
    {
        $validation_rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:admins',
            'password' => 'required|min:6',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $admin = new Admin();
        $admin->first_name = $input["first_name"];
        $admin->last_name = $input["last_name"];
        $admin->email = $input["email"];
        $admin->password = Hash::make($input["password"]);
        $admin->save();

        //logs the created admin data to activitylog
        $this->logActivity('admin', $admin );

        return array("success" => true, "message" => "Admin Created Successfully!", "data" => "");
    }



    //For the admin activity log create
    private function logActivity($activity, $admin){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created An Admin Account ' . $admin->first_name . ' ' . $admin->last_name;

        ActivityLogs::create([
            'source' => 'Admin',
            'admin_id' => $admin_id,
            'name' => $admin->first_name . ' ' . $admin->last_name,
            'activity' => $activity
        ]);

    }



    public function updateAdminRolePermission(Request $request)
    {

        $input = $request->all();

        $admin = null;

        if (isset($input["admin_id"])) {
            $admin = Admin::find($input["admin_id"]);
            if ($admin == null) {
                return array("success" => false, "message" => "Can't find Admin", "data" => null);
            }
        } else {
            return array("success" => false, "message" => "Admin ID is required!", "data" => null);
        }

        $getRoleArray = array();
        if (isset($input["admin_roles"])) {
            $getRoleArray = $input["admin_roles"];

            $syncData = [];
            foreach ($getRoleArray as $roleId) {
                $syncData[$roleId] = [
                    'role_id' => $roleId, // Add role_id to the sync data
                    'create' => isset($input["admin_permissions"][$roleId]['create']) ? $input["admin_permissions"][$roleId]['create'] : false,
                    'read'   => isset($input["admin_permissions"][$roleId]['read']) ? $input["admin_permissions"][$roleId]['read'] : false,
                    'update' => isset($input["admin_permissions"][$roleId]['update']) ? $input["admin_permissions"][$roleId]['update'] : false,
                    'delete' => isset($input["admin_permissions"][$roleId]['delete']) ? $input["admin_permissions"][$roleId]['delete'] : false,
                ];
            }
        }

        $admin->admin_roles()->sync($syncData);

        //For the Admin Role to Activity Log
        $this->updateAdminRoleLogActivity($roleId, $admin);

        return array("success" => true, "message" => "Admin Role is Updated!", "data" => null);
    }


    //For the Admin Role activity log update
    private function updateAdminRoleLogActivity($roleId, $admin){
        $admin_id = auth()->guard('admins')->id();
        $role = Role::find($roleId); // Retrieve the role instance
        $activity = 'Assign Role ' . $role->name . ' for ' . $admin->first_name . ' ' . $admin->last_name;
        ActivityLogs::create([
            'source' => 'Admin',
            'admin_id' => $admin_id,
            'name' => $admin->first_name . ' ' . $admin->last_name,
            'activity' => $activity
        ]);
    }


    public function adminApi(Request $request)
    {
        $query = Admin::orderBy('first_name', 'asc');
        $admins = $query->get();

        return array("success" => true, "message" => "", "data" => $admins);
    }

    
    //public function for delete admin accounts
    public function delete($id)
    {
        $admin = Admin::find($id);

        $admin->delete();

        $this->deleteLogActivity('admin', $admin);
    
        return response()->json(["success" => true, "message" => "Admin deleted successfully"], 200);
    }
    

    
    //For the admin delete activity log update
    private function deleteLogActivity($activity, $admin,){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Deleted Admin Account ' . $admin->first_name . ' ' . $admin->last_name;

        ActivityLogs::create([
            'source' => 'Admin',
            'admin_id' => $admin_id,
            'name' => $admin->first_name . ' ' . $admin->last_name,
            'activity' => $activity
        ]);

    }





    //function for update admin account details
    public function update(Request $request, $id){

        $admin = Admin::find($id);

        $validation_rules = [
            
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:admins,email,' . $id,

        ];

        $request->validate($validation_rules);

        $previous_name = $admin->first_name . ' ' . $admin->last_name;//For the activity log
        $admin->first_name = $request->input('first_name');
        $admin->last_name = $request->input('last_name');
        $previous_email = $admin->email;//For the activity log
        $admin->email = $request->input('email');

       if($admin->save()){

        $this->updateLogActivity('admin', $admin, $previous_name, $previous_email);

        return response()->json(['success' => true, 'message' => 'Admin details has been updated successfully']);

       }else{

        return response()->json(['success' => false, 'message' => 'Failed to update admin details']);


       }


    }

    //For the update admin activity log update
    private function updateLogActivity($activity, $admin, $previous_name, $previous_email){

        $admin_id = auth()->guard('admins')->id();
        $activity = '';
        $changes = [];

        if(strcmp($admin->first_name . ' ' .$admin->last_name, $previous_name) !== 0){
            $changes[] = 'Updated An Admin Account From ' . $previous_name . ' to ' . $admin->first_name . ' ' . $admin->last_name;
        }
        if(strcmp($admin->email, $previous_email) !== 0){
            $changes[] = 'Updated An Admin Email From ' . $previous_email . ' to ' . $admin->email;
        }
        if(count($changes) > 0){
            $activity = implode(' and ', $changes);
        }

        ActivityLogs::create([
            'source' => 'Admin',
            'admin_id' => $admin_id,
            'name' => $admin->first_name . ' ' . $admin->last_name,
            'activity' => $activity
        ]);
    
    }
   

    //function for change password
    public function changePassword(Request $request){

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if($validator->fails()){

            return response()->json(['success' => false, 'message' => $validator->errors()->first(), 400]);
            
        }

        $admin = Auth::guard('admins')->user();

        if(!Hash::check($request->current_password, $admin->password)){

            return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 400);

        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return response()->json(['success' => true, 'message' => 'Password Changed Successfully.']);

    }


    public function disabledAccounts(){

        $disabledAccounts = Admin::onlyTrashed()->get();

        foreach($disabledAccounts as $admin){

            $admin->admin_role = $admin->admin_roles->pluck('name')->implode(',');

        }

        return response()->json(['success' => true, 'message' => 'Disabled Accounts Retrieved Successfully', 'data' => $disabledAccounts]);

    }

    public function confirmRestore($id){

        $admin = Admin::withTrashed()->find($id);

        if($admin){

            $admin->restore();
            return response()->json(['success' => true, 'message' => 'Admin Account Has Been Successfully Restored'], 200);

        }else{

            return response()->json(['success' => false, 'message' => 'Admin Not Found'], 404);

        };

    }

}
