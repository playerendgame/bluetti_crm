<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class ModeOfPaymentController extends Controller
{
    public function index(Request $request)
    {
        $hasPermission = [
            'mop_create' => Auth::guard('admins')->user()->hasPermission('mop.create'),
            'mop_update' => Auth::guard('admins')->user()->hasPermission('mop.update')
        ];
        return view('admin.dropdown.mode-of-payment.index', compact('hasPermission'));
    }
}
