<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PancakeController extends Controller
{
    public function getPancakeReport(Request $request)
    {
        $validation_rules = [
            "start_date" => 'required',
            "end_date" => 'required',
        ];

        $request->validate($validation_rules);
        $input = $request->all();

        $pageId = "103818666078693";
        $accessToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0aW1lc3RhbXAiOjE3MDQ3OTA0NDksImlkIjoiMTAzODE4NjY2MDc4NjkzIn0.p6B4CUvNU4CSqtH_HGijnD_af6xUGgQM--C5upbfTYc";

        $start_date = Carbon::parse($input["start_date"])->startOfDay();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();

        $date_range = $start_date->format("d/m/Y") . "-" . $end_date->format("d/m/Y");

        $endpoint = "https://pages.fm/api/public_api/v1/pages/{$pageId}/statistics/customer_engagements?date_range={$date_range}&access_token={$accessToken}";

        $ch = curl_init($endpoint);

        // Set the required headers
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken,
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        $responseData = json_decode($response, true);
        $usersEngagements = $responseData['users_engagements'];
        $usersArray = array();

        $total_new_pancake_leads = 0;
        $total_old_pancake_leads = 0;
        $total_new_inbox_conversation = 0;

        foreach ($usersEngagements as $userEngagement) {
            $dataTable["name"] = $userEngagement["name"];
            $dataTable["new_customer_inbox"] = isset($userEngagement["customer_engagement_new_inbox"]) ? $userEngagement["customer_engagement_new_inbox"] : 0;
            $dataTable["replied_count"] = isset($userEngagement["new_customer_replied_count"]) ? $userEngagement["new_customer_replied_count"] : 0;
            $dataTable["old_replied_count"] = isset($userEngagement["total_engagement"]) ? $userEngagement["total_engagement"] : 0;
            $dataTable["total_engagement"] = $dataTable["old_replied_count"] > 0 ? $dataTable["old_replied_count"] - $dataTable["replied_count"] : 0;

            $total_new_pancake_leads += $dataTable["new_customer_inbox"];
            $total_old_pancake_leads += $dataTable["total_engagement"];
            $total_new_inbox_conversation += $dataTable["replied_count"];

            array_push($usersArray, $dataTable);
        }

        $data["admins"] = $usersArray;
        $data["total_new_pancake_leads"] = $total_new_pancake_leads;
        $data["total_old_pancake_leads"] = $total_old_pancake_leads;
        $data["total_new_inbox_conversation"] = $total_new_inbox_conversation;

        return array("success" => "true", "message" => "", "data" => $data);
    }
}
