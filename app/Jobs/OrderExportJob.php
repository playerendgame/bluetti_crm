<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Auth;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;

class OrderExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order_from;
    protected $order_to;
    protected $auth;  // Add this property.
    protected $region;
    protected $province;
    protected $city;
    protected $admin;
    protected $attribution;
    protected $courier;
    protected $delivery_status;
    protected $dispatch_from;
    protected $dispatch_to;
    protected $delivered_from;
    protected $delivered_to;
    protected $payment_status;
    protected $date_paid_from;
    protected $date_paid_to;
    protected $target_delivery_from;
    protected $target_delivery_to;
    protected $mop_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_from, $order_to, $auth, $region, $province, $city, $admin, $attribution, $courier, $delivery_status,
    $dispatch_from, $dispatch_to, $delivered_from, $delivered_to, $payment_status, $date_paid_from, $date_paid_to, $target_delivery_from, $target_delivery_to,
    $mop_id)
    {
        $this->order_from = $order_from;
        $this->order_to = $order_to;
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
        $this->auth = $auth;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->auth)->send(new OrderMail($this->order_from, $this->order_to, $this->auth, $this->region, $this->province, $this->city,
        $this->admin, $this->attribution, $this->courier, $this->delivery_status, $this->dispatch_from, $this->dispatch_to, $this->delivered_from,
        $this->delivered_to, $this->payment_status, $this->date_paid_from, $this->date_paid_to, $this->target_delivery_from, $this->target_delivery_to,
        $this->mop_id));
    }
}