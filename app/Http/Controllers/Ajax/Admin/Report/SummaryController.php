<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OrderProduct;
use App\Models\Order;
use App\Models\Target;

class SummaryController extends Controller
{
    public function data(Request $request)
    {
        $validation_rules = [
            "start_date" => 'required',
            "end_date" => 'required',
        ];

        $request->validate($validation_rules);
        $input = $request->all();

        $start_date = Carbon::parse($input["start_date"])->startOfDay();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();
        $tableArray = array();

        $totalGrossSales = 0;
        $totalCogs = 0;

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

            $orderProducts = OrderProduct::whereHas('order', function ($query) use ($currentMonth) {
                $query->whereNull('deleted_at')->where('delivery_status', '<>', 6);
                $query->whereYear('order_date', $currentMonth->copy()->year)->where('order_date', ">=", $currentMonth->copy()->startOfMonth())->where('order_date', "<=", $currentMonth->copy()->endOfMonth());
            })->get();
            $gross_sales = $orderProducts->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $cogs = OrderProduct::whereHas('order', function ($query) use ($currentMonth) {
                $query->whereNull('deleted_at')->where('delivery_status', '<>', 6);
                $query->whereYear('order_date', $currentMonth->copy()->year)->where('order_date', ">=", $currentMonth->copy()->startOfMonth())->where('order_date', "<=", $currentMonth->copy()->endOfMonth());
            })->get();
            $cogs = $cogs->sum(function ($cogs) {
                return $cogs->quantity * $cogs->cogs;
            });

            $today = Carbon::now()->addDays(2);

            $orders = Order::whereYear('order_date', $currentMonth->copy()->year)->where('order_date', ">=", $currentMonth->copy()->startOfMonth())->where('order_date', "<=", $currentMonth->copy()->endOfMonth())->where('delivery_status', '!=', 6)->count();
            $target = Target::whereYear('date', $currentMonth->copy()->year)->whereDate('date', ">=", $currentMonth->copy()->startOfMonth())->whereDate('date', "<=", $currentMonth->copy()->endOfMonth())->sum('sales_target');
            $days = $today->diffInDays($currentMonth->copy()->endOfDay());

            $dataUnit = array(
                "month" => $currentMonth->format("F Y"),
                "count_days" => $count_days,
                "gross_sales" => "₱ " . number_format($gross_sales, 2),
                "cogs" => "₱ " . number_format($cogs, 2),
                "conversion" => $gross_sales > 0 ? number_format((($cogs / $gross_sales) * 100), 2) . "%" : "0%",
                "orders" => $orders,
                "daily_ave_order" => $orders > 0 ? number_format(($orders / $count_days), 2) : 0,
                "aov" => $gross_sales > 0 ? "₱ " . number_format(($gross_sales / $orders), 2) : "₱ 0",
                "daily_ave_sales" => $gross_sales > 0 ? "₱ " . number_format(($gross_sales / $count_days), 2) : "₱ 0",
                "sales_target" => $target != null ? "₱ " . number_format($target, 2) : "₱ 0",
                "sales_lag" => "₱ " . number_format(((($gross_sales > 0 ? $gross_sales / $count_days : 0) - ($target / $count_days)) * $days), 2),
            );

            $totalGrossSales += $gross_sales;
            $totalCogs += $cogs;

            array_push($tableArray, $dataUnit);
        }

        usort($tableArray, function ($a, $b) {
            return strtotime($b['month']) - strtotime($a['month']);
        });

        $data["table"] = $tableArray;

        $data["total_gross_sales"] = "₱ " . number_format($totalGrossSales, 2);
        $data["total_cogs"] = "₱ " . number_format($totalCogs, 2);
        $data["cogs_vat"] = $totalGrossSales > 0 ? number_format((($totalCogs / $totalGrossSales) * 100), 2) . "%" : "0%";

        return array("success" => "true", "message" => "", "data" => $data);
    }
}
