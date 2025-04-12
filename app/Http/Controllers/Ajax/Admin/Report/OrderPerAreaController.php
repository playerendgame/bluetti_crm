<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\Region;

class OrderPerAreaController extends Controller
{
    public function orderPerArea(Request $request)
    {
        $validation_rules = [
            'start_date' => "required",
            'end_date' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $start_date = Carbon::parse($input["start_date"])->startOfday();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();

        $tableArray = array();
        $regions = Region::all();
        foreach ($regions as $region) {
            $total_price = 0;
            $orders = Order::where('region_id', '=', $region->id)->whereBetween('order_date', [$start_date, $end_date])->get();
            foreach ($orders as $order) {
                $sales = $order->order_products->sum(function($product) {
                    return ($product->pivot->quantity * $product->pivot->price) - $product->pivot->discount;
                });

                $total_price += $sales;
            }

            // $sales = $orders->flatMap(function ($order) {
            //     return $order->order_products;
            // })->sum(function ($order_product) {
            //     return ($order_product->quantity * $order_product->price) - $order_product->discount;
            // });
            
            $tableData["name"] = $region->name;
            $tableData["counts"] = (clone $orders)->count();
            $tableData["sales"] = "₱ " . number_format($total_price, 2);

            array_push($tableArray, $tableData);
        }

        $data["regions"] = $tableArray;

        return array('success' => true, "message" => "", "data" => $data);
    }
}
