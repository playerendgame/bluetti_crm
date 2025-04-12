@extends('layouts.admin.main')

@section('current_page')
My Orders
@endsection

@section('content')

<div id="app" class="col-md-12">
    <my-orders-component />
</div>

@endsection