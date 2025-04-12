<?php

namespace App\Http\Controllers\Ajax\Admin\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyAdsAudit;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\OrderProduct;
use App\Models\ActivityLogs;

class DailyAdsAuditController extends Controller
{
    public function table(Request $request)
    {
        $input = $request->all();

        // Per Month

        $tableArray = array();
        $start_date = Carbon::parse($input['start_date'])->startOfMonth();
        $end_date = Carbon::parse($input['end_date'])->endOfMonth();

        for ($i = 0; $i <$start_date->diffInMonths($end_date)+1; $i++) {
            $currentMonth = $start_date->copy()->addMonth($i);
            $orderProducts = OrderProduct::whereHas('order', function ($query) use ($currentMonth) {
                $query->whereNull('deleted_at');
                $query->whereYear('order_date', $currentMonth->copy()->year)->whereMonth('order_date', ">=", $currentMonth->copy()->startOfMonth())->whereMonth('order_date', "<=", $currentMonth->copy()->endOfMonth());
            })->get();
            $gross_sales = $orderProducts->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });
            $ad_spents = DailyAdsAudit::whereYear('date_ad_spent', $currentMonth->copy()->year)->whereMonth('date_ad_spent', '>=', $currentMonth->copy()->startOfMonth())->whereMonth('date_ad_spent', '<=', $currentMonth->copy()->endOfMonth())->get();

            $total_ad_spent = $ad_spents->sum(function ($ad_spent) {
                return $ad_spent->facebook_ad_spent + $ad_spent->google_ad_spent + $ad_spent->lazada_ad_spent + $ad_spent->shopee_ad_spent;
            });

            $facebook_ad_spent = (clone $ad_spents)->sum('facebook_ad_spent');
            $google_ad_spent = (clone $ad_spents)->sum("google_ad_spent");
            $lazada_ad_spent = (clone $ad_spents)->sum("lazada_ad_spent");
            $shopee_ad_spent = (clone $ad_spents)->sum("shopee_ad_spent");

