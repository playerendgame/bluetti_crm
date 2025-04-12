<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index()
    {
        $hasPermission = [
            'target_create' => Auth::guard('admins')->user()->hasPermission('target.create'),
        ];

        return view('admin.dropdown.targets.index', compact('hasPermission'));
    }
}
