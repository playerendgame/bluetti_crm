@extends('layouts.admin.main')

@section('current_page')
Targets
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-target-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection