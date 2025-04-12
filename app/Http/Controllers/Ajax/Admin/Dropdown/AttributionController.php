<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribution;
use App\Models\ActivityLogs;
use App\Models\DistributionChannel;

class AttributionController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = Attribution::orderBy($column, $request->order);

        if (isset($input["keyword"])) {
            $query->where('name', 'like', '%' . $input["keyword"] . "%");
        }

        $attributions = $query->paginate($request->per_page);

        foreach ($attributions as $attribution)
        {
            $attribution->category_name = $attribution->getCategoryName();
            $attribution->is_active_s = $attribution->getIsActiveName();
            $attribution->distribution_channel_name = $attribution->distribution_channel ? $attribution->distribution_channel->name : '';
        }

        return array("success" => true, "message" => "", "data" => $attributions);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $attribution = new Attribution();
        $attribution->name = $input["name"];
        $attribution->category = $input["category"];
        $attribution->campaign_name = $input["campaign_name"];
        $attribution->distribution_channel_id = $input['distribution_channel_id'];
        $attribution->is_active = $input["is_active"];
        $attribution->save();

        //Logs the attribution to activity log
        $this->logActivity('attribution', $attribution);

        return array("success" => true, "message" => "Attribution Created Successfully!", "data" => null);
    }

     //For the attribution activity log create
     private function logActivity($activity, $attribution){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created An Attribution ' . $attribution->name;

        ActivityLogs::create([
            'source' => 'Attribution',
            'admin_id' => $admin_id,
            'name' => $attribution->name,
            'activity' => $activity
        ]);

    }



    public function update(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();
        $attribution = null;
        
        if (isset($input['id'])) {
            $attribution = Attribution::find($input['id']);
            if ($attribution == null) {
                return array("success" => false, "message" => "Can't find attribution", "data" => null);
            }
        } else {
            return array("success" => true, "message" => "id is required", "data" => null);
        }

        $previous_name = $attribution->name;//previous name - This is for activity log
        $attribution->name = $input["name"];
        $previous_category = $attribution->category;//previous category - This is for activity log
        $attribution->category = $input["category"];
        $previous_campaignName = $attribution->campaign_name;//previous name - This is for activity log 
        $attribution->campaign_name = $input["campaign_name"];

        $attribution->distribution_channel_id = $input['distribution_channel_id'];

        $previous_isActive = $attribution->is_active;//previous is Active - This is for activity log
        $attribution->is_active = $input["is_active"];
        $attribution->save();

        $this->updateLogActivity('attribution', $attribution, $previous_name, $previous_category, $previous_campaignName, $previous_isActive);

        return array("success" => true, "message" => "Attribution Updated Succesfully!", "data" => null);
    }



     //For the update attribution activity log update
     private function updateLogActivity($activity, $attribution, $previous_name, $previous_category, $previous_campaignName, $previous_isActive){

        $admin_id = auth()->guard('admins')->id();
        $activity = '';
        $changes = [];

        if(strcmp($attribution->name, $previous_name) !== 0){
            $changes[] = 'Updated An Attribution Name From ' . $previous_name . ' to ' . $attribution->name;
        }
        if(strcmp($attribution->category, $previous_category) !== 0){
            $previous_category_name = Attribution::find($previous_category)->getCategoryName();
            $current_category_name = $attribution->getCategoryName();
            $changes[] = 'Updated An Attribution Category From ' . $previous_category_name . ' to ' . $current_category_name;
        }
        if(strcmp($attribution->campaign_name, $previous_campaignName) !== 0){
            $changes[] = 'Updated An Attribution Campaign Name From ' . $previous_campaignName . ' to ' . $attribution->campaign_name;
        }
        if(strcmp($attribution->is_active, $previous_isActive) !== 0){
            $changes[] = 'Updated An Attribution Active Status From ' . ($previous_isActive == 1 ? 'Yes' : 'No') . ' to ' . ($attribution->is_active == 1 ? 'Yes' : 'No');
        }
        if(count($changes) > 0){
            $activity = implode(' and ', $changes);
        }

        ActivityLogs::create([
            'source' => 'Attribution',
            'admin_id' => $admin_id,
            'name' => $attribution->name,
            'activity' => $activity
        ]);
    
    }


    public function fetchDistributionChannel(){

        $distributionChannels = DistributionChannel::all();

        return response()->json($distributionChannels);

    }


    public function api(Request $request)
    {
        $query = Attribution::orderBy('name', 'asc');
        $attributions = $query->get();

        foreach ($attributions as $attribution) {
            $attribution->category_name = $attribution->getCategoryName();
        }

        return array("success" => true, "message" => "", "data" => $attributions);
    }

    public function delete($id)
    {
        $attribution = Attribution::find($id);

        $attribution->delete();

        $this->deleteLogActivity('attribution', $attribution);
    
        return response()->json(["success" => true, "message" => "Attribution deleted successfully"], 200);
    }

    //For the attribution activity log create
    private function deleteLogActivity($activity, $attribution){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Deleted An Attribution ' . $attribution->name;

        ActivityLogs::create([
            'source' => 'Attribution',
            'admin_id' => $admin_id,
            'name' => $attribution->name,
            'activity' => $activity
        ]);

    }


}