<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PancakeController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.reports.pancakes.index');
    }
}
