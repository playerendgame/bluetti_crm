<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModeOfPaymentOrdersController extends Controller
{
    public function index(){

        return view('admin.reports.mode-of-payment-orders.index');

    }
}
