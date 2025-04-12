@extends('layouts.admin.main')

@section('current_page')
Orders
@endsection

@section('content')

<div id="app" class="col-md-12">
    {{-- @if (collect(Auth::guard('admins')->user()->admin_roles)->contains('id', 1)) --}}
    @if(Auth::guard('admins')->user()->hasPermission('orders.create'))
    <a href="{{ route('admin.orders.create')}}">
        <button class="btn btn-primary">Add</button>
    </a>
    @endif
    {{-- @endif --}}
    <orders-component :filters="{{$filters}}" :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection