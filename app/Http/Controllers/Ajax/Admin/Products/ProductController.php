<?php

namespace App\Http\Controllers\Ajax\Admin\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\PurchaseOrder;
use App\Models\OrderProduct;
use App\Models\ActivityLogs;
use App\Models\Purchase;
use App\Models\Order;
use App\Models\ProductCategory;

class ProductController extends Controller
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

        $query = Product::orderBy($column, $request->order);

        if (isset($input["keyword"])) {
            $query->where('name', 'like', '%' . $input["keyword"] . "%");
        }

        $products = $query->paginate($request->per_page);

        foreach ($products as $product) {
            $purchase_orders = PurchaseOrder::where("product_id", '=', $product->id)->sum('quantity');
            $order_products = OrderProduct::whereHas('order', function ($query) {
                $query->whereNotNull('deleted_at');
            })->where( 'product_id', '=', $product->id)->sum('quantity');
            $product->created_at_s = Carbon::parse($product->created_at)->toDayDateTimeString();
            $product->price_s = "₱ " . number_format($product->price, 2);
            $product->stocks = $purchase_orders > 0 ? $purchase_orders - $order_products : 0;
            $product->category = $product->product_category ? $product->product_category->name : '';
        }

        return array("success" => true, "message" => "", "data" => $products);
    }

    public function create(Request $request)
    {
        $validation_rules = [
            'name' => 'required|unique:products',
            'alt_name' => 'required',
            'price' => 'required',
        ];

        $request->validate($validation_rules);

        $input = $request->all();

        $product = new Product();
        $product->name = $input["name"];
        $product->category_id = $input['category_id'];
        $product->alt_name = $input['alt_name'];
        $product->price = $input["price"];
        $product->save();

        //For the products activity log create
        $this->logActivity('product', $product);

        return array("success" => true, "message" => "Product Created Succesfully!", "data" => null);
    }


    //For the products activity log create
    private function logActivity($activity, $product){

        $admin_id = auth()->guard('admins')->id();
        $activity = 'Created A Product ' . $product->name;

        ActivityLogs::create([
            'source' => 'Product',
            'admin_id' => $admin_id,
            'name' => $product->name,
            'activity' => $activity
        ]);

    }




    public function productApi(Request $request)
    {
        $query = Product::orderBy('name', 'asc');
        $products = $query->get();

        return array('success' => true, "message" => "", "data" => $products);
    }

    public function fetchCategories(){
        $prodCategories = ProductCategory::all();

        return response()->json($prodCategories);
    }



    public function update(Request $request)
    {
        $input = $request->all();

        $product = null;

        if (isset($input["id"])) {
            $product = Product::find($input["id"]);
            if ($product == null) {
                return array("success" => false, "message" => "Can't find Product", "data" => null);
            }
        } else {
            return array("success" => false, "message" => "Admin ID is required!", "data" => null);
        }

        $previous_name = $product->name;//For previous product name - This is for Activity Log
        $product->name = $input["name"];
        $previous_category_id = $product->category_id;
        $product->category_id = $input['category_id'];
        $previous_price = $product->price;//For previous product name - This is for Activity Log
        $product->price = $input["price"];
        $previous_altName = $product->alt_name;//For previous product Alt Name - This is for Activity Log
        $product->alt_name = $input['alt_name'];
        $product->save();

        $this->updateLogActivity('product', $product, $previous_name, $previous_price, $previous_altName, $previous_category_id);

        return array("success" => true, "message" => "Product Updated Succesfully!", "data" => null);
    }


     //For the update product activity log update
     private function updateLogActivity($activity, $product, $previous_name, $previous_price, $previous_altName, $previous_category_id){

        $admin_id = auth()->guard('admins')->id();
        $activity = '';
        $changes = [];

        if(strcmp($product->name, $previous_name) !== 0){
            $changes[] = 'Updated A Product Name From ' . $previous_name . ' to ' . $product->name;
        }
        if(strcmp($product->category_id, $previous_category_id) !== 0){
            $changes[] = 'Updated A Product Category From ' . $previous_category_id . ' to ' . $product->category_id;
        }
        if(strcmp($product->alt_name, $previous_altName ) !== 0){
            $changes[] = 'Updated A Product Alternative Name From ' . $previous_altName . ' to ' . $product->alt_name;
        }
        if(strcmp($product->price, $previous_price) !== 0){
            $changes[] = 'Updated A Product Price From ' . $previous_price . ' to ' . $product->price;
        }
        if(count($changes) > 0){
            $activity = implode(' and ', $changes);
        }

        ActivityLogs::create([
            'source' => 'Product',
            'admin_id' => $admin_id,
            'name' => $product->name,
            'activity' => $activity
        ]);
    
    }



    public function showPurchaseOrder(Request $request, $id)
    {
        $column = $request->column;
        $input = $request->all();
        $purchase_orders = []; // Initialize $purchase_orders

        if (strcmp($column, "created_at_s") == 0) {
            $column = "created_at";
        }

        $product = Product::find($id);
        
        if ($product) {
            $query = PurchaseOrder::where('product_id', '=', $product->id)->orderBy($column, $request->order);
            $purchase_orders = $query->paginate($request->per_page);
            foreach ($purchase_orders as $purchase_order) {
                $remainingQuantities = $purchase_order->quantity;

                $purchase = Purchase::where('id', '=', $purchase_order->purchase_id)->first();
                $purchase_order->created_at_s = Carbon::parse($purchase_order->created_at)->toDayDateTimeString();
                $purchase_order->po_id = $purchase != null && $purchase->ref_code != null ? $purchase->ref_code : "";
                $purchase_order->purchase_date_s = $purchase != null && $purchase->purchase_date != null ? Carbon::parse($purchase->purchase_date)->format("M j, Y") : "";
                $purchase_order->cogs_s = $purchase_order->distributor_price != null ? "₱ " . number_format($purchase_order->distributor_price, 2) : "₱ 0.00";
                $purchase_order->total_cost = "₱ " . number_format(($purchase_order->quantity * $purchase_order->distributor_price), 2);
            }
        }

        return array("success" => true, "message" => "", "data" => $purchase_orders);
    }

    // public function delete(Request $request)
    // {
    //     $product = Product::with('orderProducts')->find($request->input('id'));        
    //     if ($product) {
    //         $product->load('orderProducts');
    //         if ($product->orderProducts->count() > 0) {
    //             return array("success" => false, "message" => "Cannot delete product, it has associated orders.");
    //         }
    //         $product->delete();
    //         return array("success" => true, "message" => "Product deleted successfully!");
    //     } else {
    //         return array("success" => false, "message" => "Failed to delete product.");
    //     }
    // }

    public function delete(Request $request)
    {
        $product = Product::find($request->input('id'));
        if ($product) {
            $orderProducts = OrderProduct::where('product_id', $product->id);
            $orders = $orderProducts->pluck('order_id');
            
         
            $allOrders = Order::whereIn('id', $orders)->get();
            
            
            $allOrdersSoftDeleted = $allOrders->filter(function($order) {
                return $order->trashed();
            })->count() === $allOrders->count();
            
            if ($allOrdersSoftDeleted) {
                
                $product->delete();
                return array("success" => true, "message" => "Product deleted successfully!");
            } else {
                return array("success" => false, "message" => "Cannot delete product, it has associated orders that are not deleted.");
            }
        } else {
            return array("success" => false, "message" => "Failed to delete product.");
        }
    }
    
    
    
}
