<?php

namespace App\Http\Controllers\Admin\Retail\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function summary(Request $request)
    {
        $input = $request->all();
        $data = array(
            'filters' => json_encode(array(
                "store" => isset($input["store"]) ? (int)$input["store"] : 0,
                "branch" => isset($input["branch"]) ? (int)$input["branch"] : 0,
            ))
        );
        return view('admin.retails.reports.summary')->with($data);
    }
}
