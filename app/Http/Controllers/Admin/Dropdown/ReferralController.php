<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('admins')->user()->hasPermission('referrals.show')) {
            $hasPermission = [
                'referral_create' => Auth::guard('admins')->user()->hasPermission('referrals.create'),
            ];

            return view('admin.dropdown.referrals.index', compact('hasPermission'));
        } else {
            abort(404);
        }
    }
}
