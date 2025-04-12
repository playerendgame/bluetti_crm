<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        //if (collect(auth('admins')->user()->admin_roles)->contains('id', 1) && collect(auth('admins')->user()->admin_roles)->where(['create', 'read', 'update', 'delete'], true)) {
            $hasPermission = [
                'role_create' => Auth::guard('admins')->user()->hasPermission('role.create'),
                'role_update' => Auth::guard('admins')->user()->hasPermission('role.update'),
                'role_delete' => Auth::guard('admins')->user()->hasPermission('role.delete'),
            ];
            return view('admin.dropdown.role.index', compact('hasPermission'));
        //} else {
           // abort(404);
       // }   
    }

}
