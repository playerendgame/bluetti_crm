<?php

namespace App\Http\Controllers\Ajax\Admin\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\ActivityLogs;
use Carbon\Carbon;
use App\Models\Order;

class CustomerController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;
        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        } else if (strcmp($column, "followup_date_s") == 0) {
            $column = "followup_date";
        }

        $query = Customer::orderBy($column, $request->order);

        if (isset($input["keyword"])) {
            $query->where('name', 'like', '%' . $input["keyword"] . "%");
            $query->orWhere('email', 'like', '%' . $input["keyword"] . "%");
            $query->orWhere('number', 'like', '%' . $input["keyword"] . "%");
        }

        $customers = $query->paginate($request->per_page);

        foreach ($customers as $customer) {
            $customer->created_at_s = Carbon::parse($customer->created_at)->toDayDateTimeString();
        }

        return array("success" => true, "message" => "", "data" => $customers);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required',
            'email' => 'email|nullable|unique:customers',
            'number' => 'nullable|unique:customers',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $customer = new Customer();
        $customer->name = $input["name"];
        $customer->number = $input["number"];
        $customer->email = $input["email"];
        $customer->save();

        //For the customers activity log create
        $this->logActivity('customer', $customer);

        return array("success" => true, "message" => "Customer Created Succesfully!", "data" => null);
    }


     //For the customers activity log create
     private function logActivity($activity, $customer){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Customer ' . $customer->name;

        ActivityLogs::create([
            'source' => 'Customer',
            'admin_id' => $admin_id,
            'name' => $customer->name,
            'activity' => $activity
        ]);

    }


    public function getAllCustomers(Request $request)
    {
        $query = Customer::orderBy('name', 'asc');

        $customers = $query->get();

        return array("success" => true, "message" => "", "data" => $customers);
    }


    public function getCustomerOrders($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer Not Found'], 404);
        }

        $orders = $customer->showOrders()
            ->with('order_products')
            ->get()
            ->map(function ($order) {
                return [
                    'created_at_s' => $order->created_at->format('Y-m-d'),
                    'name' => $order->order_products->pluck('name')->join(', '),
                    'quantity' => $order->order_products->sum('pivot.quantity'),
                    'price' => $order->order_products->sum('pivot.price'),
                    'discount' => $order->order_products->sum('pivot.discount'),
                    'total_amount' => $order->order_products->sum(function ($product) {
                        return $product->pivot->price - $product->pivot->discount;
                    }),
                    'cogs' => $order->order_products->sum('pivot.cogs'),
                    'admin_name' => $order->admin ? $order->admin->name : '',
                ];
            });

        return response()->json($orders);
    }

    public function dashboard(Request $request)
    {
        $input = $request->all();

        $start_date = Carbon::parse($input["start_date"])->startOfDay();
        $end_date = Carbon::parse($input["end_date"])->endOfDay();

        $start_customers = Order::where('order_date', '<', $start_date)->distinct('customer_id')->count();
        $end_customers = Order::whereBetween('order_date', [$start_date, $end_date])->distinct('customer_id')->count();
        $new_customers = Order::whereBetween('order_date', [$start_date, $end_date])
        ->whereNotIn('customer_id', function($query) use ($start_date) {
            $query->select('customer_id')
                  ->from('orders')
                  ->where('order_date', '<', $start_date);
        })->distinct()->count('customer_id');

        $percentage = $start_customers > 0 ? (($end_customers - $new_customers) / $start_customers) * 100 : 0;

        $data["start_customers"] = $start_customers;
        $data["end_customers"] = $end_customers;
        $data["new_customers"] = $new_customers;
        $data["percentage"] = number_format($percentage, 2) . "%";

        return array("success" => true, "message" => "", "data" => $data);
    }

};
