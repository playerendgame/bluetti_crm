@extends('layouts.admin.main')

@section('current_page')
Roles
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-role-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection