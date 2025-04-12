<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function index(){

        return view('admin.marketing.qr-generator.index');

    }
}
