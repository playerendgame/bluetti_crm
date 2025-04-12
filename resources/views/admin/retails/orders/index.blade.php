@extends('layouts.admin.main')

@section('current_page')
Retail Orders
@endsection

@section('content')

<div id="app" class="col-md-12">
    <a href="{{ route('admin.retails.order.create')}}">
        <button class="btn btn-primary">Add</button>
    </a>
    <retails-order-component />
</div>

@endsection