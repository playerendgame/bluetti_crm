<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderProduct;

class OrderPerProductController extends Controller
{
    public function orderPerProduct(Request $request)
    {
        $validation_rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $start_date = Carbon::parse($input["start_date"])->startOfDay();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();

        $products = Product::all();
        $productArray = [];

        foreach ($products as $product)
        {
            $productData["name"] = $product->name;
            $order_products = OrderProduct::whereHas('order', function ($query) use ($start_date, $end_date) {
                $query->whereBetween('order_date', [$start_date, $end_date]);
            })->where('product_id', '=', $product->id)->get();
            $productData["count"] = $order_products->sum('quantity');
            $total_price = $order_products->sum(function ($order_product) {
                return ($order_product->quantity * $order_product->price) - $order_product->discount;
            });
            $productData["sales"] = "₱ " . number_format($total_price, 2);

            array_push($productArray, $productData);
        }

        $data["products"] = $productArray;

        return array("success" => true, "message" => "", "data" => $data);
    }
}
