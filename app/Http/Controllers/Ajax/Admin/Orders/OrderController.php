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
use App\Models\OrderHistory;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attribution;
use App\Models\Product;
use App\Models\OrderPaymentMethod;
use App\Models\ModeOfPayment;
use App\Models\Courier;
use App\Models\ActivityLogs;
use Illuminate\Support\Facades\Log;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Jobs\OrderExportJob;
use App\Helpers\Constants;

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

        if (isset($input["order_from"]) && $input["order_to"] != 0) {
            $query->whereBetween('order_date', [Carbon::parse($input["order_from"])->startOfDay(), Carbon::parse($input["order_to"])->endOfDay()]);
        }

        if (isset($input["admin"]) && $input["admin"] != 0) {
            $query->where('admin_id', "=", $input["admin"]);
        }

        if (isset($input["region"]) && $input["region"] != 0) {
            $query->where('region_id', '=', $input["region"]);
        }

        if (isset($input["province"]) && $input["province"] != 0) {
            $query->where('province_id', '=', $input["province"]);
        }

        if (isset($input["city"]) && $input["city"] != 0) {
            $query->where('city_id', '=', $input["city"]);
        }

        if (isset($input["referral"]) && $input["referral"] != 0) {
            $query->where('referral_id', '=', $input["referral"]);
        }

        if (isset($input["mop_id"]) && $input["mop_id"] != 0) {
            $query->where('mode_of_payment_id', $input['mop_id']);
        }
        
        if (isset($input["attribution"]) && $input["attribution"] != 0) {
            $query->where('attribution_id', '=', $input["attribution"]);
        }

        if (isset($input["courier"]) && $input["courier"] != 0) {
            $query->where('courier_id', "=", $input["courier"]);
        }

        if (isset($input["delivery_status"]) && $input["delivery_status"] != 99) {
            $query->where('delivery_status', '=', $input["delivery_status"]);
        }

        if (isset($input["dispatch_from"]) && $input["dispatch_to"] != 0) {
            $query->whereBetween('dispatch_date', [Carbon::parse($input["dispatch_from"])->startOfDay(), Carbon::parse($input["dispatch_to"])->endOfDay()]);
        }

        if (isset($input["delivered_from"]) && $input["delivered_to"] != 0) {
            $query->whereBetween('date_delivered', [Carbon::parse($input["delivered_from"])->startOfDay(), Carbon::parse($input["delivered_to"])->endOfDay()]);
        }

        if (isset($input["returned_from"]) && $input["returned_to"] != 0) {
            $query->whereBetween('returned_date', [Carbon::parse($input["returned_from"])->startOfDay(), Carbon::parse($input["returned_to"])->endOfDay()]);
        }

        if (isset($input["payment_status"]) && $input["payment_status"] != 99) {
            $query->where("mark_as_paid", "=", $input["payment_status"]);
        }

        if (isset($input["date_paid_from"]) && $input["date_paid_to"] != 0) {
            $query->whereBetween('date_paid', [Carbon::parse($input["date_paid_from"])->startOfDay(), Carbon::parse($input["date_paid_to"])->endOfDay()]);
        }

        if (isset($input["target_delivery_from"]) && $input["target_delivery_to"] != 0) {
            $query->whereBetween('target_delivery_date', [Carbon::parse($input["target_delivery_from"])->startOfDay(), Carbon::parse($input["target_delivery_to"])->endOfDay()]);
        }

        if (isset($input["keyword"])) {
            $query->where(function ($query) use ($input) {
                $query->where("order_number", "like", "%" . $input["keyword"] . "%");
                $query->orWhereHas('customers', function ($query) use ($input) {
                    $query->where('name', "like", "%" . $input["keyword"] . "%");
                });
                $query->orWhere('address', "like", "%" . $input["keyword"] . "%");
                $query->orWhere('tracking_number', "like", "%" . $input["keyword"] . "%");
            });
        }

        $orders = $query->paginate($request->per_page);

        foreach ($orders as $order) {
            $order_products = OrderProduct::where('order_id', '=', $order->id)->get();
            $order->order_date_s = Carbon::parse($order->order_date)->format("M j, Y");
            $order->region_name = $order->region_id != null ? $order->regions->name : "";
            $order->province_name = $order->province_id != null ? $order->provinces->name : "";
            $order->city_name = $order->city_id != null ? $order->cities->name : "";
            $order->customer_name = $order->customers->name;
            $order->mode_of_payment_name = $order->mode_of_payment ? $order->mode_of_payment->name : "";
            $order->admin_name = $order->admin_id != null ? $order->admin->fullName() : "";
            $order->created_at_s = Carbon::parse($order->created_at)->toDayDateTimeString();
            $order->attribution_name = $order->attribution_id != null ? $order->attribution->name : "";
            $order->count_orders = $order_products->sum('quantity');
            $total_price = $order_products->sum(function ($order_product) {
                return ($order_product->quantity * $order_product->price) - $order_product->discount;
            });
            $order->total_price = "₱ " . number_format($total_price, 2);
            $order->delivery_status_s = $order->deliveryStatusName();
            $order->target_delivery_date_s = $order->target_delivery_date != null ? Carbon::parse($order->target_delivery_date)->format("M j, Y") : "";
            $order->date_delivered_s = $order->date_delivered != null ? Carbon::parse($order->date_delivered)->format("M j, Y") : "";
            $order->dispatch_date_s = $order->dispatch_date != null ? Carbon::parse($order->dispatch_date)->format("M j, Y") : "";
            $total_price_cogs = $order_products->sum('cogs'); //Calculate total COGS
            $order->cogs_s = "₱ " . number_format($total_price_cogs, 2);
            $order->percent_cogs = $total_price > 0 ? number_format(($total_price_cogs / $total_price) * 100, 2) . "%" : "0%";
            $order->mark_as_paid_s = $order->markAsPaid();
            $order->courier_name = $order->courier_id != null ? $order->courier->name : "";
            $order->date_paid_s = $order->date_paid != null ? Carbon::parse($order->date_paid)->format("M j, Y") : "";
            $order->returned_date_s = $order->returned_date != null ? Carbon::parse($order->returned_date)->format("M j, Y") : "";
        }

        return array("success" => true, "message" => "", "data" => $orders);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'order_date' => 'required',
            'attribution_id' => 'required',
            'product_orders' => 'required|array',
            'product_orders.*.quantity' => 'required|numeric|min:1',
            'product_orders.*.price' => 'required|numeric|min:0',
            'product_orders.*.discount' => 'required|numeric|min:0',
            'product_orders.*.cogs' => 'required|numeric|min:0',
            'delivery_status' => 'required',
            'target_delivery_date' => 'required',
            'date_delivered' => 'required_if:delivery_status,2',
            'order_number' => 'unique:orders',
            'returned_date' => 'required_if:delivery_status,3,4',
            'tracking_number' => 'nullable|unique:orders',
            'province_id' => 'required|integer',
            'region_id' => 'required|integer',
            'city_id' => 'required|integer',
            'date_paid' =>'required_if:mark_as_paid,1',
        ];

        $input = $request->all();

        $purchaseOrders = PurchaseOrder::whereIn('product_id', array_column($input["product_orders"], 'product_id'))
            ->orderBy('id', 'asc')
            ->get();

        $remainingQuantity = array_sum(array_column($input["product_orders"], 'quantity'));
        $totalCogs = 0; // Initialize the total COGS

        $totalStocksLeft = (clone $purchaseOrders)->sum('stocks_left');

        $prefix = "BLU";
        $latestOrder = Order::whereNotNull('ref_number')->orderBy('ref_number', 'desc')->first();
        $ref_num = $input["order_number"] != null ? $input["order_number"] : 80000;
        
        if ($latestOrder != null) {
            $ref_num = $input["order_number"] != null ? null : $latestOrder->ref_number + 1;
        }

        $order_number = $input["order_number"] !== null ? $input["order_number"] : $prefix . $ref_num;
    
        $request->validate($validation_rules);
    
        $attribution = Attribution::findOrFail($input["attribution_id"]);
        $deliveryType = $input['delivery_status'];
        $tracking_prefix = 'GPPHBLTTI';
        $sequenceNum = 1108; //Start from 1108
        $trackingNumber = null;

        if (in_array(strtolower($attribution->name), ['own delivery', 'owndelivery', 'walk in', 'walk-in'])) {
            $latestOrder = Order::where('delivery_status', $deliveryType)->orderBy('id', 'desc')->first();
            if ($latestOrder) {
                $lastTrackingNumber = $latestOrder->tracking_number;
                $lastSequence = substr($lastTrackingNumber, strlen($tracking_prefix));
                $sequenceNum = intval($lastSequence) + 1;
            }
            // Ensure tracking number is unique
            do {
                $trackingNumber = $tracking_prefix . str_pad($sequenceNum, 9, '0', STR_PAD_LEFT);
                $sequenceNum++;
            } while (Order::where('tracking_number', $trackingNumber)->exists());
        }

        if ($totalStocksLeft >= $remainingQuantity) {
            $order = new Order();
            $order->customer_id = $input["customer_id"];
            $order->contact_number = $input["number"];
            $order->email = $input["email"];
            $order->region_id = $input["region_id"];
            $order->province_id = $input['province_id'];
            $order->city_id = $input['city_id'];
            $order->address = $input["address"];
            $order->order_date = $input["order_date"];
            $order->admin_id = $input["admin_id"];
            $order->attribution_id = $input["attribution_id"];
            $order->delivery_status = $input["delivery_status"];
            $order->target_delivery_date = $input["target_delivery_date"];
            $order->dispatch_date = $input["dispatch_date"] ?? null;
            $order->date_delivered = $input["date_delivered"] ?? null;
            $order->returned_date = $input["returned_date"] ?? null;
            $order->order_number = $order_number;
            $order->ref_number = $ref_num;
            $order->notes = $input["notes"] ?? null;
            $order->courier_id = $input["courier_id"] ?? null;
            $order->tracking_number = $trackingNumber;
            $order->mark_as_paid = $input["mark_as_paid"] ?? 0;
            if ($input["mark_as_paid"] == 1) {
                $order->date_paid = $input["date_paid"];
            }
            $order->mode_of_payment_id = $input["mode_of_payment_id"];
            $order->payment_amount = $input["payment_amount"];
            $order->payment_notes = $input["payment_notes"] ?? null;
            $order->referral_id = $input["referral_id"];
            if ($order->save()) {
                foreach ($input["product_orders"] as $product_order) {
                    $remaining_quantity = $product_order["quantity"];

                    foreach ($purchaseOrders as $purchaseOrder) {
                        if ($remaining_quantity <= 0) {
                            break;
                        }

                        if ($purchaseOrder->stocks_left > 0) {
                            $deductedStocks = min($purchaseOrder->stocks_left, $remaining_quantity);

                            $order_product = new OrderProduct();
                            $order_product->order_id = $order->id;
                            $order_product->product_id = $product_order["product_id"];
                            $order_product->quantity = $deductedStocks;
                            $order_product->price = $product_order["price"];
                            $order_product->discount = $product_order["discount"] / $product_order["quantity"];
                            $order_product->cogs = $purchaseOrder->distributor_price;
                            $order_product->po_id = $purchaseOrder->id;
                            if ($order_product->save()) {
                                $purchaseOrder->update(['stocks_left' => $purchaseOrder->stocks_left - $deductedStocks]);
                                $remaining_quantity -= $deductedStocks;
                            }
                        }
                    }

                    if ($remaining_quantity > 0) {
                        // Handle case where there are not enough stocks for the product
                        return array('success' => false, "message" => "Not enough stocks for product ID: " . $product_order["product_id"], "data" => null);
                    }
                }

                // Create order history once
                $order_history = new OrderHistory();
                $order_history->order_id = $order->id;
                $order_history->description = "Order Created";
                $order_history->notes = "Order Created";
                $order_history->admin_id = Auth::guard('admins')->user()->id;
                $order_history->save();

                return array('success' => true, "message" => "Order Created Successfully!", "data" => null);
            }
        } else {
            return array('success' => false, "message" => "Stocks are low", "data" => null);
        }
    }
    
    // public function create(Request $request)
    // {
    
    //     $validation_rules = [
    //         'order_date' => 'required',
    //         'attribution_id' => 'required',
    //         'product_orders' => 'required|array',
    //         'product_orders.*.quantity' => 'required|numeric|min:1',
    //         'product_orders.*.price' => 'required|numeric|min:0',
    //         'product_orders.*.discount' => 'required|numeric|min:0',
    //         'product_orders.*.cogs' => 'required|numeric|min:0',
    //         'delivery_status' => 'required',
    //         'target_delivery_date' => 'required',
    //         'date_delivered' => 'required_if:delivery_status,2',
    //         'order_number' => 'unique:orders',
    //         'returned_date' => 'required_if:delivery_status,3,4',
    //         'tracking_number' => 'nullable|unique:orders',
    //         'province_id' => 'required|integer',
    //         'region_id' => 'required|integer',
    //         'city_id' => 'required|integer',
    //         'date_paid' =>'required_if:mark_as_paid,1',
    //     ];

    //     $input = $request->all();

    //     $purchaseOrders = PurchaseOrder::whereIn('product_id', array_column($input["product_orders"], 'product_id'))
    //     ->orderBy('id', 'asc')
    //     ->get();

    //     $remainingQuantity = array_sum(array_column($input["product_orders"], 'quantity'));
    //     $totalCogs = 0; // Initialize the total COGS

    //     $totalStocksLeft = (clone $purchaseOrders)->sum('stocks_left');

    //     $prefix = "BLU";
    //     $latestOrder = Order::whereNotNull('ref_number')->orderBy('ref_number', 'desc')->first();
    //     $ref_num = $input["order_number"] != null ? $input["order_number"] : 80000;
        
    //     if ($latestOrder != null) {
    //         $ref_num = $input["order_number"] != null ? null : $latestOrder->ref_number + 1;
    //     }

    //     $order_number = $input["order_number"] !== null ? $input["order_number"] : $prefix . $ref_num;
    
    //     $request->validate($validation_rules);
    
    //     $attribution = Attribution::findOrFail($input["attribution_id"]);
    //     $deliveryType = $input['delivery_status'];
    //     $tracking_prefix = 'GPPHBLTTI';
    //     $sequenceNum = 1108; //Start from 1108
    //     $trackingNumber = null;
    
    //     if (in_array(strtolower($attribution->name), ['own delivery', 'owndelivery', 'walk in', 'walk-in'])) {
    //         $latestOrder = Order::where('delivery_status', $deliveryType)->orderBy('id', 'desc')->first();
    //         if ($latestOrder) {
    //             $lastTrackingNumber = $latestOrder->tracking_number;
    //             $lastSequence = substr($lastTrackingNumber, strlen($tracking_prefix));
    //             $sequenceNum = intval($lastSequence) + 1;
    //         }
    //         // Ensure tracking number is unique
    //         do {
    //             $trackingNumber = $tracking_prefix . str_pad($sequenceNum, 9, '0', STR_PAD_LEFT);
    //             $sequenceNum++;
    //         } while (Order::where('tracking_number', $trackingNumber)->exists());
    //     }

    //     if ($totalStocksLeft >= $remainingQuantity) {
    //         $order = new Order();
    //         $order->customer_id = $input["customer_id"];
    //         $order->contact_number = $input["number"];
    //         $order->email = $input["email"];
    //         $order->region_id = $input["region_id"];
    //         $order->province_id = $input['province_id'];
    //         $order->city_id = $input['city_id'];
    //         $order->address = $input["address"];
    //         $order->order_date = $input["order_date"];
    //         $order->admin_id = $input["admin_id"];
    //         $order->attribution_id = $input["attribution_id"];
    //         $order->delivery_status = $input["delivery_status"];
    //         $order->target_delivery_date = $input["target_delivery_date"];
    //         $order->dispatch_date = $input["dispatch_date"] ?? null;
    //         $order->date_delivered = $input["date_delivered"] ?? null;
    //         $order->returned_date = $input["returned_date"] ?? null;
    //         $order->order_number = $order_number;
    //         $order->ref_number = $ref_num;
    //         $order->notes = $input["notes"] ?? null;
    //         $order->courier_id = $input["courier_id"] ?? null;
    //         $order->tracking_number = $trackingNumber;
    //         $order->mark_as_paid = $input["mark_as_paid"] ?? 0;
    //         if ($input["mark_as_paid"] == 1) {
    //             $order->date_paid = $input["date_paid"];
    //         }
    //         $order->mode_of_payment_id = $input["mode_of_payment_id"];
    //         $order->payment_amount = $input["payment_amount"];
    //         $order->payment_notes = $input["payment_notes"] ?? null;
    //         $order->referral_id = $input["referral_id"];
    //         if ($order->save()) {
    //             foreach ($input["product_orders"] as $product_order) {
    //                 $remaining_quantity = $product_order["quantity"];
    
    //                 for ($i = 0; $i < $remaining_quantity; $i++) {
    //                     foreach ($purchaseOrders as $index => $purchaseOrder) {
    //                         if ($purchaseOrder->stocks_left <= 0) {
    //                             if ($purchaseOrder->stocks_left <= 0) {
    //                                 // Skip if stocks_left is zero for the current $purchaseOrder
    //                                 continue;
    //                             }

    //                             $deductedStocks = min($purchaseOrder->stocks_left, $remaining_quantity); // Deduct 1 stock at a time

    //                             $order_product = new OrderProduct();
    //                             $order_product->order_id = $order->id;
    //                             $order_product->product_id = $product_order["product_id"];
    //                             $order_product->quantity = 1;
    //                             $order_product->price = $product_order["price"];
    //                             $order_product->discount = $product_order["discount"] / $product_order["quantity"];
    //                             $order_product->cogs = $purchaseOrder->distributor_price;
    //                             $order_product->po_id = $purchaseOrder->id;
    //                             if ($order_product->save()) {
    //                                 // Update the 'stocks_left' column per index
    //                                 $purchaseOrder->update(['stocks_left' => $purchaseOrder->stocks_left - $deductedStocks]);
    //                                 $remaining_quantity -= $deductedStocks;
                    
    //                                 if ($remaining_quantity <= 0) {
    //                                     // Break the loop when the remaining quantity is fully covered
    //                                     break;
    //                                 }

    //                                 $order_history = new OrderHistory();
    //                                 $order_history->order_id = $order->id;
    //                                 $order_history->description = "Order Created";
    //                                 $order_history->notes = "Order Created";
    //                                 $order_history->admin_id = Auth::guard('admins')->user()->id;
    //                                 $order_history->save();
    //                             }
    //                         }
    //                     }
    //                 }
    //                 $purchaseOrders->each(function ($purchaseOrder) {
    //                     $purchaseOrder->update(['stocks_left' => max(0, $purchaseOrder->stocks_left)]);
    //                 });
    //             } 
    //         }
    //     } else {
    //         return array('success' => false, "message" => "Stocks are low", "data" => null);
    //     }
    //     return array('success' => true, "message" => "Order Created Succesfully!", "data" => null);
    // }
    
    //For the orders activity log create
    private function logActivity($activity, $order){

        $admin_id = auth()->guard('admins')->id();
        $customer = Customer::find($order->customer_id);
        $activity = 'Created An Order ' . $order->order_number . ' for Customer ' . $customer->name;

        ActivityLogs::create([
            'source' => 'Orders',
            'admin_id' => $admin_id,
            'name' => $order->order_number,
            'activity' => $activity
        ]);

    }

    private function generateTrackingNumber($attributionId, $deliveryStatus)
    {
        $tracking_prefix = 'GPPHBLTTI';
        $sequenceNum = 1108; //Start from 1108
    
        $latestOrder = Order::where('attribution_id', $attributionId)
            ->where('delivery_status', $deliveryStatus)
            ->orderBy('id', 'desc')
            ->first();
    
        if ($latestOrder) {
            $lastTrackingNumber = $latestOrder->tracking_number;
            $lastSequence = substr($lastTrackingNumber, strlen($tracking_prefix));
            $sequenceNum = intval($lastSequence) + 1;
        }
    
        // Ensure tracking number is unique
        do {
            $trackingNumber = $tracking_prefix . str_pad($sequenceNum, 9, '0', STR_PAD_LEFT);
            $sequenceNum++;
        } while (Order::where('tracking_number', $trackingNumber)->exists());
    
        return $trackingNumber;
    }

    public function update(Request $request)
    {
        $validation_rules = [
            'delivery_status' => 'required',
            'target_delivery_date' => 'required',
            // 'dispatch_date' => 'required_if:delivery_status,!=,0',
            'date_delivered' => 'required_if:delivery_status,2',
            'returned_date' => 'required_if:delivery_status,3,4',
            'date_paid' => 'required_if:mark_as_paid,1',
        ];
        
        $request->validate($validation_rules);

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
        $order->region_id = $input["region_id"];
        $order->province_id = $input["province_id"];
        $order->city_id = $input["city_id"];
        $order->admin_id = $input["admin_id"];
        $order->delivery_status = $input["delivery_status"];
        $order->target_delivery_date = $input["target_delivery_date"];
        $order->dispatch_date = $input["dispatch_date"];
        $order->returned_date = $input["returned_date"];
        $order->address = $input["address"];
        $order->date_delivered = $input["date_delivered"];
        $order->order_number = $input["order_number"];
        $order->courier_id = $input["courier_id"];
        $order->order_date = $input["order_date"];
        $order->tracking_number = $input["tracking_number"];
        $order->date_paid = $input["date_paid"];

        if (isset($input["delivery_status"]) && $input["delivery_status"] == 2) {
            $order->mark_as_paid = 1;
        }else if(isset($input['delivery_status']) && $input['delivery_status'] == 0){
            $order->mark_as_paid = 0;
        }

        if ($request->has('order_products')) {
            $orderProducts = [];
            foreach($request->order_products as $product) {
                $orderProducts[$product['product_id']] = [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'discount' => $product['discount'],
                    'cogs' => $product['cogs'],
                ];
            }
            $order->order_products()->sync($orderProducts);
        }

        $order->save();

        return array("success" => true, "message" => "Order Updated Successfully!", "data" => null);
    }

    //For the update del status activity log update
    private function delStatusUpdateLogActivity(
            $activity, 
            $order,  
            $previous_delStatus, 
            $previous_targetDelDate, 
            $previous_dispatchDate, 
            $previous_returnedDate, 
            $previous_dateDelivered, 
            $previous_trackingNumber,
            $previous_order_number, 
            $previous_attribution_id, 
            $previous_admin_id, 
            $previous_order_date, 
            $previous_address
        )
        
    {

        $admin_id = auth()->guard('admins')->id();
        $activity = '';
        $changes = [];
        if(strcmp($order->order_number,  $previous_order_number) !== 0){
            $changes[] = 'Updated A Order Number From ' . $previous_order_number . ' to ' . $order->order_number;
        }
        if(strcmp($order->attribution_id,  $previous_attribution_id) !== 0){
            $previousAttributionName = Attribution::find($previous_attribution_id)->name;
            $currentAttributionName = Attribution::find($order->attribution_id)->name;
            $changes[] = 'Updated The Order ' . $order->order_number . ' Attribution From ' . $previousAttributionName . ' to ' . $currentAttributionName;
        }
        if(strcmp($order->admin_id, $previous_admin_id) !== 0){
            $previousAdminId = Admin::find($previous_admin_id)->fullName();
            $currentAdminId = Admin:: find($order->admin_id)->fullName();
            $changes[] = 'Updated The Order ' . $order->order_number . ' Sales Assign From ' . $previousAdminId . ' to ' . $currentAdminId;
        }
        if(strcmp($order->tracking_number,  $previous_trackingNumber) !== 0){
            $changes[] = 'Updated A Tracking Number From ' . $previous_trackingNumber . ' to ' . $order->tracking_number;
        }
        if($order->delivery_status !== $previous_delStatus) {
            $previous_delStatus_name = $this->getDeliveryStatusName($previous_delStatus);
            $current_delStatus_name = $this->getDeliveryStatusName($order->delivery_status);
            $changes[] = 'Updated The Order ' . $order->order_number . ' Delivery Status From ' . $previous_delStatus_name . ' to ' . $current_delStatus_name;
        }
        if(strcmp($order->dispatch_date,  $previous_dispatchDate) !== 0){
            $changes[] = 'Updated The Order ' . $order->order_number . ' Dispatch Date From ' . $previous_dispatchDate . ' To ' . $order->dispatch_date;
        }
        if(strcmp($order->date_delivered,  $previous_dateDelivered) !== 0){
            $changes[] = 'Updated The Order ' . $order->order_number . ' Date Delivered From ' . $previous_dateDelivered . ' To ' . $order->date_delivered;
        }
        if(strcmp($order->returned_date,  $previous_returnedDate) !== 0){
            $changes[] = 'Updated The Order ' . $order->order_number . ' Returned Date From ' . $previous_returnedDate . ' To ' . $order->returned_date;
        }
        if(strcmp($order->address, $previous_address) !== 0){
            $changes[] = 'Updated The Order ' . $order->order_number . ' Address From ' . $previous_address . ' to ' . $order->address;
        }
   
   
        if(count($changes) > 0){
            $activity = implode(' and ', $changes);
        }

        ActivityLogs::create([
            'source' => 'Orders',
            'admin_id' => $admin_id,
            'name' => $order->order_number,
            'activity' => $activity
        ]);
    
    }


    //HAD TO IMPLEMENT getDeliveryStatusName from Order Model in order to Log the Del status name correctly in Activity Log
    private function getDeliveryStatusName($status) {
        if ($status == 0) {
            return "Pending";
        } elseif ($status == 1) {
            return "Shipped";
        } elseif ($status == 2) {
            return "Delivered";
        } elseif ($status == 3) {
            return "RTS";
        } elseif ($status == 4) {
            return "Returned";
        } elseif ($status == 5) {
            return "Out For Delivery";
        } elseif ($status == 6){
            return "Cancelled";
        }
    }







    public function orderHistory(Request $request, $id)
    {
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $input = $request->all();

        $order = Order::findOrFail($id);

        $query = OrderHistory::where('order_id', '=', $order->id)->orderBy($column, $request->order);

        $data = $query->paginate($request->per_page);

        foreach ($data as $order_history) {
            $order_history->created_at_s = Carbon::parse($order_history->created_at)->toDayDateTimeString();
            $order_history->admin_name = $order_history->admin_id != null ? $order_history->admin->fullName() : "";
        }

        return array("success" => true, "message" => "", "data" => $data);

    }

    public function myOrderList(Request $request)
    {
        $input = $request->all();
        $column = $request->column;
        if (strcmp($column, "order_date_s") == 0) {
            $column = "order_date";
        } else if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = Order::where('admin_id', '=', Auth::guard('admins')->user()->id)->orderBy($column, $request->order);

        $orders = $query->paginate($request->per_page);

        foreach ($orders as $order)
        {
            $order_products = OrderProduct::where('order_id', '=', $order->id)->get();
            $order->order_date_s = Carbon::parse($order->order_date)->format("M j, Y");
            $order->customer_name = $order->customers->name;
            $order->region_id = $order->regions->name;
            $order->province_id = $order->provinces->name;
            $order->city_id = $order->cities->name;

            $order->region_id = $order->regions->name;
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

        return array("success" => true, "messsage" => "", "data" => $orders);
    }

    public function addOrderPaymentMethod(Request $request)
    {
        $validation_rules = [
            'amount' => 'required|min:0',
            'payment_method_id' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $order = Order::find($input["order_id"]);

        $payment_method = new OrderPaymentMethod();
        $payment_method->order_id = $input["order_id"];
        $payment_method->amount = $input["amount"];
        $payment_method->payment_method_id = $input["payment_method_id"];
        $payment_method->notes = $input["notes"];
        $payment_method->admin_id = Auth::guard('admins')->user()->id;
        $payment_method->save();

        return array("success" => true, "message" => "Added Order Payment Method", "data" => null);
    }



    public function getOrderPaymentMethods($id, Request $request)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Can\'t find order', 'data' => null], 404);
        }

        $column = $request->input('column');
        $direction = $request->input('order');

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query = OrderPaymentMethod::where("order_id", "=", $order->id)->orderBy($column, $direction);

        $orderPaymentMethods = $query->paginate($request->input('per_page', 10));

        foreach ($orderPaymentMethods as $orderPaymentMethod) {
            $orderPaymentMethod->created_at_s = Carbon::parse($orderPaymentMethod->created_at)->toDayDateTimeString();
            $orderPaymentMethod->admin_name = $orderPaymentMethod->admin_id ? $orderPaymentMethod->admin->fullName() : "";
            $orderPaymentMethod->payment_method_name = $orderPaymentMethod->method_payment_id ? $orderPaymentMethod->mode_of_payment->name : "";
            $orderPaymentMethod->amount_s = "₱ " . number_format($orderPaymentMethod->amount, 2);
        }

        return response()->json(['success' => true, 'message' => '', 'data' => $orderPaymentMethods]);
    }

    

    public function updateOrderPaymentMethod(Request $request)
    {
        $validation_rules = [
            'order_id' => '',
            'mode_of_payment_id' => '',
            'payment_amount' => '',
            'payment_notes' => '',
            'mark_as_paid' => '',
            'date_paid' => 'required_if:mark_as_paid,1',
        ];
        $request->validate($validation_rules);
        $input = $request->all();

        //Declaration of table variable name
        $order = Order::find($input["order_id"]);
        if (!$order) {//If Table ORDER not found, will pop up error message
            return array("success" => false, "message" => "Order not found", "data" => null);
        }
        $order->mark_as_paid = $input["mark_as_paid"];
        $order->mode_of_payment_id = $input["mode_of_payment_id"];
        $order->payment_amount = $input["payment_amount"];
        $order->payment_notes = $input["payment_notes"];
        $order->date_paid = $input["date_paid"];
        $order->save();

        return array("success" => true, "message" => "Update Order Payment Method Successfully!", "data" => null);
    }
    


    public function markAsPaid(Request $request)
    {
        $input = $request->all();

        $order = Order::find($input["id"]);

        $order->mark_as_paid = $input["mark_as_paid"];
        $order->save();

        return array('success' => true, "message" => "Order Mark As Paid Succesfully!", "data" => null);
    }
    
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);

            $orderProducts = OrderProduct::where('order_id', $order->id)->get();

            foreach ($orderProducts as $orderProduct) {
                $purchaseOrder = PurchaseOrder::find($orderProduct->product_id);
    
                if ($purchaseOrder) {
                    $purchaseOrder->stocks_left += $orderProduct->quantity;
                    $purchaseOrder->save();
                }
            }

            $orderHistory = new OrderHistory();
            $orderHistory->order_id = $order->id;
            $orderHistory->description = "Order Deleted";
            $orderHistory->notes = "Order Deleted";
            $orderHistory->admin_id = Auth::guard('admins')->user()->id;
            $orderHistory->save();
    
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
    
    // public function destroy(Request $request, $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $order = Order::findOrFail($id);
           
    //         // Save Order History
    //         $order_history = new OrderHistory();
    //         $order_history->order_id = $order->id;
    //         $order_history->description = "Order Deleted";
    //         $order_history->notes = "Order Deleted";
    //         $order_history->admin_id = Auth::guard('admins')->user()->id;
    //         $order_history->save();

    //         $order->delete();

    //         $this->deleteLogActivity('order', $order);

    //         DB::commit();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Order deleted successfully.'
    //         ]);
            

    //         return redirect()->view('admin.orders.my-orders');

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Error deleting order: ' . $e->getMessage()
    //         ], 500);

    //     }
    // }

    //For the orders activity log DELETE
    private function deleteLogActivity($activity, $order){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Deleted An Order ' . $order->order_number;

        ActivityLogs::create([
            'source' => 'Orders',
            'admin_id' => $admin_id,
            'name' => $order->order_number,
            'activity' => $activity
        ]);

    }


    public function updateOrder(Request $request, $id)
    {
        $validation_rules = $request->validate([
            'order_products.*.product_id' => 'nullable|integer', 
            'order_products.*.quantity' => 'nullable|integer', 
            'order_products.*.price' => 'nullable|numeric', 
            'order_products.*.discount' => 'nullable|numeric', 
            'order_products.*.cogs' => 'nullable|numeric',
        ]);

        $request->validate($validation_rules);

        $order = Order::findOrFail($id);

        $input = $request->all();

        $order->customer_id = $input["customer_id"];
        $order->order_number  = $input["order_number"];
        $order->contact_number = $input["contact_number"];
        $order->email = $input["email"];
        $order->address = $input["address"];
        $order->order_date = $input["order_date"];
        $order->attribution_id = $input["attribution_id"];
        $order->region_id = $input["region_id"];
        $order->province_id = $input["province_id"];
        $order->city_id = $input["city_id"];
        $order->delivery_status = $input["delivery_status"];
        $order->target_delivery_date = $input["target_delivery_date"];
        $order->dispatch_date = $input["dispatch_date"];
        $order->date_delivered = $input["date_delivered"];
        $order->returned_date = $input["returned_date"];
        $order->courier_id = $input["courier_id"];
        $order->notes = $input["notes"];
        $order->admin_id = $input["admin_id"];
        $order->mark_as_paid = $input["mark_as_paid"];
        $order->tracking_number = $input["tracking_number"];

        if ($request->has('order_products')) {
            $orderProducts = [];
            foreach($request->order_products as $product) {
                $orderProducts[$product['product_id']] = [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'discount' => $product['discount'],
                    'cogs' => $product['cogs'],
                ];
            }
            $order->order_products()->sync($orderProducts);
        }

        $order->save();

        return array("success" => true, "message" => "Order Updated Succesfully!", "data" => null);
    }


    //For the update order activity log update
    private function orderUpdateLogActivity(
        $activity,
        $order,
        $previous_contact_number,
        $previous_order_number,
        $previous_email,
        $previous_address,
        $previous_attribution_id,
        $previous_region_id,
        $previous_province_id,
        $previous_city_id,
        $previous_admin_id,
        $previous_target_date_delivery,
        $previous_delivery_status,
        $previous_dispatch_date,
        $previous_date_delivered,
        $previous_notes,
        $previous_tracking_number,
        $previous_order_date,
        $previousOrderProducts
    )
    
    {

        $admin_id = auth()->guard('admins')->id();
        $activity = '';
        $changes = [];
        $productChanges = [];//Had to declare another variable for order_product so it will not log if didnt touch

        if(strcmp($order->order_number, $previous_order_number) !== 0 ){
            $changes[] = 'Updated an Order Number ' . $order->order_number . ' Order Number From ' . $previous_order_number . ' to ' . $order->order_number;
        }
        if(strcmp($order->contact_number, $previous_contact_number) !== 0){
            $changes[] = 'Updated An Order Number ' . $order->order_number . ' Customer Contact Number From ' . $previous_contact_number . ' to ' . $order->contact_number; 
        }
        if(strcmp($order->email, $previous_email) !== 0){
            $changes[] = 'Updated An Order Number ' . $order->order_number . ' Customer Email From ' . $previous_email . ' to ' . $order->email;
        }
        if(strcmp($order->address, $previous_address) !== 0){
            $changes[] = 'Updated An Order Number ' . $order->order_number . ' Customer Address From ' . $previous_address . ' to ' . $order->address;
        }
        if(strcmp($order->attribution_id, $previous_attribution_id) !== 0){
            $previousAttributionId = Attribution::find($previous_attribution_id)->name;
            $currentAttributionId = Attribution::find($order->attribution_id)->name;
            $changes[] = 'Updated An Order Number ' . $order->order_number . ' Attribution From ' . $previousAttributionId . ' to ' . $currentAttributionId; 
        }
        if(strcmp($order->region_id, $previous_region_id) !== 0){
            $previousRegionId = Region::find($previous_region_id)->name;
            $currentRegionId = Region::find($order->region_id)->name;
            $changes[] = 'Updated An Order Number ' . $order->order_number . ' Region From ' . $previousRegionId . ' to ' . $currentRegionId; 
        }
        if (strcmp($order->province_id, $previous_province_id) !== 0) {
            $previousProvinceId = Province::find($previous_province_id)->name;
            $currentProvinceId = Province::find($order->province_id)->name;
            $changes[] = 'Updated An Order Number ' . $order->order_number . ' Province From ' . $previousProvinceId . ' to ' . $currentProvinceId; 
        }
        if (strcmp($order->city_id, $previous_city_id) !== 0) {
            $previousCityId = City::find($previous_city_id)->name;
            $currentCityId = City::find($order->city_id)->name;
            $changes[] = 'Updated An Order Number ' . $order->order_number . ' City From ' . $previousCityId . ' to ' . $currentCityId; 
        }
        if(strcmp($order->admin_id, $previous_admin_id) !== 0){
            $previousAdminId = Admin::find($previous_admin_id)->fullName();
            $currentAdminId = Admin::find($order->admin_id)->fullName();
            $changes[] = 'Updated An Order Number ' . $order->order_number . ' Sales Assign From ' . $previousAdminId . ' to ' . $currentAdminId;
        }
        if(strcmp($order->target_delivery_date, $previous_target_date_delivery) !== 0){
            $changes[] = 'Updated An Order Number ' . $order->order_number . ' Target Date Delivery From ' . $previous_target_date_delivery . ' to ' . $order->target_delivery_date;
        }
        if($order->delivery_status !== $previous_delivery_status){
            $previous_delivery_status_name = $this->getDeliveryStatusName($previous_delivery_status);//getDeliveryStatusName source is at line 546
            $current_delivery_status_name = $this->getDeliveryStatusName($order->delivery_status);//getDeliveryStatusName source is at line 546
            $changes[] = 'Updated The Order ' . $order->order_number . ' Delivery Status From ' . $previous_delivery_status_name . ' to ' . $current_delivery_status_name;
        }
        if(strcmp($order->dispatch_date, $previous_dispatch_date) !== 0){
            $changes[] = 'Updated An Order ' . $order->order_number . ' Dispatch Date From ' . $previous_dispatch_date . ' to ' . $order->dispatch_date;
        }
        if(strcmp($order->date_delivered, $previous_date_delivered) !== 0){
            $changes[] = 'Updated An Order ' . $order->order_number . ' Date Delivered ' . $previous_date_delivered . ' to ' . $order->date_delivered;
        }
        if(strcmp($order->tracking_number, $previous_tracking_number) !== 0){
            $changes[] = 'Updated An Order ' . $order->order_number . ' Tracking Number From ' . $previous_tracking_number . ' to ' . $order->tracking_number;
        }
        if(strcmp($order->notes, $previous_notes) !== 0){
            $changes[] = 'Updated An Order ' . $order->order_number . ' Order Notes From ' . $previous_notes . ' to ' . $order->notes;
        }
        if(strcmp($order->order_date, $previous_order_date) !== 0){
            $changes[] = 'Updated An Order ' . $order->order_number . ' Order Date ' . $previous_order_date . ' to ' . $order->order_date;
        }

        // This is for updating order_product fields such as product, price, discount cogs and also for activity logging
        // foreach ($order->order_products as $orderProduct) {
        //     if (isset($previousOrderProducts[$orderProduct->product_id])) {
        //         $previousProductData = $previousOrderProducts[$orderProduct->product_id];
        //         $changesArr = [];
        
        //         if ($previousProductData['quantity'] != $orderProduct->pivot->quantity) {
        //             $changesArr[] = "Quantity: {$previousProductData['quantity']} -> {$orderProduct->pivot->quantity}";
        //         }
        //         if ($previousProductData['price'] != $orderProduct->pivot->price) {
        //             $changesArr[] = "Price: {$previousProductData['price']} -> {$orderProduct->pivot->price}";
        //         }
        //         if ($previousProductData['discount'] != $orderProduct->pivot->discount) {
        //             $changesArr[] = "Discount: {$previousProductData['discount']} -> {$orderProduct->pivot->discount}";
        //         }
        //         if ($previousProductData['cogs'] != $orderProduct->pivot->cogs) {
        //             $changesArr[] = "COGS: {$previousProductData['cogs']} -> {$orderProduct->pivot->cogs}";
        //         }
        
        //         if (!empty($changesArr)) {
        //             // $productChanges[] = 'Updated Product From ' . $previousProductData['name'] . ' to ' . $orderProduct->name . ' (' . implode(', ', $changesArr) . ')';
        //             $productChanges[] = 'Updated A Product Details For ' . $order->order_number . ' Product ' . $orderProduct->name . ' (' . implode(', ', $changesArr) . ')';
        //         }
        //     }
        // }
        
        // This is for updating order_product fields such as product, price, discount cogs and also for activity logging
        // foreach ($order->order_products as $orderProduct) {
        //     if (isset($previousOrderProducts[$orderProduct->product_id])) {
        //         $previousProductData = $previousOrderProducts[$orderProduct->product_id];
        //         if ($previousProductData['quantity'] != $orderProduct->pivot->quantity ||
        //             $previousProductData['price'] != $orderProduct->pivot->price ||
        //             $previousProductData['discount'] != $orderProduct->pivot->discount ||
        //             $previousProductData['cogs'] != $orderProduct->pivot->cogs) {

        //             $productChanges[] = 'Updated Product From ' . $previousProductData['name'] . ' to ' . $orderProduct->name . ' (Quantity: ' . $previousProductData['quantity'] . ' -> ' . $orderProduct->pivot->quantity . ', Price: ' . $previousProductData['price'] . ' -> ' . $orderProduct->pivot->price . ', Discount: ' . $previousProductData['discount'] . ' -> ' . $orderProduct->pivot->discount . ', COGS: ' . $previousProductData['cogs'] . ' -> ' . $orderProduct->pivot->cogs . ')';
                
        //         }
        //     }
        // }
    
        // This is for updating order_product fields such as product, price, discount cogs and also for activity logging
        // foreach ($order->order_products as $orderProduct) {
        //     if (isset($previousOrderProducts[$orderProduct->product_id])) {
        //         $previousProductData = $previousOrderProducts[$orderProduct->product_id];
        //         $productChangeArr = [];
        
        //         if ($previousProductData['quantity'] != $orderProduct->pivot->quantity) {
        //             $productChangeArr[] = "Quantity: {$previousProductData['quantity']} -> {$orderProduct->pivot->quantity}";
        //         }
        //         if ($previousProductData['price'] != $orderProduct->pivot->price) {
        //             $productChangeArr[] = "Price: {$previousProductData['price']} -> {$orderProduct->pivot->price}";
        //         }
        //         if ($previousProductData['discount'] != $orderProduct->pivot->discount) {
        //             $productChangeArr[] = "Discount: {$previousProductData['discount']} -> {$orderProduct->pivot->discount}";
        //         }
        //         if ($previousProductData['cogs'] != $orderProduct->pivot->cogs) {
        //             $productChangeArr[] = "COGS: {$previousProductData['cogs']} -> {$orderProduct->pivot->cogs}";
        //         }
        
        //         if (!empty($productChangeArr)) {
        //             $productChanges[] = 'Updated Product From ' . $previousProductData['name'] . ' to ' . $orderProduct->name . ' (' . implode(', ', $productChangeArr) . ')';
        //         }
        //     }
        // }

        foreach ($order->order_products as $orderProduct) {
            if (isset($previousOrderProducts[$orderProduct->product_id])) {
                $previousProductData = $previousOrderProducts[$orderProduct->product_id];
                $changesArr = [];
        
                $fields = [
                    'quantity' => 'Quantity',
                    'price' => 'Price',
                    'discount' => 'Discount',
                    'cogs' => 'COGS',
                ];
        
                $hasChanges = false;
        
                foreach ($fields as $field => $label) {
                    if ($previousProductData[$field] != $orderProduct->pivot->$field) {
                        $changesArr[] = "{$label}: {$previousProductData[$field]} -> {$orderProduct->pivot->$field}";
                        $hasChanges = true;
                    }
                }
        
                if ($hasChanges) {
                    // $message = ' From Product ' . $previousProductData['name'] . ' To ' . $orderProduct->name;
                    $message = ' Updated the product ' . $orderProduct->name;
                    if (count($changesArr) > 1) {
                        $message .= ' (' . implode(', ', $changesArr) . ')';
                    } else {
                        $message .= ' (' . $changesArr[0] . ')';
                    }
                    $productChanges[] = $message;
                }
            }
        }
        
        //Below is for the normal fields in the order and then logs it to activity logs
        if (count($changes) > 0) {
            $activity = implode(' and ', $changes);
            ActivityLogs::create([
                'source' => 'Orders',
                'admin_id' => $admin_id,
                'name' => $order->order_number,
                'activity' => $activity
            ]);
        }
        
        //This is for the productChanges ActivityLogs Create initiation for order_products
        if (count($productChanges) > 0) {
            $logMessage = 'Updated product details for order ' . $order->order_number;
            if (count($productChanges) > 1) {
                $logMessage .= ': ' . implode(', ', $productChanges);
            } else {
                $logMessage .= $productChanges[0];
            }
            ActivityLogs::create([
                'source' => 'Orders',
                'admin_id' => $admin_id,
                'name' => $order->order_number,
                'activity' => $logMessage
            ]);
        }
        
    }

    public function exportOrder(Request $request)
    {
        $input = $request->all();

        $order_from = $input["order_from"];
        $order_to = $input["order_to"];
        $region = $input["region"];
        $province = $input["province"];
        $city = $input["city"];
        $admin = $input["admin"];
        $attribution = $input["attribution"];
        $courier = $input["courier"];
        $delivery_status = $input["delivery_status"];
        $dispatch_from = $input["dispatch_from"];
        $dispatch_to = $input["dispatch_to"];
        $delivered_from = $input["delivered_from"];
        $delivered_to = $input["delivered_to"];
        $payment_status = $input["payment_status"];
        $date_paid_from = $input["date_paid_from"];
        $date_paid_to = $input["date_paid_to"];
        $target_delivery_from = $input["target_delivery_from"];
        $target_delivery_to = $input["target_delivery_to"];
        $mop_id = $input["mop_id"];

        $auth = Auth::guard('admins')->user()->email;

        OrderExportJob::dispatch($order_from, $order_to, $auth, $region, $province, $city, $admin, $attribution, $courier,
        $delivery_status, $dispatch_from, $dispatch_to, $delivered_from, $delivered_to, $payment_status, $date_paid_from, $date_paid_to,
        $target_delivery_from, $target_delivery_to, $mop_id)
        ->onConnection('database')->onQueue(Constants::$queue_email);

        return array("success" => true, "message" => "Download Export has been queued successfully", "data" => null);
    }
}
