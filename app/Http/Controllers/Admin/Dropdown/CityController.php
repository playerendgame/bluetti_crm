<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class CityController extends Controller
{
    public function index()
    {

        $hasPermission = [
            'city_update' => Auth::guard('admins')->user()->hasPermission('city.update'),
            'city_create' => Auth::guard('admins')->user()->hasPermission('city.create'),

        ];

        return view('admin.dropdown.city.index', compact('hasPermission'));
    }
}
