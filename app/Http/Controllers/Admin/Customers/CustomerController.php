<?php

namespace App\Http\Controllers\Admin\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Customer;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $hasPermission = [
            'customer_create' => Auth::guard('admins')->user()->hasPermission('customer.create'),
        ];
        return view('admin.customers.index', compact('hasPermission')); 
    }

    public function create(Request $request)
    {
        return view('admin.customers.create');
    }

    public function show(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->created_at_s = Carbon::parse($customer->created_at)->toDayDateTimeString();

        return view('admin.customers.show', compact('customer'));
    }

    public function dashboard(Request $request)
    {
        if (Auth::guard('admins')->user()->hasPermission('customers.show')) {
            return view('admin.customers.dashboard');
        } else {
            abort(404);
        }
    }

}
