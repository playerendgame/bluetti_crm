@extends('layouts.admin.main')

@section('current_page')
Distribution Channels
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-distribution-channels-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection