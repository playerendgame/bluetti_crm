<?php

namespace App\Http\Controllers\Ajax\Admin\Retail\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\RetailStore;
use Carbon\Carbon;

class StoreController extends Controller
{
    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
            'is_active' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $store = new RetailStore();
        $store->name = $input["name"];
        $store->is_active = $input["is_active"];
        $store->save();

        return array("success" => true, "message" => "Store Created Succesfully!", "data" => null);
    }

    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = RetailStore::orderBy('name', 'desc');

        if (isset($input["keyword"])) {
            $query->where('name', "like", "%" . $input["keyword"] . "%");
        }

        $stores = $query->paginate($request->per_page);

        foreach ($stores as $store)
        {
            $store->status = $store->is_active == 1 ? "Active" : "Inactive";
        }

        return array("success" => true, "message" => "", "data" => $stores);
    }

    public function api(Request $request)
    {
        $query = RetailStore::orderBy('name', 'desc');
        $stores = $query->get();

        return array("success" => true, "message" => "", "data" => $stores);
    }
}
