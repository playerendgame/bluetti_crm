<?php

namespace App\Http\Controllers\Ajax\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\ActivityLogs;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function list(Request $request)
    {
        $column = $request->column;
        if (strcmp($column, "purchase_date_s") == 0) {
            $column = "purchase_date";
        } else if (strcmp($column, "ref_code_s") == 0) {
            $column = "ref_code";
        }

        $input = $request->all();

        $query = Purchase::orderBy($column, $request->order);

        if (isset($input["keyword"])) {
            $query->where("ref_code", "like", "%" . $input["keyword"] . '%');
        }

        $purchases = $query->paginate($request->per_page);

        foreach ($purchases as $purchase) {
            $purchase_orders = PurchaseOrder::where('purchase_id', '=', $purchase->id)->get();
            $purchase->purchase_date_s = Carbon::parse($purchase->purchase_date)->format("F j, Y");
            $purchase->ref_code_s = $purchase->ref_code != null ? $purchase->ref_code : "";
            $purchase->quantity = $purchase_orders->sum(function ($purchase_order) {
                return $purchase_order->quantity;
            });

            $total_cost = $purchase_orders->sum(function ($purchase_order) {
                return $purchase_order->quantity * $purchase_order->distributor_price;
            });

            $purchase->stocks_left = $purchase_orders->sum(function ($purchase_order) {
                return $purchase_order->stocks_left;
            });

            $purchase->total_amount = "₱ " . number_format($total_cost, 2);
        }

        return array("success" => true, "message" => "", "data" => $purchases);
    }



    public function create(Request $request)
    {
        $validation_rules = [
            'purchase_date' => 'required',
            'purchase_orders' => 'required|array',
            'purchase_orders.*.product_id' => 'required',
            'purchase_orders.*.quantity' => 'required|numeric|min:1',
            'purchase_orders.*.distributor_price' => 'required|numeric',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $prefix = "PO";

        // Retrieve the latest purchase reference number
        $latestPurchase = Purchase::orderBy('series', 'desc')->first();

        // Check if there is any existing record
        if ($latestPurchase) {
            // If there are existing records, increment the series
            $series = $latestPurchase->series + 1;
        } else {
            // If there are no existing records, start the series at 0001
            $series = 1;
        }

        $year = Carbon::now()->format('y');
        $month = Carbon::now()->format('m');
        $day = Carbon::now()->format('d');

        // Format the reference code
        $ref_code = $prefix . $year . $month . $day . "-" . sprintf('%04d', $series);

        $purchase = new Purchase();
        $purchase->purchase_date = $input["purchase_date"];
        $purchase->series = $series;
        $purchase->ref_code = $ref_code;
        if ($purchase->save()) {
            foreach ($input["purchase_orders"] as $purchase_order) {
                $newPurchaseOrder = new PurchaseOrder();
                $newPurchaseOrder->purchase_id = $purchase->id;
                $newPurchaseOrder->quantity = $purchase_order["quantity"];
                $newPurchaseOrder->product_id = $purchase_order["product_id"];
                $newPurchaseOrder->distributor_price = $purchase_order["distributor_price"];
                $newPurchaseOrder->stocks_left = $newPurchaseOrder->quantity;
                $newPurchaseOrder->save();
            }
        }

        $this->logActivity('purchase', $newPurchaseOrder, $purchase);

        return array('success' => true, "message" => 'Purchase Order Succesfully Created!', 'data' => null);
    }


     //For the Purchase Order activity log create
     private function logActivity($activity, $newPurchaseOrder, $purchase){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Purchase Order With Series ' . $purchase->ref_code; 

        ActivityLogs::create([
            'source' => 'Finance',
            'admin_id' => $admin_id,
            'name' => $purchase->ref_code,
            'activity' => $activity
        ]);

    }

}
