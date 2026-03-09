<?php

namespace App\Http\Controllers\Ajax\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;

class GoogleSheetController extends Controller
{
    public function appendCreateDataToGoogleSheet($order){
        /**GOOGLE SHEET DATA IMPLEMENTATION  */

        //load Order relations
        $order->load(['products', 'customers', 'mode_of_payment', 'courier']);

        $sheetService = new GoogleSheetService();

        $totalQuantity = 0;
        $totalAmount = 0;
        $totalCOGS = 0;
        $productNames = [];
        foreach ($order->products as $product) {
            $totalQuantity += $product->pivot->quantity;
            $productNames[] = $product->name;
            $totalAmount += $product->pivot->price * $product->pivot->quantity;
            $totalCOGS += $product->pivot->cogs * $product->pivot->quantity;
        }

        $percentCOGS = round(($totalCOGS / max($totalAmount, 1)) * 100, 2) . '%';
        $customerType = $order->customers ? ($order->customers->isNewCustomer() ? 'New' : 'Old') : 'Unknown';

        $orderData = [
            $order->admin->first_name . ' ' . $order->admin->last_name, // Sales Assigned
            $order->order_number, // Order #
            $order->order_date, // Date
            $order->customers->name, // Customer Name
            $order->contact_number, // Contact #
            $order->address, // Complete Address
            $totalQuantity, // Quantity
            implode("\n", $productNames), // Product
            $totalAmount, // Amount
            $totalCOGS, // COGS
            $percentCOGS, // %COGS
            '', // Sales Invoice
            $order->attribution?->name ?? '', // Attribution
            $order->courier->name ?? '', // Courier
            $order->attribution?->name ?? '', // Attribution
            $order->mode_of_payment?->name ?? '', // Mode of Payment
            $order->markAsPaid(), // Payment Status
            $order->target_delivery_date ?? '', // Target Delivery/ Pick up Date
            $order->packing_status ?? '', // Packing Status
            $order->dispatch_date ?? '', // Dispatch Date
            $order->tracking_number ?? '', // Tracking Number
            $order->date_delivered ?? '', // Date Delivered
            $order->deliveryStatusName(), // Status
            $customerType, // Customer Type
            $order->finance_status ?? '', // Finance Status
            $order->freebies ?? '', // Freebies
            $order->notes ?? '', // Remarks
            $order->returned_date ? 'RTS' : '', // RTS
        ];

        $sheetService->appendOrder($orderData);

        /**END GOOGLE SHEET DATA IMPLEMENTATION  */
    }


    public function appendDelStatusUpdateDataToGoogleSheet($order){
        
        $sheetService = new GoogleSheetService();

        //delivery status names
        $statusMap = [
            0 => 'Pending',
            1 => 'Shipped',
            2 => 'Delivered',
            3 => 'RTS',
            4 => 'Returned',
            5 => 'Out For Delivery',
            6 => 'Cancelled',
        ];

        $statusText = $statusMap[$order->delivery_status] ?? 'Pending';

        // update data matching Google Sheet columns
        $updates = [
            'Status' => $statusText,
            'Date Delivered' => $order->date_delivered ?? '',
            'Tracking Number' => $order->tracking_number ?? '',
            'Target Delivery/Pick up Date' => $order->target_delivery_date ?? '',
            'Packing Status' => $order->packing_status ?? '',
            'Dispatch Date' => $order->dispatch_date,
        ];

        // Call the update method by Order # in googlesheetservice.php
        $sheetService->updateOrderByOrderNumber($order->order_number, $updates);

    }


    public function appendPaymentDetailsUpdateToGoogleSheet($order){

        $sheetService = new GoogleSheetService();  

        //Update data matching Google Sheet columns
        $updates = [
            'Mode of Payment' => optional($order->mode_of_payment)->name,
            'Payment Status' => $order->markAsPaid(),
            'Payment Amount' => $order->payment_amount,
            'Payment Notes' => $order->payment_notes,
            'Date Paid' => $order->date_paid,
        ];

        //Call the update method by Order # in googlesheetservice.php
        $sheetService->updateOrderByOrderNumber($order->order_number, $updates);

    }
}
