<?php

namespace App\Http\Controllers\Ajax\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use App\Models\OrderProduct;

class InventoryController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();

        $start_date = Carbon::parse($input["start_date"])->startOfDay();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();

        $products = Product::get();
        $productsArray = array();

        foreach ($products as $product)
        {
            $order_product = OrderProduct::whereHas('order', function ($query) use ($start_date, $end_date) {
                $query->whereBetween('dispatch_date', [$start_date, $end_date]);
            })
            ->where('product_id', $product->id);

            $count_order_product = OrderProduct::whereHas('order', function ($query) use ($start_date, $end_date) {
                $query->whereBetween('order_date', [$start_date, $end_date]);
            })->where('product_id', $product->id);

            $purchaseOrder = PurchaseOrder::where('product_id', '=', $product->id);
            $getCurrentCogs = (clone $purchaseOrder)->where('stocks_left', ">", 0)->pluck('distributor_price')->first();
            $productData["name"] = $product->name;
            $productData["stocks_left"] = (clone $purchaseOrder)->sum('stocks_left');
            $productData["current_cogs"] = $getCurrentCogs != null ? "₱ " . number_format($getCurrentCogs, 2) : "₱ " . 0.00;
            $productData["count_dispatch"] = (clone $order_product)->sum('quantity');
            $productData["total_purchase"] = (clone $count_order_product)->sum('quantity');

            array_push($productsArray, $productData);
        }

        $data["products"] = $productsArray;

        return array("success" => true, "message" => "", "data" => $data);
    }
}
