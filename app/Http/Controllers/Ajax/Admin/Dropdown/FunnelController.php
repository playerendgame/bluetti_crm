<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Funnel;
use App\Models\ActivityLogs;
use App\Models\Attribution;
use Carbon\Carbon;

class FunnelController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = Funnel::orderBy($column, $request->order);
        
        if (isset($input["keyword"])) {
            $query->where('name', 'like', '%' . $input["keyword"] . "%");
        }

        $funnels = $query->paginate($request->per_page);

        foreach ($funnels as $funnel)
        {
            $funnel->created_at_s = Carbon::parse($funnel->created_at)->toDayDateTimeString();
            $funnel->is_active_s = $funnel->is_active == 1 ? "Yes" : "";
            $funnel->attribution_name = $funnel->attribution_id != null ? $funnel->attribution->name : "";
        }

        return array("success" => true, "message" => "", "data" => $funnels);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
        ];

        $request->validate($validation_rules);
        $input = $request->all();

        $funnel = new Funnel();
        $funnel->name = $input["name"];
        $funnel->campaign_name = $input["campaign_name"];
        $funnel->is_active = $input["is_active"];
        $funnel->attribution_id = $input["attribution_id"];
        $funnel->save();

        //For the funnels activity log create
        $this->logActivity('funnel', $funnel);

        return array("success" => true, "message" => "Funnel Created Succesfully!", "data" => null);
    }


    //For the funnels activity log create
    private function logActivity($activity, $funnel){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Funnel ' . $funnel->name;

        ActivityLogs::create([
            'source' => 'Funnels',
            'admin_id' => $admin_id,
            'name' => $funnel->name,
            'activity' => $activity
        ]);

    }



    public function api(Request $request)
    {
        $query = Funnel::orderBy('name', 'asc');
        $funnels = $query->get();

        return array("success" => true, "message" => "", "data" => $attributions);
    }

    public function update(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
            'attribution_id' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();
        $funnel = null;

        if (isset($input['id'])) {
            $funnel = Funnel::find($input['id']);
            if ($funnel == null) {
                return array("success" => false, "message" => "Can't find funnel", "data" => null);
            }
        } else {
            return array("success" => true, "message" => "id is required", "data" => null);
        }

        $previous_name = $funnel->name;//FUnnel previous name - this is for activity logs
        $funnel->name = $input["name"];
        $previous_attribution_id = $funnel->attribution_id;//FUnnel previous name - this is for activity logs
        $funnel->attribution_id = $input["attribution_id"];
        $previous_campaignName = $funnel->campaign_name;//FUnnel previous campaign name - this is for activity logs
        $funnel->campaign_name = $input["campaign_name"];
        $previous_status = $funnel->is_active;//FUnnel previous status - this is for activity logs
        $funnel->is_active = $input["is_active"];
        $funnel->save();

        //For the update funnel activity log update
        $this->updateLogActivity('funnel', $funnel,  $previous_name, $previous_attribution_id, $previous_campaignName, $previous_status);

        return array("success" => true, "message" => "Funnel Updated Succesfully!", "data" => null);
    }


    //For the update funnel activity log update
    private function updateLogActivity($activity, $funnel,  $previous_name, $previous_attribution_id, $previous_campaignName, $previous_status){

        $admin_id = auth()->guard('admins')->id();
        $activity = '';
        $changes = [];

        if(strcmp($funnel->name, $previous_name) !== 0){
            $changes[] = 'Updated A Funnel Name From ' . $previous_name . ' to ' . $funnel->name;
        }
        if(strcmp($funnel->attribution_id, $previous_attribution_id) !== 0){
            $previous_attribution = Attribution::find($previous_attribution_id);
            $current_attribution = Attribution::find($funnel->attribution_id);
            $changes[] = 'Updated A Funnel Attribution From ' . $previous_attribution->name . ' to ' . $current_attribution->name;
        }
        if(strcmp($funnel->campaign_name, $previous_campaignName) !== 0){
            $changes[] = 'Updated A Funnel Campaign Name From ' . $previous_campaignName . ' to ' . $funnel->campaign_name;
        }
        if(strcmp($funnel->is_active, $previous_status) !== 0){
            $changes[] = 'Updated An Attribution Active Status From ' . ($previous_status == 1 ? 'Yes' : 'No') . ' to ' . ($funnel->is_active == 1 ? 'Yes' : 'No');
        }
        if(count($changes) > 0){
            $activity = implode(' and ', $changes);
        }

        ActivityLogs::create([
            'source' => 'Funnel',
            'admin_id' => $admin_id,
            'name' => $funnel->name,
            'activity' => $activity
        ]);
    
    }



}
