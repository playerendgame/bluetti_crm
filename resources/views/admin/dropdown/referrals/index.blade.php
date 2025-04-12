@extends('layouts.admin.main')

@section('current_page')
Referrals
@endsection

@section('content')

<div id="app" class="col-md-12">
    <dropdown-referrals-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection