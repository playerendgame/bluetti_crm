<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Order;

class OrdersPerProductCategoryController extends Controller
{
    public function orderPerProductCategory(Request $request){

        $validation_rules = [
            'start_date' => 'required',
            'end_date' => 'required',
        ];

        $request->validate($validation_rules);

        $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
        $end_date = Carbon::parse($request->input('end_date'))->endOfDay();

        $productCategories = ProductCategory::all();
        $productCategoryArray = [];

        foreach($productCategories as $productCategory){

            $products = $productCategory->product()
            ->whereHas('orderProducts', function ($query) use ($start_date, $end_date) {
                $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->get();

            $totalOrders = 0;
            $totalSales = 0;

            foreach($products as $product){
                $orderProducts = $product->orderProducts()
                ->whereBetween('created_at', [$start_date, $end_date])
                ->get();

                $totalOrders += $orderProducts->sum('quantity');
                // $totalSales += $orderProducts->sum('price');
                $totalSales += $orderProducts->sum(function ($orderProduct) {
                    return ($orderProduct->price * $orderProduct->quantity) - $orderProduct->discount;
                });
            }

            $productCategoryArray[] = [
                'name' => $productCategory->name,
                'total_orders' => $totalOrders,
                'total_sales' =>  '₱ ' . number_format($totalSales, 2),
            ];

        }

        $data['productCategories'] = $productCategoryArray;

        return array("success" => true, "message" => "", "data" => $data);
    }
}
