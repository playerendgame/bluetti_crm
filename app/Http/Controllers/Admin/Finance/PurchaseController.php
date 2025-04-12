<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.finance.purchase.index');
    }

    public function create(Request $request)
    {
        return view('admin.finance.purchase.create');
    }

    public function show(Request $request, $id)
    {
        $purchase = Purchase::with(['purchase_orders'])->findOrfail($id);
        $purchase->created_at_s = Carbon::parse($purchase->created_at)->format("M j, Y");

        return view('admin.finance.purchase.show', compact('purchase'));
    }
}
