@extends('layouts.admin.main')

@section('current_page')
Couriers
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-courier-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection