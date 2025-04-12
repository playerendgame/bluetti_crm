<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class CourierController extends Controller
{
   public function index(Request $request)
    {
        $hasPermission = [
            'courier_create' => Auth::guard('admins')->user()->hasPermission('courier.create'),
            'courier_update' => Auth::guard('admins')->user()->hasPermission('courier.update'),
            'courier_delete' => Auth::guard('admins')->user()->hasPermission('courier.delete'),
        ];
        return view('admin.dropdown.couriers.index', compact('hasPermission'));
    }
}

