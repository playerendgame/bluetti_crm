<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function index()
    {
        return view('admin.reports.summary.index');
    }
}
