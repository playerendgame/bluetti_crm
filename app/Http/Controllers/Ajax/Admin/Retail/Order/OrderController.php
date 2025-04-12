<?php

namespace App\Http\Controllers\Ajax\Admin\Retail\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailOrder;
use App\Models\RetailOrderProduct;
use Auth;
use Carbon\Carbon;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = RetailOrder::orderBy($column, $request->order);

        // if (isset($input["keyword"])) {
        //     $query->where(function ($query) use ($input) {
        //         $query->where("order_number", "like", "%" . $input["keyword"] . "%");
        //     });
        // }

        $orders = $query->paginate($request->per_page);

        foreach ($orders as $order) {
            $order_products = RetailOrderProduct::where('order_id', '=', $order->id)->get();
            $order->date_order_s = Carbon::parse($order->date_order)->format("M j, Y");
            $order->sales_name = $order->sales_admin != null ? $order->salesAdminName() : "";
            $order->store_s = $order->store->name;
            $order->branch_s = $order->branch->name;
            $order->created_at_s = Carbon::parse($order->created_at)->format("M j, Y g:i A");
            $order->count_orders = $order_products->sum('quantity');
            $total_msrp = $order_products->sum(function ($order_product) {
                return $order_product->quantity * $order_product->price;
            });
            $order->total_msrp = "₱ " . number_format($total_msrp, 2);
            $sum_percentage = $order_products->sum('comms');
            $count_percentage = $order_products->count('comms');
            $comms = $sum_percentage > 0 ? $sum_percentage / $count_percentage : 0;
            $order->comms = number_format($comms, 2) . "%";
            $comms_amount = ($total_msrp * $comms) / 100;
            $order->comms_amount = "₱ " . number_format($comms_amount, 2);
            $gross_profit = $total_msrp - $comms_amount;
            $order->gross_profit = "₱ " . number_format($gross_profit, 2);
            $cogs_amount = $order_products->sum(function ($order_product) {
                return $order_product->cogs * $order_product->quantity;
            });
            $order->cogs_amount = "₱ " . number_format($cogs_amount, 2);
            $cogs_percentage = $cogs_amount > 0 ? $cogs_amount / $gross_profit : 0;
            $order->cogs_percentage = number_format(($cogs_percentage * 100), 2) . "%";
            $net_profit = $gross_profit - $cogs_amount;
            $order->net_profit = "₱ " . number_format($net_profit, 2);
        }

        return array("success" => true, "message" => "", "data" => $orders);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'date_order' => 'required',
            'retail_order_products.*.quantity' => 'required|numeric|min:1',
            'retail_order_products.*.price' => 'required|numeric|min:0',
            'retail_order_products.*.cogs' => 'required|numeric|min:0',
            'retail_order_products.*.comms' => 'required|numeric|min:0',
            'store_id' => 'required',
            'branch_id' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $order = new RetailOrder();
        $order->store_id = $input["store_id"];
        $order->branch_id = $input["branch_id"];
        $order->date_order = $input["date_order"];
        $order->sales_admin = $input["sales_admin"];
        if ($order->save()) {
            foreach ($input["retail_order_products"] as $product_order) {
                $order_product = new RetailOrderProduct();
                $order_product->order_id = $order->id;
                $order_product->product_id = $product_order["product_id"];
                $order_product->quantity = $product_order["quantity"];
                $order_product->price = $product_order["price"];
                $order_product->comms = $product_order["comms"] ?? 0;
                $order_product->cogs = $product_order["cogs"] ?? 0;
                $order_product->save();
            }
        }
        return array('success' => true, "message" => "Order Created Succesfully!", "data" => null);
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = RetailOrder::findOrFail($id);

            $orderProducts = RetailOrderProduct::where('order_id', $order->id)->get();

            foreach ($orderProducts as $orderProduct) {
                $purchaseOrder = PurchaseOrder::find($orderProduct->product_id);
    
                if ($purchaseOrder) {
                    $purchaseOrder->stocks_left += $orderProduct->quantity;
                    $purchaseOrder->save();
                }
            }
    
            $order->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order deleted successfully.',
                'data' => null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting order: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $order = null;

        if (isset($input["id"])) {
            $order = RetailOrder::find($input['id']);    
            if ($order == null) {
                return array("success" => false, "message" => "Can't find order", "data" => null);
            }
        } else {
            return array("success" => true, "message" => "id is required", "data" => null);
        }

        $order->date_order = $input["date_order"];
        $order->sales_admin = $input["sales_admin"];
        $order->save();

        return array("success" => true, "message" => "Order Updated Succesfully!", "data" => null);
    }
}
