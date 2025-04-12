@extends('layouts.admin.main')

@section('current_page')
Dashboard
@endsection

@section('content')

<div id="app" class="col-md-12">
    <customers-dashboard-component />
</div>

@endsection