<?php

namespace App\Http\Controllers\Admin\Stats;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class StatController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('admins')->user()->hasPermission('mystats.show')) {
            $input = $request->all();
            $data = array(
                "filters" => json_encode(array(
                        "year" => isset($input["year"]) ? (int)$input["year"] : 0,
                        "quarter" => isset($input["quarter"]) ? (int)$input["quarter"] : 0,
                        "date_paid_from" => isset($input["date_paid_from"]) ? $input["date_paid_from"] : 0,
                        "date_paid_to" => isset($input["date_paid_to"]) ? $input["date_paid_to"] : 0,
                        "date_order_from" => isset($input["date_order_from"]) ? $input["date_order_from"] : 0,
                        "date_order_to" => isset($input["date_order_to"]) ? $input["date_order_to"] : 0,
                        "delivery_status" => isset($input["delivery_status"]) ? (int)$input["delivery_status"] : 99,
                        "date_delivered_from" => isset($input["date_delivered_from"]) ? (int)$input["date_delivered_from"] : 0,
                        "date_delivered_to" => isset($input["date_delivered_to"]) ? (int)$input["date_delivered_to"] : 0,
                    ))
            );
    
            return view('admin.stats.index')->with($data);
        } else {
            abort(404);
        }
    }
}
