<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DistributionChannel;
use App\Models\ActivityLogs;
use Carbon\Carbon;

class DistributionChannelsController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;
        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = DistributionChannel::orderBy($column, $request->order);

        if (isset($input["keyword"])) {
            $query->where('name', 'like', '%' . $input["keyword"] . "%");
        }

        $distributionChannels = $query->paginate($request->per_page);

        foreach ($distributionChannels as $distributionChannel) {
            $distributionChannel->created_at_s = Carbon::parse($distributionChannel->created_at)->toDayDateTimeString();
        }

        return array("success" => true, "message" => "", "data" => $distributionChannels);
    }

    public function store(Request $request){

        $distributionChannels = new DistributionChannel();

        $distributionChannels->name = $request->input('name');

        $distributionChannels->save();

        //For the dist channel activity log create
        $this->logActivity('distributionChannels', $distributionChannels);

        return response()->json(['message' => 'Distribution Channels Addded Successfully!']);

    }

    private function logActivity($activity, $distributionChannels){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Distribution Channel ' . $distributionChannels->name;

        ActivityLogs::create([
            'source' => 'Distribution Channels',
            'admin_id' => $admin_id,
            'name' => $distributionChannels->name,
            'activity' => $activity
        ]);

    }

    public function update(Request $request){

        $validation_rules = [
            'name' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $distribution_channel = null;

        $distribution_channel = DistributionChannel::find($input['id']);
    
        $previous_distribution_channel = $distribution_channel->name;
        $distribution_channel->name = $input['name'];

        $distribution_channel->save();

        $this->updateLogActivity('distribution_channel', $distribution_channel, $previous_distribution_channel);


        return array('success' => true, 'message' => 'Distribution Channel Updated Succesfully!', 'data' => null);

    }

      //For the update product activity log update
      private function updateLogActivity($activity, $distribution_channel, $previous_distribution_channel){

        $admin_id = auth()->guard('admins')->id();
        $activity = '';
        $changes = [];

        if(strcmp($distribution_channel->name, $previous_distribution_channel) !== 0){
            $changes[] = 'Updated A Distribution Channel Name From ' . $previous_distribution_channel . ' to ' . $distribution_channel->name;
        }
        if(count($changes) > 0){
            $activity = implode(' and ', $changes);
        }

        ActivityLogs::create([
            'source' => 'Distribution Channels',
            'admin_id' => $admin_id,
            'name' => $distribution_channel->name,
            'activity' => $activity
        ]);
    
    }
}
