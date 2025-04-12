<?php

namespace App\Http\Controllers\Ajax\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogsController extends Controller
{
    public function getActivityLogs(Request $request){

        $from_date = Carbon::parse($request->input('from_date'))->startOfDay();
        $to_date = Carbon::parse($request->input('to_date'))->endOfDay();
        $search = $request->input('search');

        $activity_logs = ActivityLogs::whereBetween('created_at', [$from_date, $to_date])
            ->with(['admins' => function ($query){
                $query->select('id', 'first_name', 'last_name');
            }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('source', 'LIKE', "%{$search}%")//Source
                        ->orWhere('name', 'LIKE', "%{$search}%")//SOurce name
                        ->orWhereHas('admins', function ($adminQuery) use ($search) {//For the admin first_name last_name can be searchable
                            $adminQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($activity_logs);

    }
}
