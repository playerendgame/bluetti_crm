<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\DistributionChannel;
use Carbon\Carbon;

class OrderPerDistributionChannelController extends Controller
{
    public function orderPerDistributionChannel(Request $request)
    {
        $validation_rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        $request->validate($validation_rules);
        $input = $request->all();

        $start_date = Carbon::parse($input["start_date"])->startOfDay();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();

        $channels = DistributionChannel::all();
        $channelsArray = array();
        foreach ($channels as $channel) {
            $total_price = 0;
            $orders = Order::whereHas('attribution', function ($query) use ($channel) {
                $query->where('distribution_channel_id', '=', $channel->id);
            })->whereBetween('order_date', [$start_date, $end_date])->get();

            foreach ($orders as $order) {
                $sales = $order->order_products->sum(function($product) {
                    return ($product->pivot->quantity * $product->pivot->price) - $product->pivot->discount;
                });

                $total_price += $sales;
            }

            $tableData["name"] = $channel->name;
            $tableData["count"] = (clone $orders)->count();
            $tableData["sales"] = "₱ " . number_format($total_price, 2);

            array_push($channelsArray, $tableData);
        }

        $data["channels"] = $channelsArray;

        return array("success" => true, "message" => "", "data" => $data);
    }
}
