@extends('layouts.admin.main')

@section('current_page')
Funnels
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-funnel-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection