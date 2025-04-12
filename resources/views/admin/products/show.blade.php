@extends('layouts.admin.main')

@section('current_page')
Product
@endsection

@section('content')

<div id="app" class="col-md-12">
    {{-- <show-products-component /> --}}
    <show-product-component :product="{{$product}}" />
</div>

@endsection