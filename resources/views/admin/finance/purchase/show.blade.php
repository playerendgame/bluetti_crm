@extends('layouts.admin.main')

@section('current_page')
Purchase Order
@endsection

@section('content')

<div id="app" class="col-md-12">
    <show-purchase-order-component :purchase="{{ $purchase }}"/>
</div>

@endsection