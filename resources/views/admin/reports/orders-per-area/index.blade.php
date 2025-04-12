@extends('layouts.admin.main')

@section('current_page')
Orders Per Area
@endsection

@section('content')

<div id="app" class="col-md-12">
    <report-orders-per-area-component />
</div>

@endsection