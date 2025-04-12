<?php

namespace App\Http\Controllers\Admin\Retail\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.retails.dropdown.branches.index');
    }
}
