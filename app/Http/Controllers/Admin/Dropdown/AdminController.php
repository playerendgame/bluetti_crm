<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Admin;

class AdminController extends Controller
{
    public function getAdmin()
    {
       // if (collect(auth('admins')->user()->admin_roles)->contains('id', 1) && collect(auth('admins')->user()->admin_roles)->whereIn(['create', 'read', 'update', 'delete'], true)) {
            $hasPermission = [
                'admin_create' => Auth::guard('admins')->user()->hasPermission('admin.create'),
                'admin_update' => Auth::guard('admins')->user()->hasPermission('admin.update'),
                'admin_delete' => Auth::guard('admins')->user()->hasPermission('admin.delete'),
            ];
            return view('admin.dropdown.admin.index', compact('hasPermission'));
       // } else {
           // abort(404);
       // }
    }

    public function impersonate($id = null)
    {
        if ($id == null) {
            return redirect()->back();
        }

        $admin = Admin::find($id);
        Auth::guard("admins")->login($admin, true);
        return redirect(route('admin.dashboard'));
    }

    public function stopImpersonating(Request $request)
    {
        Auth::guard('admins')->user()->leaveImpersonation();
        return redirect(route('admin.index'));
    }

    
    
}
