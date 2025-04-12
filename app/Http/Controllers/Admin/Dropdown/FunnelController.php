<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class FunnelController extends Controller
{
    public function index()
    {
        $hasPermission = [
            'funnel_create' => Auth::guard('admins')->user()->hasPermission('funnel.create'),
            'funnel_update' => Auth::guard('admins')->user()->hasPermission('funnel.update'),
        ];

        return view('admin.dropdown.funnel.index', compact('hasPermission'));
    }
}
