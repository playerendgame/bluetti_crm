@extends('layouts.admin.main')

@section('current_page')
Admins
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-admin-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection