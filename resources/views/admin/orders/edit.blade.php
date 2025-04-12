@extends('layouts.admin.main')

@section('current_page')
Edit Order
@endsection

@section('content')

<div id="app" class="col-md-12">
    <edit-orders-component :order="{{$order}}" />
</div>

@endsection