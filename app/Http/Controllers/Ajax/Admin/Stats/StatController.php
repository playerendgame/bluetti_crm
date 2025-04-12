<?php

namespace App\Http\Controllers\Ajax\Admin\Stats;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderProduct;
use Auth;
use App\Models\Referral;
use DB;

class StatController extends Controller
{
    public function stats(Request $request)
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

        $salesQuota = 1250000;
        $directLeadPercentage = .02;

        $email = Auth::guard('admins')->user()->email;

        $startDate = Carbon::createFromDate($year, $startMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth();

        $tableArray = array();
        for ($i = 0; $i <$startDate->diffInMonths($endDate) + 1; $i++) {
            $generated_ad_sales = 0;
            $direct_lead_sales = 0;
            $total_sales = 0;
            $generated_ad_incentives = 0;
            $direct_lead_incentives = 0;

            $currentMonth = $startDate->copy()->addMonth($i);

            $orders = Order::where('mark_as_paid', 1)
            ->where('admin_id', Auth::guard('admins')->user()->id)
            ->get();

            foreach ($orders as $order) {
                $generated_ad_order_products = OrderProduct::whereHas('order', function ($query) use ($currentMonth, $email) {
                    $query->whereBetween('date_paid', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()]);
                    $query->where(function ($query) use ($email) {
                        $query->whereDoesntHave('referral', function ($query) use ($email) {
                            $query->where('email', $email);
                        })
                        ->orWhereRelation('attribution', 'name', '<>', 'Referral');                        
                    });
                })->where('order_id', '=', $order->id)->get();

                $generatedAdSales = $generated_ad_order_products->sum(function ($order_product) {
                    return ($order_product->quantity * $order_product->price) - $order_product->discount;
                });

                $generated_ad_sales += $generatedAdSales;

                $direct_lead_products = OrderProduct::whereHas('order', function ($query) use ($currentMonth, $email) {
                    $query->whereBetween('date_paid', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()]);
                    $query->whereRelation('attribution', 'name', '=', 'Referral');
                    $query->whereRelation('referral', 'email', '=', $email);
                })->where('order_id', '=', $order->id)->get();

                $directLeadSales = $direct_lead_products->sum(function ($op) {
                    return ($op->quantity * $op->price) - $op->discount;
                });

                $direct_lead_sales += $directLeadSales;

                $total_sales = $direct_lead_sales + $generated_ad_sales;

                if ($generated_ad_sales >= 1250000 && $generated_ad_sales <= 1999999) {
                    $multiplier = 0.003; // Replace `F3` with the actual percentage multiplier value
                } elseif ($generated_ad_sales >= 2000000 && $generated_ad_sales <= 2499999) {
                    $multiplier = 0.004; // Replace `F4` with the actual percentage multiplier value
                } elseif ($generated_ad_sales >= 2500000) {
                    $multiplier = 0.005; // Replace `F5` with the actual percentage multiplier value
                } else {
                    $multiplier = 0;
                }
                
                $eligible = $generated_ad_sales - 1250000;

                if ($generated_ad_sales >= $salesQuota) {
                    $generated_ad_incentives = 3750;
                }

                $generated_ad_incentives += $eligible * $multiplier;

                $direct_lead_incentives += $directLeadSales * $directLeadPercentage;

                $total_incentives = $direct_lead_incentives + $generated_ad_incentives;
            }

            $dataUnit = [
                "month" => $currentMonth->format("F Y"),
                "ad_sales_generated" => "₱ " . number_format($generated_ad_sales, 2),
                "direct_sales_generated" => "₱ " . number_format($direct_lead_sales, 2),
                "total_sales_generated" => "₱ " . number_format($total_sales, 2),
                "ad_incentives_generated" => "₱ " . number_format($generated_ad_incentives, 2),
                "direct_incentives_generated" => "₱ " . number_format($direct_lead_incentives, 2),
                "total_incentives_generated" => "₱ " . number_format($total_incentives, 2),
            ];

            array_push($tableArray, $dataUnit);
        }

        usort($tableArray, function ($a, $b) {
            return strtotime($b['month']) - strtotime($a['month']);
        });

        $data["stats"] = $tableArray;
        $data["ad_sales_generated"] = "₱ " . number_format(array_sum(array_map(function ($item) {
                return floatval(str_replace([",", "₱ "], "", $item['ad_sales_generated']));
            }, $tableArray)),2
        );
        $data["direct_sales_generated"] = "₱ " . number_format(array_sum(array_map(function ($item) {
                return floatval(str_replace([",", "₱ "], "", $item['direct_sales_generated']));
            }, $tableArray)),2
        );
        $data["total_sales_generated"] = "₱ " . number_format(array_sum(array_map(function ($item) {
                return floatval(str_replace([",", "₱ "], "", $item['total_sales_generated']));
            }, $tableArray)),2
        );
        $data["ad_incentives_generated"] = "₱ " . number_format(array_sum(array_map(function ($item) {
                return floatval(str_replace([",", "₱ "], "", $item['ad_incentives_generated']));
            }, $tableArray)),2
        );
        $data["direct_incentives_generated"] = "₱ " . number_format(array_sum(array_map(function ($item) {
                return floatval(str_replace([",", "₱ "], "", $item['direct_incentives_generated']));
            }, $tableArray)),2
        );
        $data["total_incentives_generated"] = "₱ " . number_format(array_sum(array_map(function ($item) {
                return floatval(str_replace([",", "₱ "], "", $item['total_incentives_generated']));
            }, $tableArray)),2
        );

        return array("success" => true, "message" => "", "data" => $data);
    }

    public function list(Request $request)
    {
        $validation_rules = [
            "year" => 'required',
            "quarter" => 'required',
        ];

        $request->validate($validation_rules);
        
        $input = $request->all();
        $column = $request->column;

        if ($input["year"] == 0 && $input["quarter"] == 0) {
            return array("success" => true, "message" => "Year and Quarter is required!", "data" => null);
        }

        $quarter = $input["quarter"];
        $year = $input["year"];

        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $quarter * 3;

        $startDate = Carbon::createFromDate($year, $startMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth();

        if (strcmp($column, "order_date_s") == 0) {
            $column = "order_date";
        }

        $query = Order::where("admin_id", '=', Auth::guard('admins')->user()->id)
        ->where('mark_as_paid', 1)
        ->whereBetween('date_paid', [$startDate, $endDate])->orderBy($column, $request->order);

        if (isset($input["date_paid_from"]) && $input["date_paid_to"] != 0) {
            $query->whereBetween('date_paid', [Carbon::parse($input["date_paid_from"])->startOfDay(), Carbon::parse($input["date_paid_to"])->endOfDay()]);
        }

        if (isset($input["date_order_from"]) && $input["date_order_to"] != 0) {
            $query->whereBetween('order_date', [Carbon::parse($input["date_order_from"])->startOfDay(), Carbon::parse($input["date_order_to"])->endOfDay()]);
        }

        if (isset($input["delivery_status"]) && $input["delivery_status"] != 99) {
            $query->where('delivery_status', $input["delivery_status"]);
        }

        if (isset($input["date_delivered_from"]) && $input["date_delivered_to"] != 0) {
            $query->whereBetween('date_delivered', [Carbon::parse($input["date_delivered_from"])->startOfDay(), Carbon::parse($input["date_delivered_to"])->endOfDay()]);
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
            $order->customer_name = $order->customers->name;
            $total_price = $order_products->sum(function ($order_product) {
                return ($order_product->quantity * $order_product->price) - $order_product->discount;
            });
            $order->total_price = "₱ " . number_format($total_price, 2);
            $order->date_delivered_s = $order->date_delivered != null ? Carbon::parse($order->date_delivered)->format("M j, Y") : "";
            $order->mark_as_paid_s = $order->markAsPaid();
            $order->delivery_status_s = $order->deliveryStatusName();
            $order->date_paid_s = $order->date_paid != null ? Carbon::parse($order->date_paid)->format("M j, Y") : "";
        }
        
        return array("success" => true, "message" => "", "data" => $orders);
    }
}
