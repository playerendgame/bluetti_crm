@extends('layouts.admin.main')

@section('current_page')
Orders Per Product
@endsection

@section('content')

<div id="app" class="col-md-12">
    <report-orders-per-product-component />
</div>

@endsection