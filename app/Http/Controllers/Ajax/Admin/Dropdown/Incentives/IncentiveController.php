<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown\Incentives;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Auth;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\OrderProduct;

class IncentiveController extends Controller
{
    public function incentives(Request $request)
    {
        $validation_rules = [
            "year" => 'required',
            "quarter" => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        if ($input["year"] == 0 && $input["quarter"] == 0) {
            return array("success" => true, "message" => "Year and Quarter is required!", "data" => null);
        }

        $quarter = $input["quarter"];
        $year = $input["year"];

        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $quarter * 3;

        $startDate = Carbon::createFromDate($year, $startMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth();

        $admins = Admin::whereHas('admin_roles', function ($query) {
            $query->where("name", '=', "Sales Representative");
        })->get();
        
        $data = ["admins" => []];

        foreach ($admins as $admin) {
            $admin->generated_ad_sales = 0;
            $admin->direct_lead_sales = 0;
            $admin->total_sales = 0;
            $admin->generated_ad_incentives = 0;
            $admin->direct_incentives_generated = 0;
            $admin->total_incentives_generated = 0;

            $adminData = ["name" => $admin->fullName()];

            for ($i = 0; $i < $startDate->diffInMonths($endDate) + 1; $i++) {
                $generated_ad_sales = 0;
                $direct_lead_sales = 0;
                $total_sales = 0;
                $generated_ad_incentives = 0;
                $direct_lead_incentives = 0;
                $total_incentives_generated = 0;

                $currentMonth = $startDate->copy()->addMonth($i);
                $monthKey = $currentMonth->format("F Y");

                $orders = Order::where('mark_as_paid', 1)
                ->where('admin_id', '=', $admin->id)
                ->get();

                foreach ($orders as $order) {
                    $generated_ad_order_products = OrderProduct::whereHas('order', function ($query) use ($currentMonth, $admin) {
                        $query->whereBetween('date_paid', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()]);
                        $query->where(function ($query) use ($admin) {
                            $query->whereDoesntHave('referral', function ($query) use ($admin) {
                                $query->where('email', $admin->email);
                            })
                            ->orWhereRelation('attribution', 'name', '<>', 'Referral');                        
                        });
                    })->where('order_id', '=', $order->id)->get();

                    $generatedAdSales = $generated_ad_order_products->sum(function ($order_product) {
                        return ($order_product->quantity * $order_product->price) - $order_product->discount;
                    });

                    $generated_ad_sales += $generatedAdSales;

                    $direct_lead_products = OrderProduct::whereHas('order', function ($query) use ($currentMonth, $admin) {
                        $query->whereBetween('date_paid', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()]);
                        $query->whereRelation('attribution', 'name', '=', 'Referral');
                        $query->whereRelation('referral', 'email', '=', $admin->email);
                    })->where('order_id', '=', $order->id)->get();

                    $directLeadSales = $direct_lead_products->sum(function ($op) {
                        return ($op->quantity * $op->price) - $op->discount;
                    });

                    $direct_lead_sales += $directLeadSales;

                    $total_sales = $direct_lead_sales + $generated_ad_sales;

                    if ($generated_ad_sales >= 1250000 && $generated_ad_sales <= 1999999) {
                        $multiplier = 0.003;
                    } elseif ($generated_ad_sales >= 2000000 && $generated_ad_sales <= 2499999) {
                        $multiplier = 0.004;
                    } elseif ($generated_ad_sales >= 2500000) {
                        $multiplier = 0.005;
                    } else {
                        $multiplier = 0;
                    }

                    $eligible = $generated_ad_sales - 1250000;

                    if ($generated_ad_sales >= 1250000) {
                        $generated_ad_incentives = 3750;
                    }

                    $generated_ad_incentives += $eligible * $multiplier;

                    $direct_lead_incentives += $directLeadSales * 0.02;

                    $total_incentives_generated = $generated_ad_incentives + $direct_lead_incentives;
                }

                $admin->generated_ad_sales = $generated_ad_sales;
                $admin->direct_lead_sales = $direct_lead_sales;
                $admin->total_sales = $total_sales;
                $admin->generated_ad_incentives = $generated_ad_incentives;
                $admin->direct_incentives_generated = $direct_lead_incentives;
                $admin->total_incentives_generated = $total_incentives_generated;


                $adminData[$monthKey] = [
                    "ad_sales_generated" => "₱ " . number_format($admin->generated_ad_sales, 2),
                    "direct_sales_generated" => "₱ " . number_format($admin->direct_lead_sales, 2),
                    "total_sales_generated" => "₱ " . number_format($admin->total_sales, 2),
                    "generated_ad_incentives" => "₱ " . number_format($admin->generated_ad_incentives, 2),
                    "direct_incentives_generated" => "₱ " . number_format($admin->direct_incentives_generated, 2),
                    "total_incentives_generated" => "₱ " . number_format($admin->total_incentives_generated, 2),
                ];
            }

            $data["admins"][] = $adminData;
        }


        return array("success" => true, "message" => "", "data" => $data);
    }
}
