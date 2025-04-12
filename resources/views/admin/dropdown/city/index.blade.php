@extends('layouts.admin.main')

@section('current_page')
Cities
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-city-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection