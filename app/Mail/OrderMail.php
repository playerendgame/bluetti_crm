<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderProduct;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order_from, $order_to, $auth, $region, $province, $city, $admin, $attribution, $courier, $delivery_status,
    $dispatch_from, $dispatch_to, $delivered_from, $delivered_to, $payment_status, $date_paid_from, $date_paid_to, $target_delivery_from, $target_delivery_to,
    $mop_id)
    {
        $this->region = $region;
        $this->province = $province;
        $this->city = $city;
        $this->admin = $admin;
        $this->attribution = $attribution;
        $this->courier = $courier;
        $this->delivery_status = $delivery_status;
        $this->dispatch_from = $dispatch_from;
        $this->dispatch_to = $dispatch_to;
        $this->delivered_from = $delivered_from;
        $this->delivered_to = $delivered_to;
        $this->payment_status = $payment_status;
        $this->date_paid_from = $date_paid_from;
        $this->date_paid_to = $date_paid_to;
        $this->target_delivery_from = $target_delivery_from;
        $this->target_delivery_to = $target_delivery_to;
        $this->mop_id = $mop_id;
        $this->order_from = $order_from;
        $this->order_to = $order_to;
        $this->auth = $auth;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $order_from = $this->order_from;
        $order_to = $this->order_to;
        $region = $this->region;
        $province = $this->province;
        $city = $this->city;
        $admin = $this->admin;
        $attribution = $this->attribution;
        $courier = $this->courier;
        $delivery_status = $this->delivery_status;
        $dispatch_from = $this->dispatch_from;
        $dispatch_to = $this->dispatch_to;
        $delivered_from = $this->delivered_from;
        $delivered_to = $this->delivered_to;
        $payment_status = $this->payment_status;
        $date_paid_from = $this->date_paid_from;
        $date_paid_to = $this->date_paid_to;
        $target_delivery_from = $this->target_delivery_from;
        $target_delivery_to = $this->target_delivery_to;
        $mop_id = $this->mop_id;
        
        $query = Order::orderBy('created_at', 'asc');

        if ($order_from && $order_to) {
            $query->whereBetween('order_date', [Carbon::parse($order_from)->startOfDay(), Carbon::parse($order_to)->endOfDay()]);
        }

        if ($region != 0) {
            $query->where('region_id', '=', $region);
        }

        if ($province != 0) {
            $query->where('province_id', '=', $province);
        }

        if ($city != 0) {
            $query->where('city_id', '=', $city);
        }

        if ($admin != 0) {
            $query->where('admin_id', '=', $admin);
        }

        if ($attribution != 0) {
            $query->where('attribution_id', '=', $attribution);
        }

        if ($courier != 0) {
            $query->where("courier_id", '=', $courier);
        }

        if ($delivery_status != 99) {
            $query->where('delivery_status', '=', $delivery_status);
        }

        if ($dispatch_from && $dispatch_to) {
            $query->whereBetween('dispatch_date', [Carbon::parse($dispatch_from)->startOfDay(), Carbon::parse($dispatch_to)->endOfDay()]);
        }

        if ($delivered_from && $delivered_to) {
            $query->whereBetween('date_delivered', [Carbon::parse($dispatch_from)->startOfDay(), Carbon::parse($dispatch_to)->endOfDay()]);
        }

        if ($payment_status != 99) {
            $query->where("mark_as_paid", '=', $payment_status);
        }

        if ($date_paid_from && $date_paid_to) {
            $query->whereBetween('date_paid', [Carbon::parse($date_paid_from)->startOfDay(), Carbon::parse($date_paid_to)->endOfDay()]);
        }
        
        if ($target_delivery_from && $target_delivery_to) {
            $query->whereBetween('target_delivery_date', [Carbon::parse($target_delivery_from)->startOfDay(), Carbon::parse($target_delivery_to)->endOfDay()]);
        }

        if ($mop_id != 0) {
            $query->where('mode_of_payment_id', '=', $mop_id);
        }

        $orders = $query->get();

        if ($orders != null) {
            $excel_data = [array('Date Created', 'Order Date', 'Order Number', "Paid Status", "Date Paid", "Mode Of Payment", "Customer Name",
            "Region", "Province", "City", "Attribution", "Quantity", "Total Amount", "COGS", "% COGS", "Contact #", "Email", "Address", "Target Delivery Date",
            "Status", "Dispatch Date", "Returned Date", "Delivery Date", "Tracking #", "Courier", "Admin Assign")];
            foreach ($orders as $order) {
                $order_products = OrderProduct::where('order_id', '=', $order->id)->get();
                $order->region_name = $order->region_id != null ? $order->regions->name : "";
                $order->province_name = $order->province_id != null ? $order->provinces->name : "";
                $order->city_name = $order->city_id != null ? $order->cities->name : "";
                $order->customer_name = $order->customers->name;
                $order->mark_as_paid_s = $order->markAsPaid();
                $order->date_paid_s != null ? Carbon::parse($order->date_paid)->format("M j, Y") : "";
                $order->mode_of_payment_name = $order->mode_of_payment ? $order->mode_of_payment->name : "";
                $order->customer_name = $order->customers->name;
                $order->region_name = $order->region_id != null ? $order->regions->name : "";
                $order->province_name = $order->province_id != null ? $order->provinces->name : "";
                $order->city_name = $order->city_id != null ? $order->cities->name : "";
                $order->attribution_name = $order->attribution_id != null ? $order->attribution->name : "";
                $order->count_orders = $order_products->sum(function ($order_product) {
                    $total_orders = $order_product->price != 0 ? $order_product->quantity : 0;
                    return $total_orders;
                });
                $total_price = $order_products->sum(function ($order_product) {
                    return (($order_product->quantity * $order_product->price) - $order_product->discount);
                });
                $order->total_price = "₱ " . number_format($total_price, 2);
                $total_price_cogs = $order_products->sum(function ($order_product) {
                    return $order_product->cogs;
                });
                $order->cogs_s = "₱ " . number_format($total_price_cogs, 2);
                $order->percent_cogs = $total_price > 0 ? number_format((($total_price_cogs / $total_price) * 100), 2) . "%" : "0%";
                $order->target_delivery_date_s = $order->target_delivery_date != null ? Carbon::parse($order->target_delivery_date)->format("M j, Y") : "";
                $order->delivery_status_s = $order->deliveryStatusName();
                $order->dispatch_date_s = $order->dispatch_date != null ? Carbon::parse($order->dispatch_date)->format("M j, Y") : "";
                $order->returned_date_s = $order->returned_date != null ? Carbon::parse($order->returned_date)->format("M j, Y") : "";
                $order->date_delivered_s = $order->date_delivered != null ? Carbon::parse($order->date_delivered)->format("M j, Y") : "";
                $order->courier_name = $order->courier_id != null ? $order->courier->name : "";
                $order->admin_name = $order->admin_id != null ? $order->admin->fullName() : "";

                array_push($excel_data, array(
                    Carbon::parse($order->date_created)->format("M j, Y g:i A"),
                    Carbon::parse($order->order_date)->format("M j, Y"),
                    $order->order_number,
                    $order->mark_as_paid_s,
                    $order->date_paid_s,
                    $order->mode_of_payment_name,
                    $order->customer_name,
                    $order->region_name,
                    $order->province_name,
                    $order->city_name,
                    $order->attribution_name,
                    $order->count_orders,
                    $order->total_price,
                    $order->cogs_s,
                    $order->percent_cogs,
                    $order->contact_number,
                    $order->email,
                    $order->address,
                    $order->target_delivery_date_s,
                    $order->delivery_status_s,
                    $order->dispatch_date_s,
                    $order->returned_date_s,
                    $order->date_delivered_s,
                    $order->tracking_number,
                    $order->courier_name,
                    $order->admin_name,
                ));
            }
        }

        $filepath = 'storage/orders/export/' . Carbon::now()->timestamp . '.xlsx';
        $collection = new Collection($excel_data);
        $collection->storeExcel(
            $filepath,
            $disk = null,
            $writerType = null,
            $headings = false,
        );

        return $this->view('email.export-orders')->subject('Orders - ' . Carbon::now()->format("M j, Y"))->attach(storage_path('app/' . $filepath));
    }
}
