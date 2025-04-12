<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ModeOfPayment;
use App\Models\OrderPaymentMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ModeOfPaymentOrdersController extends Controller
{
    
    public function getModeOfPaymentOrders(Request $request){

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $paymentMethods = ModeOfPayment::withCount(['orders' => function ($query) use ($startDate, $endDate){

            $query->whereBetween('order_date', [$startDate, $endDate]);

        }])->get();

        $totalOrders = Order::whereBetween('order_date', [$startDate, $endDate])->count();

        $paymentMethods->each(function ($paymentMethod) use ($totalOrders) {
            $paymentMethod->count = $paymentMethod->orders_count;
            $paymentMethod->percentage = $paymentMethod->count > 0 ? number_format(($paymentMethod->orders_count / $totalOrders) * 100, 2) . "%" : "0%";
        });

        return response()->json($paymentMethods);

    }

    // Rejected Code
    // public function getModeOfPaymentOrders(Request $request){
    //     $startDate = Carbon::parse($request->input('start_date'))->startOfDay(); // Adjust start date to 00:00:00
    //     $endDate = Carbon::parse($request->input('end_date'))->endOfDay(); // Adjust end date to 23:59:59
    //     $paymentMethods = ModeOfPayment::get();
    //     $totalOrders = OrderPaymentMethod::whereBetween('created_at', [$startDate, $endDate])->count();
    //     $paymentMethods->each(function ($paymentMethod) use ($startDate, $endDate, $totalOrders) {
    //         $paymentMethod->count = OrderPaymentMethod::where('payment_method_id', $paymentMethod->id)
    //             ->whereBetween('created_at', [$startDate, $endDate])
    //             ->count();
    //         $paymentMethod->percentage = $paymentMethod->count > 0 ? number_format(($paymentMethod->count / $totalOrders) * 100, 2) . "%" : "0%";
    //     });
    //     return response()->json($paymentMethods);
    // }
    
    
}
