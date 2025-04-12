<?php

namespace App\Http\Controllers\Ajax\Admin\ProductsCategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Carbon\Carbon;

class ProductsCategoryController extends Controller
{

    public function list(Request $request){
        $input = $request->all();
        $column = $request->column;

        if(strcmp($column, 'created_at_s') == 0){
            $column = 'created_at';
        }

        $query = ProductCategory::orderBy('name', 'desc');

        if(isset($input['keyword'])){
            $query->where('name', 'like', '%' . $input['keyword'] . '%');
        }

        $prodCategories = $query->paginate($request->per_page);

        foreach($prodCategories as $prodCategory){
            $prodCategory->created_at_s = Carbon::parse($prodCategory->created_at)->toDayDateTimeString();
        }

        return array('succes' => true, 'message' => '', 'data' => $prodCategories);
    }

    public function store(Request $request){

        $prodCategory = new ProductCategory();

        $prodCategory->name = $request->input('name');

        $prodCategory->save();

        return response()->json(['message' => 'Product Category Has Been Stored']);

    }
}
