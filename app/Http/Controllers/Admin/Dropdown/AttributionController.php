<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class AttributionController extends Controller
{
    public function index(Request $request)
    {
        $hasPermission = [
            'att_create' => Auth::guard('admins')->user()->hasPermission('att.create'),
            'att_update' => Auth::guard('admins')->user()->hasPermission('att.update'),
            'att_delete' => Auth::guard('admins')->user()->hasPermission('att.delete'),
        ];
        return view('admin.dropdown.attributions.index', compact('hasPermission'));
    }
}
