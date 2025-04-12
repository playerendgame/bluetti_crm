<?php

namespace App\Http\Controllers\Admin\ProductsCategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class ProductsCategoryController extends Controller
{
    public function index(){

        $hasPermission = [

            'products_category_create' => Auth::guard('admins')->user()->hasPermission('products_category.show'),

        ];        

        return view('admin.products_category.index', compact('hasPermission'));

    }

}
