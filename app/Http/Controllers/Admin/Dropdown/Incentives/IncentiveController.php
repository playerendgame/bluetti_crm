<?php

namespace App\Http\Controllers\Admin\Dropdown\Incentives;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class IncentiveController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('admins')->user()->hasPermission('incentives.show')) {
            $input = $request->all();
            $data = array(
                "filters" => json_encode(array(
                        "year" => isset($input["year"]) ? (int)$input["year"] : 0,
                        "quarter" => isset($input["quarter"]) ? (int)$input["quarter"] : 0,
                    ))
            );
            return view('admin.dropdown.incentives.index')->with($data);
        } else {
            abort(404);
        }
    }
}
