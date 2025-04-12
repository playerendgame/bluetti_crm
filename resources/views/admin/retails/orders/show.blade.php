@extends('layouts.admin.main')

@section('current_page')
Orders
@endsection

@section('content')

<div id="app" class="col-md-12">
   
    <retails-order-show-component :order="{{ $order }}"  />

    </div>

@endsection