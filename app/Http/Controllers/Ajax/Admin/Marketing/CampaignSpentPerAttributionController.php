<?php

namespace App\Http\Controllers\Ajax\Admin\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attribution;
use App\Models\OrderProduct;
use App\Models\CampaignSpent;

class CampaignSpentPerAttributionController extends Controller
{
    public function campaign_list(Request $request)
    {
        $validation_rules = [
            "start_date" => 'required',
            "end_date" => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $start_date = Carbon::parse($input['start_date']);
        $end_date = Carbon::parse($input['end_date']);

        if ($start_date == null || $end_date == null) {
            return array("success" => true, "message" => "Start Date and End Date required!", "data" => null);
        }

        $attributions = Attribution::whereNotNull('campaign_name')->whereHas("campaign_spent", function ($query) use ($start_date, $end_date) {
            $query->whereBetween("date_spent", [$start_date->startOfDay(), $end_date->endOfDay()]);
            $query->where("ads_spent", ">", "0.00");
        })->get();

        $attributionArray = array();

        foreach ($attributions as $attribution) {
            $ads_spent = CampaignSpent::where("attribution_id", "=", $attribution->id)->whereBetween("date_spent", [$start_date->startOfDay(), $end_date->endOfDay()])->sum('ads_spent');
            $order_products = OrderProduct::whereHas('order', function ($query) use ($start_date, $end_date, $attribution) {
                $query->whereHas('attribution', function ($query) use ($attribution) {
                    $query->whereRelation('campaign_spent', 'attribution_id', '=', $attribution->id);
                });
                $query->whereBetween('order_date', [$start_date->startOfDay(), $end_date->endOfDay()]);
            })->get();

            $gross_sales = $order_products->sum(function ($order_product) {
                return $order_product->quantity * $order_product->price;
            });

            $orders = $order_products->sum(function ($order_product) {
                $total_orders = $order_product->price != 0 ? $order_product->quantity : 0;
                return $total_orders;
            });

            $aov = $orders > 0 ? $gross_sales / $orders : 0;

            $attributionData["campaign_name"] = $attribution->name;
            $attributionData["category"] = $attribution->getCategoryName();
            $attributionData["ad_spent"] = $ads_spent > 0 ? "₱ " . number_format($ads_spent, 2) : 0;
            $attributionData["gross_sales"] = "₱ " . number_format($gross_sales, 2);
            $attributionData["roas"] = $ads_spent != 0 ? number_format(($gross_sales / $ads_spent), 2) : 0;
            $attributionData["order"] = $orders != 0 ? $orders : 0;
            $attributionData["cost_per_purchase"] = $ads_spent > 0 ? "₱ " . number_format(($ads_spent / $orders), 2 ) : 0;
            $attributionData["aov"] = "₱ " . number_format($aov, 2);

            array_push($attributionArray, $attributionData);
        }

        $data["attributions"] = $attributionArray;

        return array('success' => true, "message" => '', "data" => $data);
    }
}
