<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class RegionController extends Controller
{
    public function index()
    {

        $hasPermission = [
            'region_update' => Auth::guard('admins')->user()->hasPermission('regions.update'),
            'region_create' => Auth::guard('admins')->user()->hasPermission('regions.create')

        ];

        return view('admin.dropdown.region.index', compact('hasPermission'));
    }
}
