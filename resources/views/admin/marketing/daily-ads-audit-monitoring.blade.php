@extends('layouts.admin.main')

@section('current_page')
Daily Ads Audit Monitoring
@endsection

@section('content')

<div id="app" class="col-md-12">
    {{-- <a href="{{ route('admin.orders.create')}}"> --}}
        <button class="btn btn-primary">Add</button>
    {{-- </a> --}}
    <marketing-daily-ads-audit-component />
</div>

@endsection