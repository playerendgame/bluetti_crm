<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrdersPerProductCategoryController extends Controller
{
    public function index(){
        return view('admin.reports.orders-per-product-category.index');
    }
}
