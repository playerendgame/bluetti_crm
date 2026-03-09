<?php

namespace App\Http\Controllers\Ajax\Admin\Orders;

use Auth;
use Carbon\Carbon;
use App\Models\City;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Region;
use App\Models\Courier;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Province;
use App\Models\Purchase;
use App\Helpers\Constants;
use App\Models\Attribution;
use App\Jobs\OrderExportJob;
use App\Models\ActivityLogs;
use App\Models\OrderHistory;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Models\ModeOfPayment;
use App\Models\PurchaseOrder;
use App\Models\OrderPaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
// use App\Services\GoogleSheetService;
use App\Http\Controllers\Ajax\Admin\Orders\GoogleSheetController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetDate;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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

                //Google Sheet Implementation to separate controller
                $googleSheetController = app(GoogleSheetController::class);
                $googleSheetController->appendCreateDataToGoogleSheet($order);

                return array('success' => true, "message" => "Order Created Successfully!", "data" => null);
            }
        } else {
            return array('success' => false, "message" => "Stocks are low", "data" => null);
        }
    }
    
    
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
        };

        $order->save();

        //Google Sheet Implementation to separate controller
        $googleSheetController = app(GoogleSheetController::class);
        $googleSheetController->appendDelStatusUpdateDataToGoogleSheet($order);

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

        //Google Sheet Implementation to separate controller
        $googleSheetController = app(GoogleSheetController::class);
        $googleSheetController->appendPaymentDetailsUpdateToGoogleSheet($order);


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

    /** EXPORTING ORDER DATA */
    // public function exportOrder(Request $request)
    // {
    //     $input = $request->all();

    //     $order_from = $input["order_from"];
    //     $order_to = $input["order_to"];
    //     $region = $input["region"];
    //     $province = $input["province"];
    //     $city = $input["city"];
    //     $admin = $input["admin"];
    //     $attribution = $input["attribution"];
    //     $courier = $input["courier"];
    //     $delivery_status = $input["delivery_status"];
    //     $dispatch_from = $input["dispatch_from"];
    //     $dispatch_to = $input["dispatch_to"];
    //     $delivered_from = $input["delivered_from"];
    //     $delivered_to = $input["delivered_to"];
    //     $payment_status = $input["payment_status"];
    //     $date_paid_from = $input["date_paid_from"];
    //     $date_paid_to = $input["date_paid_to"];
    //     $target_delivery_from = $input["target_delivery_from"];
    //     $target_delivery_to = $input["target_delivery_to"];
    //     $mop_id = $input["mop_id"];

    //     $auth = Auth::guard('admins')->user()->email;

    //     OrderExportJob::dispatch($order_from, $order_to, $auth, $region, $province, $city, $admin, $attribution, $courier,
    //     $delivery_status, $dispatch_from, $dispatch_to, $delivered_from, $delivered_to, $payment_status, $date_paid_from, $date_paid_to,
    //     $target_delivery_from, $target_delivery_to, $mop_id)
    //     ->onConnection('database')->onQueue(Constants::$queue_email);

    //     return array("success" => true, "message" => "Download Export has been queued successfully", "data" => null);
    // }

    public function exportExcel(Request $request){
        //fetching order relationships
        $query = Order::with(['customers', 'admin', 'courier', 'attribution', 'mode_of_payment', 'regions', 'provinces', 'cities', 'order_product_items.product']);
        
        //Apply filters
        if ($request->has('order_from') && $request->has('order_to') && $request->order_from != '' && $request->order_to != '' && $request->order_from != '0' && $request->order_to != '0') {
            $query->whereBetween('order_date', [Carbon::parse($request->order_from)->startOfDay(), Carbon::parse($request->order_to)->endOfDay()]);
        }

        if ($request->has('admin') && $request->admin != 0 && $request->admin != '' && $request->admin != '0') {
            $query->where('admin_id', "=", $request->admin);
        }

        if ($request->has('region') && $request->region != 0 && $request->region != '' && $request->region != '0') {
            $query->where('region_id', '=', $request->region);
        }

        if ($request->has('province') && $request->province != 0 && $request->province != '' && $request->province != '0') {
            $query->where('province_id', '=', $request->province);
        }

        if ($request->has('city') && $request->city != 0 && $request->city != '' && $request->city != '0') {
            $query->where('city_id', '=', $request->city);
        }

        if ($request->has('attribution') && $request->attribution != 0 && $request->attribution != '' && $request->attribution != '0') {
            $query->where('attribution_id', '=', $request->attribution);
        }

        if ($request->has('courier') && $request->courier != 0 && $request->courier != '' && $request->courier != '0') {
            $query->where('courier_id', "=", $request->courier);
        }

        if ($request->has('delivery_status') && $request->delivery_status != 99 && $request->delivery_status != '' && $request->delivery_status != '99') {
            $query->where('delivery_status', '=', $request->delivery_status);
        }

        if ($request->has('dispatch_from') && $request->has('dispatch_to') && $request->dispatch_from != '' && $request->dispatch_to != '' && $request->dispatch_from != '0' && $request->dispatch_to != '0') {
            $query->whereBetween('dispatch_date', [Carbon::parse($request->dispatch_from)->startOfDay(), Carbon::parse($request->dispatch_to)->endOfDay()]);
        }

        if ($request->has('delivered_from') && $request->has('delivered_to') && $request->delivered_from != '' && $request->delivered_to != '' && $request->delivered_from != '0' && $request->delivered_to != '0') {
            $query->whereBetween('date_delivered', [Carbon::parse($request->delivered_from)->startOfDay(), Carbon::parse($request->delivered_to)->endOfDay()]);
        }

        if ($request->has('payment_status') && $request->payment_status != 99 && $request->payment_status != '' && $request->payment_status != '99') {
            $query->where("mark_as_paid", "=", $request->payment_status);
        }

        if ($request->has('date_paid_from') && $request->has('date_paid_to') && $request->date_paid_from != '' && $request->date_paid_to != '' && $request->date_paid_from != '0' && $request->date_paid_to != '0') {
            $query->whereBetween('date_paid', [Carbon::parse($request->date_paid_from)->startOfDay(), Carbon::parse($request->date_paid_to)->endOfDay()]);
        }

        if ($request->has('target_delivery_from') && $request->has('target_delivery_to') && $request->target_delivery_from != '' && $request->target_delivery_to != '' && $request->target_delivery_from != '0' && $request->target_delivery_to != '0') {
            $query->whereBetween('target_delivery_date', [Carbon::parse($request->target_delivery_from)->startOfDay(), Carbon::parse($request->target_delivery_to)->endOfDay()]);
        }

        if ($request->has('mop_id') && $request->mop_id != 0 && $request->mop_id != '' && $request->mop_id != '0') {
            $query->where('mode_of_payment_id', $request->mop_id);
        }

        $orders = $query->get();

        //Creates spreadsheet with data
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        //sheet column headers
        $headers = [
            'Sales',
            'Order #',
            'Date',
            'Customer Name',
            'Contact #',
            'Complete Address',
            'Quantity',
            'Product',
            'Price',
            'COGS',
            '%COGS',
            'Sales Invoice',
            'Order',
            'Courier',
            'Attribution',
            'Mode of Payment',
            'Payment Status',
            'Target Delivery/ Pick up Date',
            'Packing Status',
            'Dispatch Date',
            'Tracking Number',
            'Date Delivered',
            'Status',
            'Customer Type'
        ];
        
        //write headers with styling
        $col = 'A';
        foreach ($headers as $header) {
            $cell = $col . '1';
            $sheet->setCellValue($cell, $header);
            
            //Applied styling to header
            $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB('FF9FC5E9');
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            
            $col++;
        }
        
        //Add some padding to header row
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        //write data - one row per order, combining products
        $row = 2;
        foreach ($orders as $order) {
            $this->writeOrderRowCombined($sheet, $row, $order);
            $row++;
        }
        
        //Auto-size columns
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        //Styling of data rows, and data formatting
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            //Style Price column
            $priceRange = 'I2:I' . $lastRow;
            $sheet->getStyle($priceRange)->getNumberFormat()->setFormatCode('₱ #,##0.00');
            $sheet->getStyle($priceRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            
            //Style COGS column
            $cogsRange = 'J2:J' . $lastRow;
            $sheet->getStyle($cogsRange)->getNumberFormat()->setFormatCode('₱ #,##0.00');
            $sheet->getStyle($cogsRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            
            //Style %COGS column
            $percentCogsRange = 'K2:K' . $lastRow;
            $sheet->getStyle($percentCogsRange)->getNumberFormat()->setFormatCode('0.00%');
            $sheet->getStyle($percentCogsRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            //Style Quantity column
            $quantityRange = 'G2:G' . $lastRow;
            $sheet->getStyle($quantityRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            //Paddings to data rows
            foreach (range(2, $lastRow) as $dataRow) {
                $sheet->getRowDimension($dataRow)->setRowHeight(20);
            }
            
            //Center align all text columns vertically
            $textColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X'];
            foreach ($textColumns as $col) {
                $range = $col . '2:' . $col . $lastRow;
                $sheet->getStyle($range)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }
        }
        
        // Set headers for download
        $filename = 'orders-export-' . date('Y-m-d-H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        
        exit;
    }

    //cREATING Data if order has 2 or more products
    private function writeOrderRowCombined($sheet, $row, $order){
        //Get all order products for this order 
        $orderProducts = $order->order_product_items;
        
        //Check if we have order products
        if ($orderProducts->isEmpty()) {
            $data = [
                $order->admin ? $order->admin->fullName() : '', //Sales
                $order->order_number, //Order #
                $order->order_date ? Carbon::parse($order->order_date)->format('Y-m-d') : '', //Date
                $order->customers ? $order->customers->name : '',
                $order->customers ? $order->customers->number : '', //Contact #
                $order->address, //Complete Address
                '', //Quantity
                'No Products', //Product
                '', //Price
                '', //COGS
                null, //%COGS - Use null for empty percentage values
                '', //Sales Invoice
                $order->attribution ? $order->attribution->name : '', //Order (Attribution)
                $order->courier ? $order->courier->name : '', //Courier
                $order->attribution ? $order->attribution->name : '', //Attribution
                $order->mode_of_payment ? $order->mode_of_payment->name : '', //Mode of Payment
                $order->mark_as_paid == 1 ? 'Paid' : 'Unpaid', //Payment Status
                $order->target_delivery_date ? Carbon::parse($order->target_delivery_date)->format('Y-m-d') : '', //Target Delivery/ Pick up Date
                '', //Packing Status
                $order->dispatch_date ? Carbon::parse($order->dispatch_date)->format('Y-m-d') : '', //Dispatch Date
                $order->tracking_number, //Tracking Number
                $order->date_delivered ? Carbon::parse($order->date_delivered)->format('Y-m-d') : '', //date Delivered
                $order->deliveryStatusName(), //Status
                '' //Customer Type
            ];
        } else {
            //Calculate totals
            $totalQuantity = $orderProducts->sum('quantity');
            $totalPrice = $orderProducts->sum(function($orderProduct) {
                return ($orderProduct->quantity * $orderProduct->price) - $orderProduct->discount;
            });
            $totalCogs = $orderProducts->sum('cogs');
            
            //Calculate average COGS percentage
            $percentCogs = ($totalPrice > 0) ? (($totalCogs / $totalPrice) * 100) : 0;
            
            //Combine product names
            $productNames = $orderProducts->map(function($orderProduct) {
                return $orderProduct->product ? $orderProduct->product->name : 'Unknown Product';
            })->implode(' + ');
            
            //Map delivery status
            $deliveryStatusMap = [
                0 => 'Pending',
                1 => 'Shipped',
                2 => 'Delivered',
                3 => 'RTS',
                4 => 'Returned',
                5 => 'Out For Delivery',
                6 => 'Cancelled'
            ];
            
            //Map payment status
            $paymentStatus = $order->mark_as_paid == 1 ? 'Paid' : 'Unpaid';
            
            $data = [
                $order->admin ? $order->admin->fullName() : '', //Sales
                $order->order_number, //Order #
                $order->order_date ? Carbon::parse($order->order_date)->format('Y-m-d') : '', //Date
                $order->customers ? $order->customers->name : '',
                $order->customers ? $order->customers->number : '', //Contact #
                $order->address, //Complete Address
                $totalQuantity, //Quantity (sum of all products)
                $productNames, //Product (combined names)
                $totalPrice, //Price (sum of all products)
                $totalCogs, //COGS (sum of all products)
                $percentCogs / 100, //%COGS (convert to decimal for Excel percentage formatting)
                '', //Sales Invoice
                $order->attribution ? $order->attribution->name : '', //Order (Attribution)
                $order->courier ? $order->courier->name : '', //Courier
                $order->attribution ? $order->attribution->name : '', //Attribution
                $order->mode_of_payment ? $order->mode_of_payment->name : '', //Mode of Payment
                $paymentStatus, //Payment Status
                $order->target_delivery_date ? Carbon::parse($order->target_delivery_date)->format('Y-m-d') : '', //Target Delivery/ Pick up Date
                '', //Packing Status
                $order->dispatch_date ? Carbon::parse($order->dispatch_date)->format('Y-m-d') : '', //Dispatch Date
                $order->tracking_number, //Tracking Number
                $order->date_delivered ? Carbon::parse($order->date_delivered)->format('Y-m-d') : '', //Date Delivered
                isset($deliveryStatusMap[$order->delivery_status]) ? $deliveryStatusMap[$order->delivery_status] : '', //Status
                '' //Customer Type
            ];
        }
        
        $col = 'A';
        foreach ($data as $value) {
            $sheet->setCellValue($col . $row, $value);
            $col++;
        }
    }

    //creating data for single product order
    private function writeOrderRow($sheet, $row, $order, $orderProduct){
        //calculates COGS percentage
        $price = $orderProduct ? $orderProduct->price : 0;
        $cogs = $orderProduct ? $orderProduct->cogs : 0;
        $percentCogs = ($price > 0) ? (($cogs / $price) * 100) : 0;
        
        //maps delivery status
        $deliveryStatusMap = [
            0 => 'Pending',
            1 => 'Shipped',
            2 => 'Delivered',
            3 => 'RTS',
            4 => 'Returned',
            5 => 'Out For Delivery',
            6 => 'Cancelled'
        ];
        
        //Maps payment status
        $paymentStatus = $order->mark_as_paid == 1 ? 'Paid' : 'Unpaid';
        
        $data = [
            $order->admin ? $order->admin->fullName() : '', //sales admin
            $order->order_number, //Order #
            $order->order_date ? Carbon::parse($order->order_date)->format('Y-m-d') : '',//order date
            $order->customers ? $order->customers->name : '',//customer name
            $order->customers ? $order->customers->number : '', //customer contact number
            $order->address, //complete address
            $orderProduct ? $orderProduct->quantity : '', //quantity
            $orderProduct && $orderProduct->product ? $orderProduct->product->name : '', //Product
            $orderProduct ? $orderProduct->price : '', //Price
            $orderProduct ? $orderProduct->cogs : '', //COGS
            ($percentCogs > 0) ? ($percentCogs / 100) : null, //%COGS - convert to decimal for formatting, use null for zero
            '', //Sales Invoice
            $order->attribution ? $order->attribution->name : '', //Order (Attribution)
            $order->courier ? $order->courier->name : '', //Courier
            $order->attribution ? $order->attribution->name : '', //Attribution
            $order->mode_of_payment ? $order->mode_of_payment->name : '', //Mode of Payment
            $paymentStatus, //Payment Status
            $order->target_delivery_date ? Carbon::parse($order->target_delivery_date)->format('Y-m-d') : '', //Target Delivery/ Pick up Date
            '', //Packing Status
            $order->dispatch_date ? Carbon::parse($order->dispatch_date)->format('Y-m-d') : '', //Dispatch Date
            $order->tracking_number, //Tracking Number
            $order->date_delivered ? Carbon::parse($order->date_delivered)->format('Y-m-d') : '', //Date Delivered
            isset($deliveryStatusMap[$order->delivery_status]) ? $deliveryStatusMap[$order->delivery_status] : '', //Status
            '' //Customer Type
        ];
        
        $col = 'A';
        foreach ($data as $value) {
            $sheet->setCellValue($col . $row, $value);
            $col++;
        }
    }

    /** END OF EXPORTING ORDER DATA */



    /**BELOW FUNCTIONS IS FOR IMPORT FEATURE IN ORDERS */

    //Preparing the importation 
    // public function import(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'sheet_url' => 'nullable|url',
    //         'file' => 'nullable|file|mimes:xlsx,xls,csv',
    //         'first_row_header' => 'required|in:0,1' //Changed from 'boolean' to 'in:0,1'
    //     ]);

    //     if($validator->fails()){
    //         return response()->json(['message' => $validator->errors()->first()], 422);
    //     }

    //     if(!$request->sheet_url && !$request->file){
    //         return response()->json(['message' => 'Please provide either a Google Sheet URL or upload a file'], 422);
    //     }

    //     try{
    //         if($request->file){
    //             $data = $this->parseExcelFile($request->file, $request->first_row_header);
    //         }else{
    //             return response()->json(['message' => 'Google Sheets integration required additional setup. Please upload an Excel File instead'], 400);
    //         }

    //         $importedCount = $this->processImportData($data);

    //         return response()->json([
    //             'message' => `Successfully imported {$importedCount} orders`,
    //             'imported_count' => $importedCount
    //         ]);
    //     } catch(\Exception $e){
    //         return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 500);
    //     }    
    // }

        //Preparing the importation 
    public function import(Request $request){
        $validator = Validator::make($request->all(), [
            'sheet_url' => 'nullable|url',
            'file' => 'nullable|file|mimes:xlsx,xls,csv',
            'first_row_header' => 'required|in:0,1' //Changed from 'boolean' to 'in:0,1'
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        if(!$request->sheet_url && !$request->file){
            return response()->json(['message' => 'Please provide either a Google Sheet URL or upload a file'], 422);
        }

        try{
            if($request->file){
                $data = $this->parseExcelFile($request->file, $request->first_row_header);
            }else{
                return response()->json(['message' => 'Google Sheets integration required additional setup. Please upload an Excel File instead'], 400);
            }

            // Return the response from processImportData instead of just the count
            return $this->processImportData($data);

        } catch(\Exception $e){
            return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 500);
        }    
    }

    //Parsing imported sheet file and checks for sheet name ONLINE TEAM SALES
    private function parseExcelFile($file, $firstRowHeader){

        $spreadsheet = IOFactory::load($file->getPathname());

        if(!$spreadsheet->sheetNameExists('ONLINE TEAM SALES')){
            throw new \Exception('Sheet "ONLINE TEAM SALES" not found in the workbook');
        }

        $worksheet = $spreadsheet->getSheetByName('ONLINE TEAM SALES');
        $rows = $worksheet->toArray();

        if($firstRowHeader && !empty($rows)){
            array_shift($rows);//removes header row
        }

        return $rows;
    }

    //Processing imported excel data to system, THE MAIN Function BACKUP
    private function processImportData($data){
        $importedCount = 0;
        $skippedOrders = [];
        $processedOrders = [];
        $insufficientStockProducts = [];
        $noStockProducts = [];

        DB::beginTransaction();

        try{
            foreach($data as $index => $row){
                //Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $orderNumber = $row[1] ?? null;

                if(empty($orderNumber)){
                    continue;
                }

                //PARSING OF ORDER PRODUCTS SECTION
                //Parse products from the product column
                $productInfo = $row[7] ?? null; // product
                
                //Get quantity from spreadsheet column 6
                $spreadsheetQuantity = (int)($row[6] ?? 0); // quantity from spreadsheet
                
                //Properly parse price and COGS values (remove commas, currency symbols, etc.)
                $priceStr = $row[8] ?? '0';
                $cogsStr = $row[9] ?? '0';
                
                //Clean and convert price and COGS (these are TOTAL values from spreadsheet)
                $totalPrice = $this->parseNumericValue($priceStr);
                $totalCogs = $this->parseNumericValue($cogsStr);
                
                if(!$productInfo){
                    //Skip if no product
                    $skippedOrders[] = [
                        'order_number' => $orderNumber,
                        'reason' => 'No product information',
                        'row' => $index + 1
                    ];
                    continue;
                }

                //Parse product names (handle combinations like "EB3A + SP200" or "3*AC200MAX 6*PV350")
                $productEntries = $this->parseProductNames($productInfo);
                $products = [];
                $missingProducts = [];
                $totalQuantity = 0;
                
                //Find all products before importing, if null, specific order will not push through
                foreach($productEntries as $entry) {
                    // Extract quantity from entries like "3*AC200MAX" or "2*AC300"
                    $quantity = 1; // Default quantity
                    $productName = $entry;
                    
                    //Check if entry follows quantity*product format
                    if (preg_match('/^(\d+)\*(.+)$/', $entry, $matches)) {
                        $quantity = (int)$matches[1];
                        $productName = $matches[2];
                    }
                    
                    $product = Product::where('name', 'like', "%{$productName}%")->first();
                    if($product) {
                        //Check if product has sufficient stock
                        $availableStock = $this->getAvailableStock($product->id);
                        if ($availableStock < $quantity) {
                            $missingProducts[] = $productName . " (Insufficient stock: {$availableStock} available, {$quantity} required)";

                            //insufficient stock products list for display
                            $insufficientStockProducts[] = [
                                'product_name' => $productName,
                                'available_stock' => $availableStock,
                                'required_stock' => $quantity
                            ];

                            //no stock products list if available stock is 0
                            if ($availableStock == 0) {
                                $noStockProducts[] = $productName;
                            }

                            continue;
                        }
                        
                        $products[] = [
                            'product' => $product,
                            'quantity' => $quantity,
                            'entry' => $entry
                        ];
                        $totalQuantity += $quantity;
                    } else {
                        $missingProducts[] = $productName;
                    }
                }
                
                //If any products are missing or have insufficient stock, skip this order
                if(!empty($missingProducts)) {
                    $skippedOrders[] = [
                        'order_number' => $orderNumber,
                        'missing_products' => $missingProducts,
                        'row' => $index + 1
                    ];
                    continue;
                }
                //END OF PARSING OF ORDER PRODUCTS SECTION

                //Process datas
                $orderDate = $this->parseDate($row[2] ?? null); //date created or order date
                $targetDeliveryDate = $this->parseDate($row[17] ?? null); //target delivery date
                $dispatchDate = $this->parseDate($row[19] ?? null); //dispatch date
                $dateDelivered = $this->parseDate($row[21] ?? null); //date delivered
                $deliveryStatus = $this->mapDeliveryStatus($row[22] ?? 'pending'); //Status
                $markAsPaid = ($row[16] ?? 'Unpaid') === 'Paid' ? 1 : 0;
                $adminId = $this->getAdminId($row[0] ?? null); //sales admin
                
                //Fetch or create customer
                $customerName = $row[3] ?? null; //Customer Name
                $contactNumber = $row[4] ?? null; //Contact #
                $address = $row[5] ?? null; //Address
                $customerId = $this->getCustomerId($customerName, $contactNumber, null, $address);

                $orderData = [
                    'customer_id' => $customerId,
                    'order_number' => $orderNumber,
                    'contact_number' => $contactNumber,
                    'email' => null,
                    'address' => $address,//complete_address
                    'order_date' => $orderDate,//order_date
                    'delivery_status' => $deliveryStatus,//delivery_statuss
                    'target_delivery_date' => $targetDeliveryDate,//target_delivery_date
                    'dispatch_date' => $dispatchDate,//dispatch_date
                    'date_delivered' => $dateDelivered,//date_delivered
                    'tracking_number' => $row[20] ?? null, //Tracking Number
                    'mark_as_paid' => $markAsPaid,//status
                    'courier_id' => $this->getCourierId($row[13] ?? null), //Courier
                    'attribution_id' => $this->getAttributionId($row[14] ?? null), //Attribution
                    'mode_of_payment_id' => $this->getModeOfPaymentId($row[15] ?? null), //Mode of Payment
                    'notes' => $row[11] ?? null, //Sales Invoice
                    'admin_id' => $adminId, //Sales (Admin Assignment)
                ];

                //Create or update orders
                $order = Order::updateOrCreate(
                    ['order_number' => $orderNumber],
                    array_filter($orderData)
                );

                //Calculate total product prices based on actual product prices
                $totalProductPrice = 0;
                $productsWithPrices = [];
                
                foreach($products as $productEntry) {
                    $product = $productEntry['product'];
                    $productQuantity = $productEntry['quantity'];
                    
                    //Get actual product price from database
                    $actualProductPrice = $product->price ?? 0;
                    $totalProductItemPrice = $actualProductPrice * $productQuantity;
                    
                    $productsWithPrices[] = [
                        'product' => $product,
                        'quantity' => $productQuantity,
                        'entry' => $productEntry['entry'],
                        'actual_price' => $actualProductPrice,
                        'total_item_price' => $totalProductItemPrice
                    ];
                    
                    $totalProductPrice += $totalProductItemPrice;
                }

                //Calculate total discount only if prices don't match
                $totalDiscount = 0;
                $hasDiscount = false;
                
                //only apply discount  if total product price doesn't match spreadsheet price
                if (abs($totalProductPrice - $totalPrice) > 0.01) {
                    $totalDiscount = $totalProductPrice - $totalPrice;
                    $totalDiscount = $totalDiscount > 0 ? $totalDiscount : 0;
                    $hasDiscount = $totalDiscount > 0;
                }

                //STORE EXACT VALUES FROM SPREADSHEET WITH DISCOUNTS
                $totalProducts = count($productsWithPrices);

                if ($totalProducts == 1) {
                    //Single product(STORE EXACT VALUES FROM SPREADSHEET)
                    $productEntry = $productsWithPrices[0];
                    $product = $productEntry['product'];
                    $actualProductPrice = $productEntry['actual_price'];
                    $productQuantity = $productEntry['quantity'];
                    
                    //Get actual COGS from PurchaseOrder
                    $actualCogsPerUnit = $this->getProductCogs($product->id);
                    $totalActualCogs = $actualCogsPerUnit * $productQuantity; //TOTAL COGS

                    //Calculate discount for single product only if there's a mismatch
                    $productDiscount = $hasDiscount ? $totalDiscount : 0;
                    
                    //Use actual product price from database for unit price
                    $unitPrice = $actualProductPrice;
                    $unitCogs = $totalActualCogs; //STORE TOTAL COGS FOR ALL QUANTITIES
                    $unitDiscount = $productQuantity > 0 ? $productDiscount / $productQuantity : $productDiscount;
                    
                    OrderProduct::updateOrCreate(
                        [
                            'order_id' => $order->id,
                            'product_id' => $product->id
                        ],
                        [
                            'quantity' => $productQuantity,
                            'price' => $unitPrice, //USE ACTUAL PRODUCT PRICE FROM DATABASE
                            'discount' => $unitDiscount, //STORE CALCULATED DISCOUNT PER UNIT ONLY IF APPLICABLE
                            'cogs' => $unitCogs //STORE TOTAL COGS FOR ALL QUANTITIES
                        ]
                    );
                    
                    //Decrement stock for single product
                    $this->decrementProductStock($product->id, $productQuantity);
                } else {
                    //Multiple products with explicit quantities (like "6*AC180P 1*AC2P")
                    //For each product, store the portion of total COGS based on quantity proportion
                    foreach($productsWithPrices as $productEntry) {
                        $product = $productEntry['product'];
                        $productQuantity = $productEntry['quantity'];
                        $actualProductPrice = $productEntry['actual_price'];
                        $totalItemPrice = $productEntry['total_item_price'];
                        
                        //Get actual COGS from PurchaseOrder
                        $actualCogsPerUnit = $this->getProductCogs($product->id);
                        $totalActualCogs = $actualCogsPerUnit * $productQuantity; //TOTAL COGS for all quantities
                        
                        //Calculate discount proportion for this product only if there's a mismatch
                        $productTotalDiscount = 0;
                        if ($hasDiscount && $totalProductPrice > 0) {
                            $productProportion = $totalItemPrice / $totalProductPrice;
                            $productTotalDiscount = $totalDiscount * $productProportion;
                        }
                        
                        //Distribute total values according to product quantity proportion
                        $productProportionByQuantity = $totalQuantity > 0 ? $productQuantity / $totalQuantity : 0;
                        $productTotalPrice = $totalPrice * $productProportionByQuantity;
                        
                        //Use actual product price from database for unit price
                        $unitPrice = $actualProductPrice;
                        $unitCogs = $totalActualCogs; //STORE TOTAL COGS FOR ALL QUANTITIES
                        $unitDiscount = $productQuantity > 0 ? $productTotalDiscount / $productQuantity : 0;
                        
                        OrderProduct::updateOrCreate(
                            [
                                'order_id' => $order->id,
                                'product_id' => $product->id
                            ],
                            [
                                'quantity' => $productQuantity,
                                'price' => $unitPrice, //USE ACTUAL PRODUCT PRICE FROM DATABASE
                                'discount' => $unitDiscount, //STORE CALCULATED DISCOUNT PER UNIT ONLY IF APPLICABLE
                                'cogs' => $unitCogs //STORE TOTAL COGS FOR ALL QUANTITIES
                            ]
                        );
                        
                        //Decrement stock for each product
                        $this->decrementProductStock($product->id, $productQuantity);
                    }
                }

                //Create order history entry for imported order
                $this->createOrderHistory($order, $adminId, $row);

                $importedCount++;
                $processedOrders[] = $orderNumber;
            }

            
            //FETCHES products with no stock BEFORE committing the transaction
            if (!empty($noStockProducts)) {
                DB::rollback();
                
                $noStockList = implode('\n', $noStockProducts);
                
                return response()->json([
                    'message' => "Import failed due to products with no stock available. No stocks left for products:\n{$noStockList}",
                    'no_stock_products' => $noStockProducts
                ], 422);
            }

            DB::commit();
            
            //response message
            $message = "Successfully imported {$importedCount} orders";
            if (!empty($skippedOrders)) {
                $message .= ". Skipped " . count($skippedOrders) . " orders due to missing products or insufficient stock.";
            }
            
            return response()->json([
                'message' => $message,
                'imported_count' => $importedCount,
                'skipped_orders' => $skippedOrders,
                'processed_orders' => $processedOrders,
                'insufficient_stock_products' => $insufficientStockProducts
            ]);
            
            return response()->json([
                'message' => $message,
                'imported_count' => $importedCount,
                'skipped_orders' => $skippedOrders,
                'processed_orders' => $processedOrders
            ]);
            
        } catch (\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
    

    //fetches available products
    private function getAvailableStock($productId){
        //Sum all stocks_left from purchase_orders for this product
        return PurchaseOrder::where('product_id', $productId)
                          ->sum('stocks_left');
    }

    //fetches actual COGS from PurchaseOrder column (distributor_price)
    private function getProductCogs($productId) {
        //fetches the latest purchase order's distributor_price for this product
        $purchaseOrder = PurchaseOrder::where('product_id', $productId)
                                     ->where('stocks_left', '>', 0)
                                     ->orderBy('created_at', 'desc')
                                     ->first();
        
        return $purchaseOrder ? $purchaseOrder->distributor_price : 0;
    }

    //Decrement product stock from purchase orders
    private function decrementProductStock($productId, $quantity) {
        //Get purchase orders with available stock, ordered by creation date
        $purchaseOrders = PurchaseOrder::where('product_id', $productId)
                                     ->where('stocks_left', '>', 0)
                                     ->orderBy('created_at', 'asc')
                                     ->get();
        
        $remainingQuantity = $quantity;
        
        foreach($purchaseOrders as $purchaseOrder) {
            if($remainingQuantity <= 0) {
                break;
            }
            
            $availableStock = $purchaseOrder->stocks_left;
            
            if($availableStock >= $remainingQuantity) {
                //This purchase order has enough stock
                $purchaseOrder->decrement('stocks_left', $remainingQuantity);
                $remainingQuantity = 0;
            } else {
                //Use all stock from this purchase order
                $purchaseOrder->stocks_left = 0;
                $purchaseOrder->save();
                $remainingQuantity -= $availableStock;
            }
        }
        
        if($remainingQuantity > 0) {
            throw new \Exception("Insufficient stock for product ID: {$productId}. Remaining: {$remainingQuantity}");
        }
    }


    //Parsing numerical values 
    private function parseNumericValue($value) {
        if (is_null($value) || $value === '') {
            return 0;
        }
        
        //convert to string and clean it
        $valueStr = (string)$value;
        
        //remove currency symbols, commas, and whitespace
        $cleanValue = preg_replace('/[₱$,\\s]/', '', $valueStr);
        
        //convert to float
        return (float)$cleanValue;
    }

    //Created Order History per Order, only create if not exists
    private function createOrderHistory($order, $adminId, $row){
        $salesName = $row[0] ?? 'Unknown';
        
        //checking if order history already exists for this order
        $existingHistory = OrderHistory::where('order_id', $order->id)->first();
        
        //only create if no history exists for this order
        if (!$existingHistory) {
            $description = "Order Imported";
            $notes = "Order Imported";

            $currentlyLoggedAdmin = $adminId ?? auth()->guard('admins')->id();

            OrderHistory::create([
                'order_id' => $order->id,
                'description' => $description,
                'notes' => $notes,
                'admin_id' => $currentlyLoggedAdmin
            ]);
        }
    }

    //Parse product names from combined strings
    private function parseProductNames($productString) {
        if (empty($productString)) {
            return [];
        }
        
        //Clean the string
        $productString = trim($productString);
        
        //Handle combinations separated by + (highest priority)
        if (strpos($productString, '+') !== false) {
            $parts = explode('+', $productString);
            $products = [];
            foreach($parts as $part) {
                $cleanPart = trim($part);
                if(!empty($cleanPart)) {
                    $products[] = $cleanPart;
                }
            }
            return $products;
        }
        
        //Handle space-separated products (like "EB3A SP200")
        //Only split if there are exactly 2 parts and both are reasonable product names
        if (strpos($productString, ' ') !== false) {
            $parts = explode(' ', $productString);
            //If exactly 2 parts, treat as separate products
            if (count($parts) == 2) {
                $products = [];
                foreach($parts as $part) {
                    $cleanPart = trim($part);
                    if(!empty($cleanPart)) {
                        $products[] = $cleanPart;
                    }
                }
                //Only return as separate products if we have 2 clean parts
                if (count($products) == 2) {
                    return $products;
                }
            }
        }
        
        //Handle other separators (&, "and", "AND")
        $separators = ['&', ' and ', ' AND '];
        foreach($separators as $separator) {
            if(strpos($productString, $separator) !== false) {
                $parts = explode($separator, $productString);
                $products = [];
                foreach($parts as $part) {
                    $cleanPart = trim($part);
                    if(!empty($cleanPart)) {
                        $products[] = $cleanPart;
                    }
                }
                return $products;
            }
        }
        
        //Handle quantity*product format (like "3*AC200MAX" or "2*AC300")
        //This is a new pattern we need to support
        if (preg_match('/^\d+\*/', $productString)) {
            // This is a single product with quantity prefix
            return [$productString];
        }
        
        //Handle multiple products with quantity prefixes separated by spaces
        //(like "3*AC200MAX 6*PV350 3*D050S")
        if (strpos($productString, '*') !== false && preg_match('/\d+\*[A-Za-z0-9]+/', $productString)) {
            $parts = explode(' ', $productString);
            $products = [];
            foreach($parts as $part) {
                $cleanPart = trim($part);
                if(!empty($cleanPart) && preg_match('/^\d+\*[A-Za-z0-9]+/', $cleanPart)) {
                    $products[] = $cleanPart;
                }
            }
            if (!empty($products)) {
                return $products;
            }
        }
        
        //If no separators found, treat as single product
        return [trim($productString)];
    }
        
    //date parsing
    private function parseDate($dateString)
    {
        if (!$dateString) return null;
        
        try {
            //Handle Excel date serial numbers
            if (is_numeric($dateString)) {
                //Check if it's a reasonable serial number (Excel dates typically range from 1 to ~100000)
                if ($dateString > 0 && $dateString < 100000) {
                    $date = PhpSpreadsheetDate::excelToDateTimeObject($dateString);
                    if ($date && $date->format('Y') > 1900 && $date->format('Y') < 2100) {
                        return $date->format('Y-m-d');
                    }
                }
                return null;
            }
            
            //Handle string date values
            $dateString = trim($dateString);
            
            //Try common date formats
            $formats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'Y/m/d', 'y-m-d', 'm-d-Y', 'd-m-Y'];
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date && $this->isValidYear($date)) {
                    return $date->format('Y-m-d');
                }
            }
            
            //Try parsing with strtotime as fallback
            $timestamp = strtotime($dateString);
            if ($timestamp !== false) {
                $date = new \DateTime();
                $date->setTimestamp($timestamp);
                if ($this->isValidYear($date)) {
                    return $date->format('Y-m-d');
                }
            }
            
            return null;
        } catch (\Exception $e) {
            //Log the error for debugging
            \Log::debug("Date parsing error for value: " . $dateString . " - " . $e->getMessage());
            return null;
        }
    }

    //Helper method to validate reasonable years
    private function isValidYear($date)
    {
        $year = (int)$date->format('Y');
        return $year > 1900 && $year < 2100;
    }

    //mapping delivery status
    private function mapDeliveryStatus($status)
    {
        $status = strtolower(trim($status));
        
        $statusMap = [
            'pending' => 0,
            'shipped' => 1,
            'delivered' => 2,
            'rts' => 3,
            'returned' => 4,
            'out for delivery' => 5,
            'cancelled' => 6,
            'confirmed' => 1, 
            'in transit' => 1 
        ];
        
        return isset($statusMap[$status]) ? $statusMap[$status] : 0; //0 (Pending)
    }

    //fetchesCustomer ID
    private function getCustomerId($customerName, $contactNumber = null, $email = null, $address = null){
        if(empty($customerName)){
            return null;
        }

        //Try to find existing customer by name
        $customer = Customer::where('name', 'like', "%{$customerName}%")->first();

        if($customer){
            return $customer->id;
        }

        //Create new customer if not found
        $customer = Customer::create([
            'name' => $customerName,
            'number' => $contactNumber,
            'email' => $email,
        ]);
        
        return $customer->id;
    }
    
    //fetches adminID this is for the Sales Admin Assign for each Order
    private function getAdminId($salesName)
    {
        if (empty($salesName)) {
            return null;
        }
        
        //Try to find admin by first name or full name
        $admin = Admin::where('first_name', 'like', "%{$salesName}%")
                      ->orWhere('last_name', 'like', "%{$salesName}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$salesName}%"])
                      ->first();
        
        return $admin ? $admin->id : null;
    }

    //fetches courierID
    private function getCourierId($courierName)
    {
        if (empty($courierName)) {
            return null;
        }
        
        //Try to find existing courier
        $courier = Courier::where('name', 'like', "%{$courierName}%")->first();
        
        if ($courier) {
            return $courier->id;
        }
        
        //Create new courier if needed
        $courier = Courier::create([
            'name' => $courierName,
            'is_active' => 1
        ]);
        
        return $courier->id;
    }

    //fetches attributionID
    private function getAttributionId($attributionName)
    {
        if (empty($attributionName)) {
            return null;
        }
        
        //Try to find existing attribution
        $attribution = Attribution::where('name', 'like', "%{$attributionName}%")->first();
        
        if ($attribution) {
            return $attribution->id;
        }
        
        //Create new attribution if needed
        $attribution = Attribution::create([
            'name' => $attributionName
        ]);
        
        return $attribution->id;
    }
    
    //fetches modeofpaymentID
    private function getModeOfPaymentId($modeOfPaymentName)
    {
        if (empty($modeOfPaymentName)) {
            return null;
        }
        
        //Try to find existing mode of payment
        $modeOfPayment = ModeOfPayment::where('name', 'like', "%{$modeOfPaymentName}%")->first();
        
        if ($modeOfPayment) {
            return $modeOfPayment->id;
        }
        
        //Create new mode of payment if needed
        $modeOfPayment = ModeOfPayment::create([
            'name' => $modeOfPaymentName
        ]);
        
        return $modeOfPayment->id;
    }

    /**END OF FUNCTIONS IS FOR IMPORT FEATURE IN ORDERS */


}

