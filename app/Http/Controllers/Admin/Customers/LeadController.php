<?php

namespace App\Http\Controllers\Admin\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class LeadController extends Controller
{
    public function leads(Request $request){

        $hasPermission = [
            'customer_create' => Auth::guard('admins')->user()->hasPermission('customer.create'),
        ];
        return view('admin.customers.customer_index', compact('hasPermission')); 
    }
}
