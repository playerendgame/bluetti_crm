<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class OrderPerAreaController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.reports.orders-per-area.index');
    }
}
