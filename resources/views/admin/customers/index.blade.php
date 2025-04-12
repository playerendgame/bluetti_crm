@extends('layouts.admin.main')

@section('current_page')
Customers
@endsection

@section('content')

<div id="app" class="col-md-12">
    <customers-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection