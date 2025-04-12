<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DailyAdsAuditController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.marketing.daily-ads-audit.index');
    }

    public function create(Request $request)
    {
        return view('admin.marketing.daily-ads-audit.create');
    }
}
