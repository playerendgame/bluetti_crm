<?php

namespace App\Http\Controllers\Admin\Dropdown;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class DistributionChannelsController extends Controller
{
    public function index(){

        $hasPermission = [
            'dist_channel_update' => Auth::guard('admins')->user()->hasPermission('dist_channel.update'),
        ];

        return view('admin.dropdown.distribution_channels.index', compact('hasPermission'));

    }
}
