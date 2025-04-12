<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class OrdersOverviewController extends Controller
{
    public function orderStatusOverview(Request $request)
    {
        $validation_rules = [
            'start_date' => "required",
            'end_date' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $start_date = Carbon::parse($input["start_date"])->startOfday();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();

        $order = Order::whereBetween('order_date', [$start_date, $end_date]);

        $pending = (clone $order)->where('delivery_status', 0)->count();
        $shipped = (clone $order)->where('delivery_status', 1)->count();
        $delivered = (clone $order)->where('delivery_status', 2)->count();
        $rts = (clone $order)->where('delivery_status', 3)->count();
        $returned = (clone $order)->where('delivery_status', 4)->count();
        $out_of_delivery = (clone $order)->where('delivery_status', 5)->count();

        $total = $pending + $shipped + $delivered + $rts + $returned + $out_of_delivery;

        // Pending
        $data["pending"] = $pending;
        $data["conversion_pending"] = $pending > 0 ? number_format((($pending / $total) * 100), 2) . "%" : "0%";

        // Shipped
        $data["shipped"] = $shipped;
        $data["conversion_shipped"] = $shipped > 0 ? number_format((($shipped / $total) * 100), 2) . "%" : "0%";

        // Delivered
        $data["delivered"] = $delivered;
        $data["conversion_delivered"] = $delivered > 0 ? number_format((($delivered / $total) * 100), 2) . "%" : "0%";

        // RTS
        $data["rts"] = $rts;
        $data["conversion_rts"] = $rts > 0 ? number_format((($rts / $total) * 100), 2) . "%" : "0%";

        // Returned
        $data["returned"] = $returned;
        $data["conversion_returned"] = $returned > 0 ? number_format((($returned / $total) * 100), 2) . "%" : "0%";

        // Out of Delivery
        $data["out_of_delivery"] = $out_of_delivery;
        $data["conversion_out_of_delivery"] = $out_of_delivery > 0 ? number_format((($out_of_delivery / $total) * 100), 2) . "%" : "0%";

        // Total
        $data["total"] = $total;

        return array("success" => true, "message" => "", "data" => $data);
    }
}
