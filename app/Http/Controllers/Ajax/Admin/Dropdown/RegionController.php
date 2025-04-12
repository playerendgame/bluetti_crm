<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = Region::orderBy('name', 'desc');

        if (isset($input["keyword"])) {
            $query->where('name', "like", "%" . $input["keyword"] . "%");
        }

        $regions = $query->paginate($request->per_page);

        foreach ($regions as $region) {
            $region->status = $region->is_active == 1 ? "Active" : "Inactive";
        }

        return array("success" => true, "message" => "", "data" => $regions);
    }

    public function fetchRegions(){

        $regions = Region::all();

        return response()->json($regions);

    }

    public function regionsApi()
    {
        $regions = Region::where('is_active', true)->get();

        return array("success" => true, "message" => "", "data" => $regions);
    }

    
    public function store(Request $request){

        $request->validate = [
            'name' => 'required',
            'is_active' => 'required'
        ];

        $region = new Region();
        $region->name = $request->input('name');
        $region->is_active = $request->input('is_active');
        $region->save();

        return array('success' => true, 'message' => 'City Created Successfully', 'data' => null);

    }

    public function update(Request $request){

        $validation_rules = [
            'name' => 'required',
            'is_active' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $region = null;

        $region = Region::find($input['id']);

        $region->name = $input['name'];
        $region->is_active = $input['is_active'];

        $region->save();

        return array('success' => true, 'message' => 'Region Updated Successfully', 'data' => null);

    }
}
