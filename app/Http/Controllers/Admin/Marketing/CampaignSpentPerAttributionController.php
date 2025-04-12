<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\CampaignSpent;
use App\Models\Attribution;
use Carbon\Carbon;

class CampaignSpentPerAttributionController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.marketing.campaign-spent-per-attribution.index');
    }

    public function import(Request $request)
    {
        if (empty($request->file('import_file'))) {
            return back()->with('flash_message_error', 'No file selected');
        } else {
            $filePath = $request->file('import_file')->store('campaign_spent');
            $adspends = (new FastExcel)->sheet(1)->import(storage_path('app/') . $filePath);

            foreach($adspends as $adspend) {
                $attribution = Attribution::where("campaign_name", "=", $adspend["Campaign name"])->first();

                if ($attribution == null) 
                {
                    continue;
                }

                $camapaignSpent = CampaignSpent::where("attribution_id", "=", $attribution->id)->where("date_spent", "=", Carbon::parse($adspend["Reporting starts"]))->first();

                if ($camapaignSpent == null)
                {
                    $camapaignSpent = new CampaignSpent();
                    $camapaignSpent->date_spent = Carbon::parse($adspend["Reporting starts"]);
                    $camapaignSpent->attribution_id = $attribution->id;
                }

                $camapaignSpent->ads_spent = $adspend["Amount spent (PHP)"];
                $camapaignSpent->save();
            }
            
            return redirect()->back()->with("flash_message_success", 'Campaigns Spent Imported Succesfully!');
        }
    }
}
