@extends('layouts.admin.main')

@section('current_page')
Mode of Payments
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-mode-of-payment-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection