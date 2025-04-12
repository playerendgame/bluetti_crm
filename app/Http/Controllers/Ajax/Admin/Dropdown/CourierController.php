<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Courier;
use App\Models\ActivityLogs;
use Carbon\Carbon;

class CourierController extends Controller
{
    public function list(Request $request)
    {
        $column = $request->column;
        
        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = Courier::orderBy($column, $request->order);

        if (isset($input["keyword"])) {
            $query->where('name', 'like', '%' . $input["keyword"] . "%");
        }

        $couriers = $query->paginate($request->per_page);

        foreach ($couriers as $courier) {
            $courier->created_at_s = Carbon::parse($courier->created_at)->toDayDateTimeString();
            $courier->is_active_s = $courier->getActiveName();
        }

        return array("success" => true, "message" => "", "data" => $couriers);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required|unique:couriers',
            'is_active' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $courier = new Courier();
        $courier->name = $input["name"];
        $courier->is_active = $input["is_active"];
        $courier->save();

        $this->logActivity('courier', $courier);

        return array("success" => true, "message" => "Courier Created Successfully!", "data" => null);
    }

     //For the courier activity log create
     private function logActivity($activity, $courier){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Courier ' . $courier->name;

        ActivityLogs::create([
            'source' => 'Courier',
            'admin_id' => $admin_id,
            'activity' => $activity
        ]);

    }


    public function update(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $courier = Courier::find($input["id"]);
        $previous_name = $courier->name;
        $courier->name = $input["name"];
        $courier->is_active = $input["is_active"];
        $courier->save();

        $this->updatelogActivity('courier', $courier, $previous_name);

        return array("success" => true, "message" => "Courier Updated Succesfully!", 'data' => null);
    }


     //For the courier activity log update
     private function updatelogActivity($activity, $courier, $previous_name){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Updated A Courier From  ' . $previous_name . ' to ' . $courier->name;

        ActivityLogs::create([
            'source' => 'Courier',
            'admin_id' => $admin_id,
            'activity' => $activity
        ]);

    }

    public function delete($id){

        $courier = Courier::find($id);

        $courier->delete();

        $this->deleteLogActivity('courier', $courier);

        return response()->json(["success" => true, "message" => "Courier deleted successfully"], 200);   
    }


    //For the courier activity log delete
    private function deleteLogActivity($activity, $courier){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Deleted A Courier ' . $courier->name;

        ActivityLogs::create([
            'source' => 'Courier',
            'admin_id' => $admin_id,
            'activity' => $activity
        ]);

    }



    public function api (Request $request)
    {
        $query = Courier::orderBy('name', 'asc');
        $couriers = $query->get();

        return array("success" => true, "message" => "", "data" => $couriers);
    }
}
