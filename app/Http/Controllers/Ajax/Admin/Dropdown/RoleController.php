<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\ActivityLogs;
use App\Models\AdminRole;

class RoleController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $roleQuery = Role::orderBy('created_at', 'desc');

        if (isset($input["keyword"])) {
            $roleQuery->where('name', "like", "%" . $input["keyword"] . "%");
        }

        $roles = $roleQuery->paginate($request->per_page);

        return array("success" => true, "message" => "", "data" => $roles);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required|unique:roles',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $role = new Role();
        $role->name = $input["name"];
        $role->description = $input["description"];
        $role->save();

        //For the Role activity log create
        $this->logActivity('role', $role);

        return array("success" => true, "message" => "Role Created Successfully!", 'data' => null);
    }


     //For the Role activity log create
     private function logActivity($activity, $role){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Role ' . $role->name;

        ActivityLogs::create([
            'source' => 'Role',
            'admin_id' => $admin_id,
            'name' => $role->name,
            'activity' => $activity
        ]);

    }


    public function roleApi(Request $request)
    {
        $query = Role::orderBy('name', 'asc');
        $roles = $query->get();

        return array("success" => true, "message" => "", "data" => $roles);

    }

    public function update(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
        ];

        $request->validate($validation_rules);
        $input = $request->all();

        $role = Role::find($input["id"]);
        $previous_name = $role->name;
        $role->name = $input["name"];
        $previous_description = $role->description;
        $role->description = $input["description"];
        $role->save();

        //For the update role activity log update
        $this->updateLogActivity('role', $role, $previous_name, $previous_description);

        return array("success" => true, "message" => "Role Updated Succesfully!", "data" => null);
    }


    //For the update role activity log update
    private function updateLogActivity($activity, $role, $previous_name, $previous_description){

        $admin_id = auth()->guard('admins')->id();
        $activity = '';
        $changes = [];

        if(strcmp($role->name, $previous_name) !== 0){
            $changes[] = 'Updated A Role Name From ' . $previous_name . ' to ' . $role->name;
        }
        if(strcmp($role->description, $previous_description) !== 0){
            $changes[] = 'Updated An Role Description From ' . $previous_description . ' to ' . $role->description;
        }
        if(count($changes) > 0){
            $activity = implode(' and ', $changes);
        }

        ActivityLogs::create([
            'source' => 'Role ',
            'admin_id' => $admin_id,
            'name' => $role->name,
            'activity' => $activity
        ]);
    
    }


    public function delete($id){

        $role = Role::find($id);

        $role->delete();

        $this->deleteLogActivity('role', $role);

        return response()->json(['success' => true, 'message' => "Role Has Been Deleted Successfully"], 200);

    }

      //For the Role activity log delete
      private function deleteLogActivity($activity, $role){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Deleted A Role ' . $role->name;

        ActivityLogs::create([
            'source' => 'Role',
            'admin_id' => $admin_id,
            'name' => $role->name,
            'activity' => $activity
        ]);

    }

   
    public function disabledRoles(){

        $disabledRoles = Role::onlyTrashed()->get();

        return response()->json([

            'success' => true,
            'message' => 'Disabled Roles Retrieved Successfully',
            'data' => $disabledRoles

        ]);

    }

    public function confirmRestore($id){

        $role = Role::withTrashed()->find($id);

        if($role){

            $role->restore();
            return response()->json(['success' => true, 'message' => 'Admin Account Has Been Successfully Restored'], 200);

        }else{

            return response()->json(['success' => false, 'message' => 'Admin Not Found'], 404);

        };

    }

}
