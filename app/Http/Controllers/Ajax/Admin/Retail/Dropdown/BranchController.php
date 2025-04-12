<?php

namespace App\Http\Controllers\Ajax\Admin\Retail\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RetailBranch;
use Auth;
use Carbon\Carbon;

class BranchController extends Controller
{
    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
            'store_id' => 'required',
            'is_active' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $branch = new RetailBranch();
        $branch->name = $input["name"];
        $branch->store_id = $input["store_id"];
        $branch->is_active = $input["is_active"];
        $branch->save();

        return array('success' => true, "message" => "Branch Created Succesfully!", "data" => null);
    }

    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = RetailBranch::orderBy('name', 'desc');

        if (isset($input["keyword"])) {
            $query->where('name', "like", "%" . $input["keyword"] . "%");
        }

        $branches = $query->paginate($request->per_page);

        foreach ($branches as $branch) {
            $branch->status = $branch->is_active == 1 ? "Active" : "Inactive";
            $branch->store_name = $branch->store->name;
        }

        return array("success" => true, "message" => "", "data" => $branches);
    }

    public function api(Request $request)
    {
        $branches = RetailBranch::where('is_active', 1)->get();

        return array("success" => true, "message" => "", "data" => $branches);
    }
}
