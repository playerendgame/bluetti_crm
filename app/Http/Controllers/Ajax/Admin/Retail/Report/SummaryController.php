<?php

namespace App\Http\Controllers\Ajax\Admin\Retail\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailOrder;
use Carbon\Carbon;

class SummaryController extends Controller
{
    public function summary(Request $request)
    {
        $validation_rules = [
            "start_date" => 'required',
            "end_date" => 'required',
        ];

        $request->validate($validation_rules);
        $input = $request->all();

        $start_date = Carbon::parse($input["start_date"])->startOfDay();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();

        $query = RetailOrder::whereNotNull('id');

        if (isset($input["store"]) && $input["store"] != 0) {
            $query->where("store_id", '=', $input["store"]);
        }

        if (isset($input["branch"]) && $input["branch"] != 0) {
            $query->where('branch_id', '=', $input["branch"]);
        }

        $orders = $query->with('retail_order_products')->get();

        $tableArray = array();

        $totalGrossSales = 0;
        $totalCommissions = 0;
        $totalnetSales = 0;
        $totalCogs = 0;
        $totalNetProfit = 0;
        $totalOrders = 0;
        $totalDays = 0;
        $averageOrders = 0;
        $averageSales = 0;

        for ($i = 0; $i < $start_date->diffInMonths($end_date)+1; $i++) {
            $currentMonth = $start_date->copy()->addMonth($i);
            $startOfMonth = $currentMonth->copy()->startOfMonth();
            $endOfMonth = $currentMonth->copy()->endOfMonth();

            if ($currentMonth->isSameMonth($start_date)) {
                $count_days = $start_date->diffInDays($endOfMonth) + 1;
            } elseif ($currentMonth->isSameMonth($end_date)) {
                $count_days = $end_date->diffInDays($startOfMonth) + 1;
            } else {
                $count_days = $currentMonth->daysInMonth;
            }

            // Filter orders for the current month
            $filteredOrders = $orders->filter(function ($order) use ($startOfMonth, $endOfMonth) {
                return Carbon::parse($order->date_order)->between($startOfMonth, $endOfMonth);
            });

            // Calculate gross sales correctly
            $grossSales = $filteredOrders->map(function ($order) {
                return $order->retail_order_products->sum(function ($product) {
                    return $product->pivot->price * $product->pivot->quantity;
                });
            })->sum(); // Sum up all the orders

            // Commissions
            $commissions = $filteredOrders->map(function ($order) {
                return $order->retail_order_products->sum(function ($product) {
                    return (($product->pivot->price * $product->pivot->quantity) * $product->pivot->comms) / 100; 
                });
            })->sum();

            // Net sales
            $net_sales = $grossSales > 0 ? $grossSales - $commissions : 0;

            $cogs_amount = $filteredOrders->map(function ($order) {
                return $order->retail_order_products->sum(function ($product) {
                    return $product->pivot->cogs * $product->pivot->quantity; 
                });
            })->sum();

            $net_profit = $net_sales > 0 ? $net_sales - $cogs_amount : 0;

            $order_count = $filteredOrders->count();

            $daily_average_order = $order_count > 0 ? $order_count / $count_days : 0;

            $daily_average_sales = $grossSales > 0 ? $grossSales / $count_days : 0;

            $dataUnit = array(
                "month" => $currentMonth->format("F Y"),
                "gross_sales" => "₱ " . number_format($grossSales, 2),
                "commissions" => "₱ " . number_format($commissions, 2),
                "net_sales" => "₱ " . number_format($net_sales, 2),
                "cogs_amount" => "₱ " . number_format($cogs_amount, 2),
                "net_profit" => "₱ " . number_format($net_profit, 2),
                "order_count" => $order_count,
                "days" => $count_days,
                "daily_average_order" => number_format($daily_average_order, 2),
                "daily_average_sales" => "₱ " . number_format($daily_average_sales, 2),
            );

            $totalGrossSales += $grossSales;
            $totalCommissions += $commissions;
            $totalnetSales += $net_sales;
            $totalCogs += $cogs_amount;
            $totalNetProfit = $totalnetSales - $totalCogs;
            $totalOrders = $order_count;
            $totalDays += $count_days;

            $averageOrders = $totalOrders > 0 ? $totalOrders / $totalDays : 0;
            $averageSales = $totalGrossSales > 0 ? $totalGrossSales / $totalDays : 0;

            array_push($tableArray, $dataUnit);
        }
        
        usort($tableArray, function ($a, $b) {
            return strtotime($b['month']) - strtotime($a['month']);
        });

        $data["table"] = $tableArray;
        $data["total_gross_sales"] = "₱ " . number_format($totalGrossSales, 2);
        $data["total_commissions"] = "₱ " . number_format($totalCommissions, 2);
        $data["total_net_sales"] = "₱ " . number_format($totalnetSales, 2);
        $data["total_cogs"] = "₱ " . number_format($totalCogs, 2);
        $data["total_net_profit"] = "₱ " . number_format($totalNetProfit, 2);
        $data["total_orders"] = $totalOrders;
        $data["total_days"] = $totalDays;
        $data["average_order"] = number_format($averageOrders, 2);
        $data["average_sales"] = "₱ " . number_format($averageSales, 2);

        return array("success" => "true", "message" => "", "data" => $data);
    }
}
