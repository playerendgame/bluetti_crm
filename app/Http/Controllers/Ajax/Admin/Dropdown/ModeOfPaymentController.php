<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModeOfPayment;
use App\Models\ActivityLogs;
use Carbon\Carbon;

class ModeOfPaymentController extends Controller
{
    public function list(Request $request)
    {
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = ModeOfPayment::orderBy($column, $request->order);

        $mode_of_payments = $query->paginate($request->per_page);

        foreach ($mode_of_payments as $mode_of_payment) {
            $mode_of_payment->created_at_s = Carbon::parse($mode_of_payment->created_at)->toDayDateTimeString();
        }

        return array("success" => true, "message" => "", "data" => $mode_of_payments);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $mode_of_payment = new ModeOfPayment();
        $mode_of_payment->name = $input["name"];
        $mode_of_payment->save();

        //logs the created mode of payment data to activity log
        $this->logActivity('mode_of_payment', $mode_of_payment );

        return array("success" => 'true', "message" => "Mode Of Payment Created Succesfully!", "data" => null);
    }


    //For the mode of payment activity log create
    private function logActivity($activity, $mode_of_payment){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Mode Of Payment ' . $mode_of_payment->name;

        ActivityLogs::create([
            'source' => 'Mode Of Payment',
            'admin_id' => $admin_id,
            'name' => $mode_of_payment->name,
            'activity' => $activity
        ]);

    }


    public function apiList(Request $request)
    {
        $query = ModeOfPayment::orderBy('name', 'asc');
        $mode_of_payments = $query->get();

        return array('success' => true, "message" => "", "data" => $mode_of_payments);
    }


    public function apiShow(Request $request, $id)
    {
        $mode_of_payment = ModeOfPayment::find($id);
        return response()->json(['success' => true, 'data' => $mode_of_payment]);
    }

    public function updateMOP(Request $request, $id){
        $mop = ModeOfPayment::find($id);

        if($mop){
            $previous_name = $mop->name;
            $mop->name = $request->input('name');
            $mop->save();

            //logs the update mode of payment data to activity log
            $this->updateLogActivity('mode_of_payment', $mop, $previous_name);

            return response()->json(['success', 'message' => 'Mode Of Payment updated successfully'], 201);
        }
        else{
            return response()->json(['error', 'message' => 'Mode Of Payment not found'], 404);
        }        
    }

     //For the mode of payment activity log update
     private function updateLogActivity($activity, $mode_of_payment, $previous_name){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Updated A Mode Of Payment From ' . $previous_name . ' to ' . $mode_of_payment->name;

        ActivityLogs::create([
            'source' => 'Mode Of Payment',
            'admin_id' => $admin_id,
            'name' => $mode_of_payment->name,
            'activity' => $activity
        ]);

    }
}
