<?php

namespace App\Http\Controllers\Ajax\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Province;

class ProvinceController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $query = Province::orderBy('name', 'desc');

        if (isset($input["keyword"])) {
            $query->where('name', "like", "%" . $input["keyword"] . "%");
        }

        $provinces = $query->paginate($request->per_page);

        foreach ($provinces as $province) {
            $province->region_name = $province->region->name;
            $province->status = $province->is_active == 1 ? "Active" : "Inactive";
        }

        return array("success" => true, "message" => "", "data" => $provinces);
    }

    
    public function fetchProvinces($regionId){
        
        $provinces = Province::where('region_id', $regionId)->get();
       
        return response()->json($provinces);
        
    }

    public function provinceApi()
    {
        $provinces = Province::where('is_active', true)->get();

        return array("success" => true, "message" => "", "data" => $provinces);
    }

    public function fetchProvincesAll(){
        $provinces = Province::all();

        return response()->json($provinces);
    }

    public function store(Request $request){

        $request->validate = [
            'name' => 'required',
            'region_id' => 'required',
            'is_active' => 'required',
        ];

        $province = new Province();

        $province->name = $request->input('name');
        $province->region_id = $request->input('region_id');
        $province->is_active = $request->input('is_active');

        $province->save();

        return array('success' => true, 'message' => 'Province Created Successfully', 'data' => null);

    }

    public function update(Request $request){

        $validation_rules = [
            'name' => 'required',
            'region_id' => 'required',
            'is_active' => 'required'
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $province = null;

        $province = Province::find($input['id']);

        $province->name = $input['name'];
        $province->region_id = $input['region_id'];
        $province->is_active = $input['is_active'];
        $province->save();

        return array('success' => true, 'message' => 'Province Updated Successfully', 'data' => null);

    }
}
