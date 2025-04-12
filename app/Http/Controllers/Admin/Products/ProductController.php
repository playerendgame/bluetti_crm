<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $hasPermission = [
            'product_update' => Auth::guard('admins')->user()->hasPermission('product.update'),
            'product_delete' => Auth::guard('admins')->user()->hasPermission('product.delete'), 
        ];
        return view('admin.products.index', compact('hasPermission'));
    }

    public function create(Request $request)
    {
        return view('admin.products.create');
    }

    public function show(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->price_s = "₱ " . number_format($product->price, 2);
        $product->cogs_s = "₱ " . number_format($product->cogs, 2);

        return view('admin.products.show', compact('product'));
    }
}
