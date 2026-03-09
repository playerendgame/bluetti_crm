<?php

namespace App\Http\Controllers\Admin\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quotations;

class QuotationController extends Controller
{
    public function view($id)
    {
        try {
          
            $quotationId = (int) $id;
            if ($quotationId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid quotation ID'
                ], 400);
            }
            
            $quotation = Quotations::with(['customers', 'quotation_products.products'])
                ->findOrFail($quotationId);
            
            return response()->json([
                'success' => true,
                'quotation' => $quotation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching quotation: ' . $e->getMessage()
            ], 404);
        }
    }

    public function showPage($id)
    {
        $quotationId = (int) $id;
        if ($quotationId <= 0) {
            abort(404);
        }
        
        return view('admin.customers.view_quotation', ['quotationId' => $quotationId]);
    }
}