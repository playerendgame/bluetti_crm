@extends('layouts.admin.main')

@section('current_page')
Products
@endsection

@section('content')

<div id="app" class="col-md-12">
    @if (Auth::guard('admins')->user()->hasPermission('product.create'))
    <a href="{{ route('admin.products.create')}}">
        <button class="btn btn-primary">Add</button>
    </a>
    @endif
    <products-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection