@extends('layouts.admin.main')

@section('current_page')
Orders Per Distribution Channel
@endsection

@section('content')

<div id="app" class="col-md-12">
    <report-orders-per-distribution-channel-component />
</div>

@endsection