@extends('layouts.admin.main')

@section('current_page')
Regions
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-region-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection