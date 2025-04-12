<?php

namespace App\Http\Controllers\Admin\Retail\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class StoreController extends Controller
{
    public function index()
    {
        return view('admin.retails.dropdown.stores.index');
    }
}
