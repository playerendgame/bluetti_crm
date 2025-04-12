@extends('layouts.admin.main')

@section('current_page')
Products Category
@endsection

@section('content')

<div id="app" class="col-md-12">
    <products-category-component :has-permission="{{ json_encode($hasPermission)}}"/>
</div>

@endsection