            $facebook_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                       $orderProduct->order->attribution->category == 1 &&
                       $orderProduct->order->attribution->campaign_name !== null;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $google_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->category == 2;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $lazada_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->category == 3;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity & $orderProduct->price;
            });

            $shopee_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->category == 4;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $referral_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->name == "Referral";
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });
            
            $organic_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->category == 5;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $dataUnit = array(
                "month" => $currentMonth->format("F Y"),
                "gross_sales" => "₱ " . number_format($gross_sales, 2),
                "ad_spent" => "₱ " . number_format($total_ad_spent, 2),
                "roas" => $total_ad_spent != 0 ? number_format(($gross_sales / $total_ad_spent), 2) : 0,
                "facebook_sales" => "₱ " . number_format($facebook_sales, 2),
                "google_sales" => "₱ " . number_format($google_sales, 2),
                "lazada_sales" => "₱ " . number_format($lazada_sales, 2),
                "shopee_sales" => "₱ " . number_format($shopee_sales, 2),
                "referral_sales" => "₱ " . number_format($referral_sales, 2),
                "organic_sales" => "₱ " . number_format($organic_sales, 2),
                "facebook_roas" => $facebook_ad_spent != 0 ? number_format(($facebook_sales / $facebook_ad_spent), 2) : 0,
                "google_roas" => $google_ad_spent != 0 ? number_format(($google_sales / $google_ad_spent), 2) : 0,
                "lazada_roas" => $lazada_ad_spent != 0? number_format(($lazada_sales / $lazada_ad_spent), 2) : 0,
                "shopee_roas" => $shopee_ad_spent != 0 ? number_format(($shopee_sales / $shopee_ad_spent), 2) : 0,
                "facebook_ad_spent" => "₱ " . number_format($facebook_ad_spent, 2),
                "google_ad_spent" => "₱ " . number_format($google_ad_spent, 2),
                "lazada_ad_spent" => "₱ " . number_format($lazada_ad_spent, 2),
                "shopee_ad_spent" => "₱ " . number_format($shopee_ad_spent, 2),
            );

            array_push($tableArray, $dataUnit);
        }

        usort($tableArray, function ($a, $b) {
            return strtotime($b['month']) - strtotime($a['month']);
        });        

        $table["table"] = $tableArray;

        return array("success" => true, "message" => "", "data" => $table);
    }

    public function getData(Request $request)
    {
        $input = $request->all();
        
        $ad_spents = DailyAdsAudit::get();

        $total_ad_spent = $ad_spents->sum(function ($ad_spent) {
            return $ad_spent->facebook_ad_spent + $ad_spent->google_ad_spent + $ad_spent->lazada_ad_spent + $ad_spent->shopee_ad_spent;
        });
        
        $orderProducts = OrderProduct::whereHas('order', function ($query) {
            $query->whereNull('deleted_at');
        })->get();

        $total_gross_sales = $orderProducts->sum(function ($orderProduct) {
            return $orderProduct->quantity * $orderProduct->price;
        });

        $data["total_ad_spent"] = "₱ " . number_format($total_ad_spent, 2);
        $data["total_gross_sales"] = "₱ " . number_format($total_gross_sales, 2);
        $data["overall_roas"] = $total_ad_spent != 0 ? number_format(($total_gross_sales / $total_ad_spent), 2) : 0;

        return array("success" => true, "message" => "", "data" => $data);
    }

    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;
        if (strcmp($column, 'date_ad_spent_s') == 0) {
            $column = "date_ad_spent";
        }

        $query = DailyAdsAudit::orderBy($column, $request->order);

        $daily_ads_audits = $query->paginate($request->per_page);

        foreach ($daily_ads_audits as $daily_ads_audit) {
            $orderProducts = OrderProduct::whereHas('order', function ($query) use ($daily_ads_audit) {
                $query->whereDate('order_date', Carbon::parse($daily_ads_audit->date_ad_spent));
            })->get();

            $facebook_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                       $orderProduct->order->attribution->category == 1 &&
                       $orderProduct->order->attribution->campaign_name !== null;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $google_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->category == 2;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $lazada_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->category == 3;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity & $orderProduct->price;
            });

            $shopee_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->category == 4;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $referral_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->name == "Referral";
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });
            
            $organic_sales = $orderProducts->filter(function ($orderProduct) {
                return $orderProduct->order->attribution &&
                    $orderProduct->order->attribution->category == 5;
            })->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $total_gross_sales = $orderProducts->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $daily_ads_audit->date_ad_spent_s = Carbon::parse($daily_ads_audit->date_ad_spent)->format("M j, Y");
            $daily_ads_audit->facebook_ad_spent_s = "₱ " . number_format($daily_ads_audit->facebook_ad_spent, 2);
            $daily_ads_audit->google_ad_spent_s = "₱ " . number_format($daily_ads_audit->google_ad_spent, 2);
            $daily_ads_audit->lazada_ad_spent_s = "₱ " . number_format($daily_ads_audit->lazada_ad_spent, 2);
            $daily_ads_audit->shopee_ad_spent_s = "₱ " . number_format($daily_ads_audit->shopee_ad_spent, 2);
            $total_ad_spent = $daily_ads_audit->facebook_ad_spent + $daily_ads_audit->google_ad_spent + $daily_ads_audit->lazada_ad_spent + $daily_ads_audit->shopee_ad_spent;
            $daily_ads_audit->total_ad_spent = "₱ " . number_format($total_ad_spent, 2);
            $daily_ads_audit->gross_sales = "₱ " . number_format($total_gross_sales, 2);
            $daily_ads_audit->roas = $total_ad_spent > 0 ? number_format(($total_gross_sales / $total_ad_spent), 2) : 0.00;
            $daily_ads_audit->facebook_sales = "₱ " . number_format($facebook_sales, 2);
            $daily_ads_audit->google_sales = "₱ " . number_format($google_sales, 2);
            $daily_ads_audit->lazada_sales = "₱ " . number_format($lazada_sales, 2);
            $daily_ads_audit->shopee_sales = "₱ " . number_format($shopee_sales, 2);
            $daily_ads_audit->referral_sales = "₱ " . number_format($referral_sales, 2);
            $daily_ads_audit->organic_sales = "₱ " . number_format($organic_sales, 2);
            // $daily_ads_audit->facebook_roas = $facebook_sales != 0 ? number_format(($facebook_sales / $daily_ads_audit->facebook_ad_spent), 2) : 0.00;
            // $daily_ads_audit->google_roas = $google_sales != 0 ? number_format(($google_sales / $daily_ads_audit->google_ad_spent), 2) : 0.00;
            // $daily_ads_audit->lazada_roas = $lazada_sales != 0 ? number_format(($lazada_sales / $daily_ads_audit->lazada_ad_spent), 2) : 0.00;
            // $daily_ads_audit->shopee_roas = $shopee_sales != 0 ? number_format(($shopee_sales / $daily_ads_audit->shopee_ad_spent), 2) : 0.00;
            
            $daily_ads_audit->facebook_roas = $daily_ads_audit->facebook_ad_spent > 0 ? number_format($facebook_sales / $daily_ads_audit->facebook_ad_spent, 2): 0;
            $daily_ads_audit->google_roas = $daily_ads_audit->google_ad_spent > 0 ? number_format($google_sales / $daily_ads_audit->google_ad_spent, 2): 0;
            $daily_ads_audit->lazada_roas = $daily_ads_audit->lazada_ad_spent > 0 ? number_format($lazada_sales / $daily_ads_audit->lazada_ad_spent, 2): 0;
            $daily_ads_audit->shopee_roas = $daily_ads_audit->shopee_ad_spent > 0 ? number_format($shopee_sales / $daily_ads_audit->shopee_ad_spent, 2): 0;

        }

        return array('success' => true, "message" => "", "data" => $daily_ads_audits);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'date_ad_spent' => 'required|unique:daily_ads_audits',
            'facebook_ad_spent' => 'required',
            'google_ad_spent' => 'required',
            'lazada_ad_spent' => 'required',
            'shopee_ad_spent' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $daily_ads_audit = new DailyAdsAudit();
        $daily_ads_audit->date_ad_spent = $input["date_ad_spent"];
        $daily_ads_audit->facebook_ad_spent = $input["facebook_ad_spent"];
        $daily_ads_audit->google_ad_spent = $input["google_ad_spent"];
        $daily_ads_audit->lazada_ad_spent = $input["lazada_ad_spent"];
        $daily_ads_audit->shopee_ad_spent = $input["shopee_ad_spent"];
        $daily_ads_audit->save();

        //For the daily ads audit activity log create
        $this->logActivity('daily_ads_audit', $daily_ads_audit);

        return array("success" => true, "message" => "Daily Ad Spent Created Succesfully!", "data" => null);
    }

     //For the daily ads audit activity log create
     private function logActivity($activity, $daily_ads_audit){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Daily Ads Audit ' . $daily_ads_audit->date_ad_spent;

        ActivityLogs::create([
            'source' => 'Daily Ads Audit',
            'admin_id' => $admin_id,
            'name' => $daily_ads_audit->date_ad_spent,
            'activity' => $activity
        ]);

    }


    public function update(Request $request)
    {
        $validation_rules = [
            'date_ad_spent' => ['required', Rule::unique('daily_ads_audits')->ignore($request->id)],
            'facebook_ad_spent' => 'required',
            'google_ad_spent' => 'required',
            'lazada_ad_spent' => 'required',
            'shopee_ad_spent' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $ads_audit = DailyAdsAudit::find($input["id"]);
        $previous_date = $ads_audit->date_ad_spent;//for the previous daily ads audit date = This is for the activity log
        $ads_audit->date_ad_spent = $input["date_ad_spent"];
        $previous_facebook_ad_spent = $ads_audit->facebook_ad_spent;//for the previous fb ad spent = This is for the activity log
        $ads_audit->facebook_ad_spent = $input["facebook_ad_spent"];
        $previous_google_ad_spent = $ads_audit->google_ad_spent;//for the previous google ad spent = This is for the activity log
        $ads_audit->google_ad_spent = $input["google_ad_spent"];
        $previous_lazada_ad_spent = $ads_audit->lazada_ad_spent;//for the previous lazada ad spent = This is for the activity log
        $ads_audit->lazada_ad_spent = $input["lazada_ad_spent"];
        $previous_shopee_ad_spent = $ads_audit->shopee_ad_spent;//for the previous shopee ad spent = This is for the activity log
        $ads_audit->shopee_ad_spent = $input["shopee_ad_spent"];
        $ads_audit->save();

        //For the update daily ads audit activity log update
        $this->updateLogActivity('ads_audit', $ads_audit, $previous_date, $previous_facebook_ad_spent, $previous_google_ad_spent, $previous_lazada_ad_spent, $previous_shopee_ad_spent);

        return array("success" => true, "message" => "Daily Ads Audit Updated Succesfully!", "data" => null);
    }
    
    //For the update daily ads audit activity log update
    private function updateLogActivity($activity, $ads_audit, $previous_date, $previous_facebook_ad_spent, $previous_google_ad_spent, $previous_lazada_ad_spent, $previous_shopee_ad_spent){

        $admin_id = auth()->guard('admins')->id();
        $activity = '';
        $changes = [];

        if(strcmp($ads_audit->date_ad_spent, $previous_date) !== 0){
            $changes[] = 'Updated A Daily Ads Audit From ' . $previous_date . ' to ' . $ads_audit->date_ad_spent;
        }
        if(strcmp($ads_audit->facebook_ad_spent, $previous_facebook_ad_spent) !== 0){
            $changes[] = 'Updated A FB Ads Spent From ' . $previous_facebook_ad_spent . ' to ' . $ads_audit->facebook_ad_spent;
        }
        if(strcmp($ads_audit->google_ad_spent, $previous_google_ad_spent ) !== 0){
            $changes[] = 'Updated A Google Ads Spent From ' . $previous_google_ad_spent . ' to ' . $ads_audit->google_ad_spent;
        }
        if(strcmp($ads_audit->lazada_ad_spent, $previous_lazada_ad_spent) !== 0){
            $changes[] = 'Updated A Lazada Ads Spent From ' . $previous_lazada_ad_spent . ' to ' . $ads_audit->lazada_ad_spent;
        }
        if(strcmp($ads_audit->shopee_ad_spent, $previous_shopee_ad_spent) !== 0){
            $changes[] = 'Updated A Shopee Ads Spent From ' . $previous_shopee_ad_spent . ' to ' . $ads_audit->shopee_ad_spent;
        }
        if(count($changes) > 0){
            $activity = implode(' and ', $changes);
        }

        ActivityLogs::create([
            'source' => 'Daily Ads Audit',
            'admin_id' => $admin_id,
            'name' => $ads_audit->date_ad_spent,
            'activity' => $activity
        ]);
    
    }

}
