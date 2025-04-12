@extends('layouts.admin.main')

@section('current_page')
Purchase Order
@endsection

@section('content')

<div id="app" class="col-md-12">
    @if (Auth::guard('admins')->user()->hasPermission('purchase.create'))
    <a href="{{ route('admin.finance.purchase.create')}}">
        <button class="btn btn-primary">Add</button>
    </a>
    @endif
    <finance-purchase-order-component />
</div>

@endsection