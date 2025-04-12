<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Target;
use App\Models\ActivityLogs;
use Carbon\Carbon;

class TargetController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        } else if (strcmp($column, "date_s") == 0) {
            $column = "date";
        }

        $query = Target::orderBy($column, $request->order);

        $targets = $query->paginate($request->per_page);

        foreach ($targets as $target) {
            $target->date_s = Carbon::parse($target->date)->format("M, Y");
            $target->created_at_s = Carbon::parse($target->created_at)->toDayDateTimeString();
            $target->sales_target_s = "₱ " . number_format($target->sales_target, 2);
        }

        return array("success" => true, "message" => "", "data" => $targets);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'date' => 'required|unique:targets',
            'sales_target' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $target = new Target();
        $target->date = $input["date"] . "-01";
        $target->sales_target = $input["sales_target"];
        $target->save();

        //For the target activity log create
        $this->logActivity('target', $target);

        return array("success" => true, "message" => "Target Created Successfully!", "data" => null);
    }


     //For the target activity log create
     private function logActivity($activity, $target){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Target Date ' . $target->date . ' w/ Target Sales ' . $target->sales_target;

        ActivityLogs::create([
            'source' => 'Target',
            'admin_id' => $admin_id,
            'name' => $target->date,
            'activity' => $activity
        ]);

    }

}
