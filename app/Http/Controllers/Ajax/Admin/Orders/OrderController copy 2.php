<?php

namespace App\Http\Controllers\Ajax\Admin\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\OrderProduct;
use Carbon\Carbon;
use App\Models\PurchaseOrder;
use App\Models\Purchase;

class OrderController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;
        if (strcmp($column, "order_date_s") == 0) {
            $column = "order_date";
        } else if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = Order::orderBy($column, $request->order);

        $orders = $query->paginate($request->per_page);

        foreach ($orders as $order) {
            $order_products = OrderProduct::where('order_id', '=', $order->id)->get();
            $order->order_date_s = Carbon::parse($order->order_date)->format("M j, Y");
            $order->customer_name = $order->customers->name;
            $order->admin_name = $order->admin_id != null ? $order->admin->fullName() : "";
            $order->created_at_s = Carbon::parse($order->created_at)->toDayDateTimeString();
            $order->attribution_name = $order->attribution_id != null ? $order->attribution->name : "";
            $order->count_orders = $order_products->sum(function ($order_product) {
                $total_orders = $order_product->price != 0 ? $order_product->quantity : 0;
                return $total_orders;
            });
            $total_price = $order_products->sum(function ($order_product) {
                return (($order_product->quantity * $order_product->price) - $order_product->discount);
            });
            $order->total_price = "₱ " . number_format($total_price, 2);
            $order->delivery_status_s = $order->deliveryStatusName();
            $order->target_delivery_date_s = $order->target_delivery_date != null ? Carbon::parse($order->target_delivery_date)->format("M j, Y") : "";
            $order->date_delivered_s = $order->date_delivered != null ? Carbon::parse($order->date_delivered)->format("M j, Y") : "";
            $order->dispatch_date_s = $order->dispatch_date != null ? Carbon::parse($order->dispatch_date)->format("M j, Y") : "";
            $total_price_cogs = $order_products->sum(function ($order_product) {
                return $order_product->cogs;
            });
            $order->cogs_s = "₱ " . number_format($total_price_cogs, 2);
            $order->percent_cogs = $total_price > 0 ? number_format((($total_price_cogs / $total_price) * 100), 2) . "%" : "0%";
        }

        return array("success" => true, "message" => "", "data" => $orders);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'order_date' => 'required',
            'attribution_id' => 'required',
            'admin_id' => 'required',
            'product_orders' => 'required|array',
            'product_orders.*.product_id' => 'required',
            'product_orders.*.quantity' => 'required|numeric|min:1',
            'product_orders.*.price' => 'required|numeric|min:0',
            'product_orders.*.discount' => 'required|numeric|min:0',
            'delivery_status' => 'required',
            'target_delivery_date' => 'required',
            'dispatch_date' => 'required_if:delivery_status,=,1',
            'date_delivered' => 'required_if:delivery_status,2,4',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        // Fetch the related PurchaseOrders in ascending order by ID
        $purchaseOrders = PurchaseOrder::whereIn('product_id', array_column($input["product_orders"], 'product_id'))
        ->orderBy('id', 'asc')
        ->get();

        $remainingQuantity = array_sum(array_column($input["product_orders"], 'quantity'));
        $totalCogs = 0; // Initialize the total COGS

        $totalStocksLeft = (clone $purchaseOrders)->sum('stocks_left');

        if ($totalStocksLeft >= $remainingQuantity) {
            if (isset($input["is_new_customer"]) && $input["is_new_customer"] == 0) {

            // Save New Customer
            $customer = new Customer();
            $customer->name = $input["name"];
            $customer->number = $input["number"];
            $customer->email = $input["email"];

            if ($customer->save()) {

                $order = new Order();
                $order->customer_id = $customer->id;
                $order->contact_number = $input["number"];
                $order->email = $input["email"];
                $order->address = $input["address"];
                $order->order_date = $input["order_date"];
                $order->admin_id = $input["admin_id"];
                $order->attribution_id = $input["attribution_id"];
                $order->delivery_status = $input["delivery_status"];
                $order->target_delivery_date = $input["target_delivery_date"];
                $order->dispatch_date = $input["dispatch_date"];
                $order->date_delivered = $input["date_delivered"];

                if ($order->save()) {
                    foreach ($input["product_orders"] as $product_order) {
                        $order_product = new OrderProduct();
                        $order_product->order_id = $order->id;
                        $order_product->product_id = $product_order["product_id"];
                        $order_product->quantity = $product_order["quantity"];
                        $order_product->price = $product_order["price"];
                        $order_product->discount = $product_order["discount"];
                        if ($order_product->save()) {
                            foreach ($purchaseOrders as $index => $purchaseOrder) {
                                $deductedStocks = min($purchaseOrder->stocks_left, $product_order["quantity"]);

                                // Update the 'stocks_left' column per index
                                $purchaseOrder->update(['stocks_left' => $purchaseOrder->stocks_left - $deductedStocks]);

                                $totalCogs += $deductedStocks * $purchaseOrder->distributor_price;

                                $product_order["quantity"] -= $deductedStocks;

                                if ($product_order["quantity"] <= 0) {
                                    // Break the loop when the remaining quantity is fully covered
                                    break;
                                }
                            }

                            // Ensure that 'stocks_left' is not updated to negative values
                            $purchaseOrders->each(function ($purchaseOrder) {
                                $purchaseOrder->update(['stocks_left' => max(0, $purchaseOrder->stocks_left)]);
                            });

                            // Update the 'cogs' property in the associated OrderProduct
                            $order_product->update(['cogs' => $totalCogs]);
                        }
                    }
                }
            }
        } else if (isset($input["is_new_customer"]) && $input["is_new_customer"] == 1) {
            $order = new Order();
            $order->customer_id = $input["customer_id"];
            $order->contact_number = $input["number"];
            $order->email = $input["email"];
            $order->address = $input["address"];
            $order->order_date = $input["order_date"];
            $order->admin_id = $input["admin_id"];
            $order->attribution_id = $input["attribution_id"];
            $order->delivery_status = $input["delivery_status"];
            $order->target_delivery_date = $input["target_delivery_date"];
            $order->dispatch_date = $input["dispatch_date"];
            $order->date_delivered = $input["date_delivered"];

            if ($order->save()) {
                foreach ($input["product_orders"] as $product_order) {
                    $order_product = new OrderProduct();
                    $order_product->order_id = $order->id;
                    $order_product->product_id = $product_order["product_id"];
                    $order_product->quantity = $product_order["quantity"];
                    for ($i < 0; $i < $product_order["quantity"]; $i++) {
                        $order_product_quantity = 1;
                    }
                    $order_product->price = $product_order["price"];
                    $order_product->discount = $product_order["discount"];
                    if ($order_product->save()) {
                        foreach ($purchaseOrders as $index => $purchaseOrder) {
                            $deductedStocks = min($purchaseOrder->stocks_left, $product_order["quantity"]);

                            // Update the 'stocks_left' column per index
                            $purchaseOrder->update(['stocks_left' => $purchaseOrder->stocks_left - $deductedStocks]);

                            $totalCogs += $deductedStocks * $purchaseOrder->distributor_price;

                            $product_order["quantity"] -= $deductedStocks;

                            if ($product_order["quantity"] <= 0) {
                                // Break the loop when the remaining quantity is fully covered
                                break;
                            }
                        }

                        // Ensure that 'stocks_left' is not updated to negative values
                        $purchaseOrders->each(function ($purchaseOrder) {
                            $purchaseOrder->update(['stocks_left' => max(0, $purchaseOrder->stocks_left)]);
                        });

                        // Update the 'cogs' property in the associated OrderProduct
                        $order_product->update(['cogs' => $totalCogs]);
                    }
                }
            }
        }
        } else {
            return array('success' => false, "message" => "Stocks are low", "data" => null);
        }

        return array("success" => true, "message" => "Order Created Succesfully!", "data" => null);
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $order = null;

        if (isset($input["id"])) {
            $order = Order::find($input['id']);    
            if ($order == null) {
                return array("success" => false, "message" => "Can't find order", "data" => null);
            }
        } else {
            return array("success" => true, "message" => "id is required", "data" => null);
        }

        $order->attribution_id = $input["attribution_id"];
        $order->admin_id = $input["admin_id"];
        $order->delivery_status = $input["delivery_status"];
        $order->target_delivery_date = $input["target_delivery_date"];
        $order->dispatch_date = $input["dispatch_date"];
        $order->date_delivered = $input["date_delivered"];
        $order->save();

        return array("success" => true, "message" => "Order Updated Successfully!", "data" => null);
    }
}
