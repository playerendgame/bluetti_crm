@extends('layouts.admin.main')

@section('current_page')
Provinces
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-province-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection