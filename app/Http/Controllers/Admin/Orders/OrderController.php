<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderHistory;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderProduct;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\Attribution;
use App\Models\Admin;
use App\Models\Product;
use App\Models\ModeOfPayment;
use App\Models\City;
use App\Models\Region;
use App\Models\Province;


class OrderController extends Controller
{
    public function myOrder(Request $request)
    {
        return view('admin.orders.my-orders');
    }

    public function all(Request $request)
    {
        $input = $request->all();
        $data = array(
            'filters' => json_encode(array(
                "order_from" => isset($input["order_from"]) ? $input["order_from"] : 0,
                "order_to" => isset($input["order_to"]) ? $input["order_to"] : 0,

                "region" => isset($input["region"]) ? (int)$input["region"] : 0,
                "province" => isset($input["province"]) ? (int)$input["province"] : 0,
                "city" => isset($input["city"]) ? (int)$input["city"] : 0,
                "referral" => isset($input["referral"]) ? (int)$input["referral"] : 0,

                "delivery_status" => isset($input["delivery_status"]) ? (int)$input["delivery_status"] : 99,
                "admin" => isset($input["admin"]) ? (int)$input["admin"] : 0,
                "attribution" => isset($input["attribution"]) ? (int)$input["attribution"] : 0,
                'mop_id' => isset($input['mop_id']) ? (int)$input['mop_id'] : 0,
                "courier" => isset($input["courier"]) ? (int)$input["courier"] : 0,
                "dispatch_from" => isset($input["dispatch_from"]) ? $input["dispatch_from"] : 0,
                "dispatch_to" => isset($input["dispatch_to"]) ? $input["dispatch_to"] : 0,
                "delivered_from" => isset($input["delivered_from"]) ? $input["delivered_from"] : 0,
                "delivered_to" => isset($input["delivered_to"]) ? $input["delivered_to"] : 0,
                "returned_from" => isset($input["returned_from"]) ? $input["returned_from"] : 0,
                "returned_to" => isset($input["returned_to"]) ? $input["returned_to"] : 0,
                "payment_status" => isset($input["payment_status"]) ? (int)$input["payment_status"] : 99,
                "date_paid_from" => isset($input["date_paid_from"]) ? $input["date_paid_from"] : 0,
                "date_paid_to" => isset($input["date_paid_to"]) ? $input["date_paid_to"] : 0,
                "target_delivery_from" => isset($input["target_delivery_from"]) ? $input["target_delivery_from"] : 0,
                "target_delivery_to" => isset($input["target_delivery_to"]) ? $input["target_delivery_to"] : 0,
                
            ))
        );
        $hasPermission = [
            'orders_create' => Auth::guard('admins')->user()->hasPermission('orders.create'),
            'orders_update' => Auth::guard('admins')->user()->hasPermission('orders.update'),
            'orders_delete' => Auth::guard('admins')->user()->hasPermission('orders.delete'),
        ];
        return view('admin.orders.all', compact('hasPermission'))->with($data);
    }

    public function create(Request $request)
    {
        return view('admin.orders.create');
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['customers', 'mode_of_payment', 'attribution', 'admin', 'order_products', 'regions', 'provinces', 'cities'])->findOrFail($id);

        $order->attribution_name = $order->attribution_name = $order->attribution_id != null ? $order->attribution->name : "";
        $order->mode_of_payment_name = $order->mode_of_payment_name = $order->mode_of_payment_id != null ? $order->mode_of_payment->name : '';        
        $order->admin_name = $order->admin_id != null ? $order->admin->fullName() : "";
        $order->delivery_status_s = $order->deliveryStatusName();
        $order->referral_name = $order->referral_id != null ? $order->referral->name : "";
        $order->date_paid_s = $order->date_paid != null ? $order->date_paid : "";

        $customers = Customer::all(); 
        $attribution = Attribution::all();
        $mode_of_payment = ModeOfPayment::all();
        $admin = Admin::all();
        $product = Product::all();
        $region = Region::all();
        $province = Province::all();
        $city = City::all();

        //For the permission of update and delete
        $hasPermission = [
            'view_orders_update' => Auth::guard('admins')->user()->hasPermission('view.orders.update'),
            'view_orders_delete' => Auth::guard('admins')->user()->hasPermission('view.orders.delete'),
        ];
        return view('admin.orders.show', compact('order', 'customers', 'attribution', 'admin', 'region', 'province', 'city', 'hasPermission'));
    }


    public function updateRegion($regionId)
    {
        $province = Province::where('region_id', $regionId)->get();

        return response()->json($province);
    }

    public function updateProvince($provinceId)
    {
        $city = City::where('province_id', $provinceId)->get();

        return response()->json($city);
    }

}
