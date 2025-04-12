@extends('layouts.admin.main')

@section('current_page')
Summary Report
@endsection

@section('content')

<div id="app" class="col-md-12">
    <report-summary-component />
</div>

@endsection