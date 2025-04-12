<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\OrderProduct;
use Carbon\Carbon;

class OrdersPerCategoryController extends Controller
{
    public function data(Request $request)
    {
        $validation_rules = [
            'start_date' => "required",
            'end_date' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $start_date = Carbon::parse($input["start_date"])->startOfDay();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();

        // Fetch orders within date range
        $orders = Order::whereBetween('order_date', [$start_date, $end_date])->pluck('id');

        // Fetch most ordered products
        $mostOrderedProducts = OrderProduct::whereIn('order_id', $orders)
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->get();

        $maxOrderQuantity = $mostOrderedProducts->first() ? $mostOrderedProducts->first()->total_quantity : 0;

        // Filter the products to only include those with the highest order quantity
        $mostOrderedProducts = $mostOrderedProducts->filter(function ($product) use ($maxOrderQuantity) {
            return $product->total_quantity == $maxOrderQuantity;
        });

        $productData = $mostOrderedProducts->map(function ($product) {
            $productModel = Product::find($product->product_id);
            return [
                'name' => $productModel ? $productModel->name : 'Product Not Found',
                'total_orders' => $product->total_quantity
            ];
        });

        $facebook = Order::whereBetween('order_date', [$start_date, $end_date])
            ->whereHas('attribution', function ($query) {
                $query->where('category', 1);
            })
            ->count();

        $website = Order::whereBetween('order_date', [$start_date, $end_date])
            ->whereHas('attribution', function ($query) {
                $query->where('category', 2);
            })
            ->count();

        $lazada = Order::whereBetween('order_date', [$start_date, $end_date])
            ->whereHas('attribution', function ($query) {
                $query->where('category', 3);
            })
            ->count();

        $shopee = Order::whereBetween('order_date', [$start_date, $end_date])
            ->whereHas('attribution', function ($query) {
                $query->where('category', 4);
            })
            ->count();

        $organic = Order::whereBetween('order_date', [$start_date, $end_date])
            ->whereHas('attribution', function ($query) {
                $query->where('category', 5);
            })
            ->count();

        $referral = Order::whereBetween('order_date', [$start_date, $end_date])
            ->whereHas('attribution', function ($query) {
                $query->where('category', 6);
            })
            ->count();

        $total = $facebook + $website + $lazada + $shopee + $organic + $referral;

        // Facebook
        $data["facebook"] = $facebook;
        $data["conversion_facebook"] = $facebook > 0 ? number_format((($facebook / $total) * 100), 2) . "%" : "0%";

        // Website
        $data["website"] = $website;
        $data["conversion_website"] = $website > 0 ? number_format((($website / $total) * 100), 2) . "%" : "0%";

        // Lazada
        $data["lazada"] = $lazada;
        $data["conversion_lazada"] = $lazada > 0 ? number_format((($lazada / $total) * 100), 2) . "%" : "0%";

        // Shopee
        $data["shopee"] = $shopee;
        $data["conversion_shopee"] = $shopee > 0 ? number_format((($shopee / $total) * 100), 2) . "%" : "0%";

        // Organic
        $data["organic"] = $organic;
        $data["conversion_organic"] = $organic > 0 ? number_format((($organic / $total) * 100), 2) . "%" : "0%";

        // Referral
        $data["referral"] = $referral;
        $data["conversion_referral"] = $referral > 0 ? number_format((($referral / $total) * 100), 2) . "%" : "0%";

        // Add most ordered product data
        $data['most_ordered_products'] = $productData;

        $data["total"] = $total;

        return array('success' => true, "message" => "", "data" => $data);
    }
}
