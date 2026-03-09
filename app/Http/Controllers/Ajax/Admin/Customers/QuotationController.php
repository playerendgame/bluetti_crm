<?php

namespace  App\Http\Controllers\Ajax\Admin\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quotations;
use App\Models\QuotationProducts;
use App\Models\Customer;
use App\Models\Product;

class QuotationController extends Controller
{
    public function index($customerId)
    {
        try {
            $quotations = Quotations::with(['quotation_products.products'])
                ->where('customer_id', $customerId)
                ->get();
            
            return response()->json([
                'success' => true,
                'quotations' => $quotations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching quotations: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $customerId)
    {
        try {
            $request->validate([
                'description' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.discount' => 'required|numeric|min:0'
            ]);

            
            $quotation = new Quotations();
            $quotation->customer_id = $customerId;
            $quotation->reference_number = '#' . rand(10000, 99999); 
            $quotation->description = $request->description;
            $quotation->save();

            foreach ($request->items as $item) {
                $quotationProduct = new QuotationProducts();
                $quotationProduct->quotations_id = $quotation->id;
                $quotationProduct->product_id = $item['product_id'];
                $quotationProduct->quantity = $item['quantity'];
                $quotationProduct->price = $item['price'];
                $quotationProduct->discount = $item['discount'];
                $quotationProduct->save();
            }

            $quotation->load(['customers', 'quotation_products.products']);

            return response()->json([
                'success' => true,
                'message' => 'Quotation created successfully',
                'quotation' => $quotation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating quotation: ' . $e->getMessage()
            ], 500);
        }
    }
}