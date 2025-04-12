<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class ProvinceController extends Controller
{
    public function index()
    {

        $hasPermission = [

            'province_update' => Auth::guard('admins')->user()->hasPermission('provinces.update'),
            'province_create' => Auth::guard('admins')->user()->hasPermission('provinces.create'),

        ];

        return view('admin.dropdown.province.index', compact('hasPermission'));
    }
}
