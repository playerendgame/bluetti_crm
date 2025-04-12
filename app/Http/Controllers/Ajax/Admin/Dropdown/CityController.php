<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = City::orderBy('name', 'desc');

        if (isset($input["keyword"])) {
            $query->where('name', "like", "%" . $input["keyword"] . "%");
        }

        $cities = $query->paginate($request->per_page);

        foreach ($cities as $city) {
            $city->province_name = $city->province->name;
            $city->status = $city->is_active == 1 ? "Active" : "Inactive";
        }

        return array("success" => true, "message" => "", "data" => $cities);
    }

    public function store(Request $request){

        $request->validate=[
            'name' => 'required',
            'province_id' => 'required',
            'is_active' => 'required'
        ];

        $city = new City();

        $city->name = $request->input('name');
        $city->province_id = $request->input('province_id');
        $city->is_active = $request->input('is_active');
        $city->save();

        return array('success' => true, 'message' => 'City Created Successfully', 'data' => null);


    }

    public function update(Request $request){
        $validation_rules = [
            'name' => 'required',
            'province_id' => 'required',
            'is_active' => ' required'
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $city = null;

        $city = City::find($input['id']);

        $city->name = $input['name'];
        $city->province_id = $input['province_id'];
        $city->is_active = $input['is_active'];
        $city->save();

        return array('success' => true, 'message' => 'City Updated Successfully', 'data' => null);

    }

    public function fetchCity($provinceId){

        $cities = City::where('province_id', $provinceId)->get();

        return response()->json($cities);

    }

    public function cityApi()
    {
        $cities = City::where('is_active', true)->get();

        return array('success' => true, "message" => "", "data" => $cities);
    }
    
}
