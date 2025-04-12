@extends('layouts.admin.main')

@section('current_page')
Orders Overview Report
@endsection

@section('content')

<div id="app" class="col-md-12">
    <report-orders-overview-component />
</div>

@endsection