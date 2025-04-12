<?php

namespace App\Http\Controllers\Ajax\Admin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\OrderProduct;
use App\Models\Target;
use App\Models\Courier;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function list(Request $request)
    {
        $input = $request->all();
        $column = $request->column;
        if (strcmp($column, "order_date_s") == 0) {
            $column = "order_date";
        } else if (strcmp($column, "target_delivery_date_s") == 0) {
            $column = "target_delivery_date";
        }

        $query = Order::orderBy($column, $request->order);

        if (isset($input["start_date"]) && $input["end_date"] != null) {
            $query->whereBetween('target_delivery_date', [Carbon::parse($input["start_date"])->startOfDay(), Carbon::parse($input["end_date"])->endOfDay()]);
        }

        if (isset($input["keyword"])) {
            $query->where(function ($query) use ($input) {
                $query->where("order_number", "like", "%" . $input["keyword"] . "%");
                $query->orWhereHas('customers', function ($query) use ($input) {
                    $query->where('name', "like", "%" . $input["keyword"] . "%");
                });
            });
        }

        $orders = $query->paginate($request->per_page);

        foreach ($orders as $order) {
            $order_products = OrderProduct::where('order_id', '=', $order->id)->get();
            $order->target_delivery_date_s = Carbon::parse($order->target_delivery_date)->format("M j, Y");
            $order->order_date_s = Carbon::parse($order->order_date)->format("M j, Y");
            $order->items = $order_products->sum(function ($order_product) {
                $total_orders = $order_product->price != 0 ? $order_product->quantity : 0;
                return $total_orders;
            });
            $order->customer_name = $order->customers->name;
            $total_price = $order_products->sum(function ($order_product) {
                return ($order_product->quantity * $order_product->price) - ($order_product->quantity * $order_product->discount);
            });
            
            $order->total_price = "₱ " . number_format($total_price, 2);
            $order->mark_as_paid_s = $order->markAsPaid();
        }

        return array("success" => true, "message" => "", "data" => $orders);
    }


    public function regularDispatchList(Request $request)
    {
        $input = $request->all();
        $column = $request->column;
        if (strcmp($column, "order_date") == 0) {
            $column = "order_date";
        }

        $query = Order::orderBy($column, $request->order);

        //for the date range
        if (isset($input["start_date"]) && !empty($input["start_date"]) && 
            isset($input["end_date"]) && !empty($input["end_date"])) {
            $query->whereBetween('dispatch_date', [Carbon::parse($input["start_date"])->startOfDay(), Carbon::parse($input["end_date"])->endOfDay()]);
        }

        //for the search keyword
        if (isset($input["keyword"])) {
            $query->where(function ($query) use ($input) {
                $query->where("order_number", "like", "%" . $input["keyword"] . "%");
                $query->orWhereHas('customers', function ($query) use ($input) {
                    $query->where('name', "like", "%" . $input["keyword"] . "%");
                });
            });
        }

        //for the courier select
        if (isset($input["courier_id"]) && $input["courier_id"] > 0) {
            $query->whereHas('courier', function ($query) use ($input) {
                $query->where('id', $input["courier_id"]);
            });
        }

    
        $orders = $query->paginate($request->per_page);
        foreach ($orders as $order) {
            $order_products = OrderProduct::where('order_id', '=', $order->id)->get();
            $order->order_date = Carbon::parse($order->order_date)->format("M j, Y");
            $order->order_number = $order->order_number;
            $order->order_courier = $order->courier ? $order->courier->name : 'N/A';
            $order->customer_name = $order->customers ? $order->customers->name : 'N/A';
            $order->complete_address = $order->address;

            /*Fetches total amount of order*/
            $total_price = $order_products->sum(function ($order_product) {
                return ($order_product->quantity * $order_product->price) - $order_product->discount;
            });
            $order->total_price = "₱ " . number_format($total_price, 2);
            /** */

            /*Fetches products of specific order */

            // foreach ($sv->sv_in_charges as $svInCharge) {
            //     $svAdmin .= ($svInCharge->fullName() . ", ");
            // }
            // if (strlen($svAdmin > 0)) {
            //     $svAdmin = substr($svAdmin, 0, strlen($svAdmin) - 2);
            // }
            // $products = [];
            // foreach ($order_products as $order_product) {
            //     $products .= ($order_product->product->name . "\n");
            //     // if ($order_product->product) {
            //     //     $products[] = $order_product->product->name . ' x' . $order_product->quantity;
            //     // }
            // }
            // // $order->product_name = implode("\n", $products);
            // if (strlen($products > 0)) {
            //     $$order->product_name = substr($products, 0, strlen($products) - 2);
            // }

            $products = ''; // Initialize as a string instead of an array

            foreach ($order_products as $order_product) {
                if ($order_product->product) {
                    $products .= $order_product->product->name . ' x ' . $order_product->quantity . ", "; 
                }
            }

            // Remove the last newline character
            if (strlen($products) > 0) {
                $order->product_name = substr($products, 0, -1);
            } else {
                $order->product_name = ''; // Ensure it's an empty string if no products
            }
            /** */

            $order->contact_number = $order->contact_number;
            $order->mode_of_payment_s = $order->mode_of_payment ? $order->mode_of_payment->name : 'N/A';
            $order->payment_status = $order->markAsPaid();
        }

        return array("success" => true, "message" => "", "data" => $orders);
    }

    public function fetchCouriers(){

        $couriers = Courier::all();

        return array("success" => true, "message" => "", "data" => $couriers);

    }

    public function dashboardSummary(Request $request)
    {
        $validation_rules = [
            "start_date" => 'required',
            "end_date" => 'required',
        ];

        $request->validate($validation_rules);
        $input = $request->all();

        $start_month = Carbon::parse($input["start_date"])->startOfDay();
        $end_month = Carbon::parse($input["end_date"])->endOfDay();
        $tableArray = array();

        $totalGrossSales = 0;
        $totalActualSales = 0;
        $totalCogs = 0;

        for ($i = 0; $i < $start_month->diffInMonths($end_month)+1; $i++) {
            $currentMonth = $start_month->copy()->addMonth($i);
            $startOfMonth = $currentMonth->copy()->startOfMonth();
            $endOfMonth = $currentMonth->copy()->endOfMonth();

            if ($currentMonth->isSameMonth($start_month)) {
                $count_days = $start_month->diffInDays($endOfMonth) + 1;
            } elseif ($currentMonth->isSameMonth($end_month)) {
                $count_days = $end_month->diffInDays($startOfMonth) + 1;
            } else {
                $count_days = $currentMonth->daysInMonth;
            }

            $orderProducts = OrderProduct::whereHas('order', function ($query) use ($currentMonth) {
                $query->whereNull('deleted_at')->where('delivery_status', '<>', 6);
                $query->whereYear('order_date', $currentMonth->copy()->year)->where('order_date', ">=", $currentMonth->copy()->startOfMonth())->where('order_date', "<=", $currentMonth->copy()->endOfMonth());
            })->get();
        
            $gross_sales = $orderProducts->sum(function ($orderProduct) {
                return $orderProduct->quantity * $orderProduct->price;
            });

            $actual_sales = $orderProducts->sum(function ($orderProduct) {
                return ($orderProduct->quantity * $orderProduct->price) - $orderProduct->discount;
            });

            $cogs = OrderProduct::whereHas('order', function ($query) use ($currentMonth) {
                $query->whereNull('deleted_at')->where('delivery_status', '<>', 6);
                $query->whereYear('order_date', $currentMonth->copy()->year)->where('order_date', ">=", $currentMonth->copy()->startOfMonth())->where('order_date', "<=", $currentMonth->copy()->endOfMonth());
            })->get();
            $cogs = $cogs->sum(function ($cogs) {
                return $cogs->quantity * $cogs->cogs;
            });

            $today = Carbon::now()->addDays(2);

            $orders = Order::whereYear('order_date', $currentMonth->copy()->year)->where('order_date', ">=", $currentMonth->copy()->startOfMonth())->where('order_date', "<=", $currentMonth->copy()->endOfMonth())->where('delivery_status', '!=', 6)->count();
            $target = Target::whereYear('date', $currentMonth->copy()->year)->whereDate('date', ">=", $currentMonth->copy()->startOfMonth())->whereDate('date', "<=", $currentMonth->copy()->endOfMonth())->sum('sales_target');
            $days = $today->diffInDays($currentMonth->copy()->endOfDay());

            $dataUnit = array(
                "month" => $currentMonth->format("F Y"),
                "count_days" => $count_days,
                "gross_sales" => "₱ " . number_format($gross_sales, 2),
                "actual_sales" => "₱ " . number_format($actual_sales, 2),
                "cogs" => "₱ " . number_format($cogs, 2),
                "conversion" => $actual_sales > 0 ? number_format((($cogs / $actual_sales) * 100), 2) . "%" : "0%",
                "orders" => $orders,
                "daily_ave_order" => $orders > 0 ? number_format(($orders / $count_days), 2) : 0,
                "aov" => $actual_sales > 0 ? "₱ " . number_format(($actual_sales / $orders), 2) : "₱ 0",
                "daily_ave_sales" => $actual_sales > 0 ? "₱ " . number_format(($actual_sales / $count_days), 2) : "₱ 0",
                "sales_target" => $target != null ? "₱ " . number_format($target, 2) : "₱ 0",
                "sales_lag" => "₱ " . number_format(((($actual_sales > 0 ? $actual_sales / $count_days : 0) - ($target / $count_days)) * $days), 2),
            );

            $totalGrossSales += $gross_sales;
            $totalActualSales += $actual_sales;
            $totalCogs += $cogs;

            array_push($tableArray, $dataUnit);
        }

        usort($tableArray, function ($a, $b) {
            return strtotime($b['month']) - strtotime($a['month']);
        });

        $data["table"] = $tableArray;

        $data["total_gross_sales"] = "₱ " . number_format($totalGrossSales, 2);
        $data["total_actual_sales"] = "₱ " . number_format($totalActualSales, 2);
        $data["total_cogs"] = "₱ " . number_format($totalCogs, 2);
        $data["cogs_vat"] = $totalGrossSales > 0 ? number_format((($totalCogs / $totalGrossSales) * 100), 2) . "%" : "0%";

        return array("success" => "true", "message" => "", "data" => $data);
    }

    public function getCustomerProductDetails($id)
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

    public function getTargetDateCustomerOrders($id)
    {
        // Find the order by $id including its order products through eager loading
        $order = Order::with('order_products', 'paymentMethods')->find($id);
    
        if (!$order) {
            return response()->json(['error' => 'Order Not Found'], 404);
        }
    
        //Calculate the total paid amount
        $totalPaid = $order->paymentMethods->sum('amount');

        //For the oustanding balance
        $totalPrice = $order->order_products->sum(function($product) {

            return ($product->pivot->quantity * $product->pivot->price) - $product->pivot->discount;

        });

        //Calculate the outstanding balance
        $outstandingBalance = $totalPrice - $totalPaid;
        
    
        //Map the order products into a format you need
        $orderProducts = $order->order_products->map(function ($product) {
            return [
               //'name' => $product->name,
                'alt_name' => $product->alt_name,
                'quantity' => $product->pivot->quantity,
                'price' => $product->pivot->price,
                'discount' => $product->pivot->discount,
                'total_amount' => ($product->pivot->quantity * $product->pivot->price) - $product->pivot->discount,
            ];
        });
    
        return response()->json([
            'orderProducts' => $orderProducts,
            'totalPaid' => $totalPaid,
            'outstandingBalance' => $outstandingBalance
        ]);
    }

}

