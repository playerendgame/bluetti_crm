<?php

namespace App\Http\Controllers\Admin\Retail\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailOrder;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.retails.orders.index');
    }

    public function create(Request $request)
    {
        return view('admin.retails.orders.create');
    }

    public function show(Request $request, $id)
    {
        $order = RetailOrder::with(['retail_order_products'])->findOrFail($id);
        $order->date_order = Carbon::parse($order->date_order)->format("M j, Y");
        $order->sales_name = $order->salesAdminName();
        $order->store_name = $order->store->name;
        $order->branch_name = $order->branch->name;

        return view('admin.retails.orders.show', compact('order'));
    }
}
