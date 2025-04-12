@extends('layouts.admin.main')

@section('current_page')
Daily Ads Audit Monitoring
@endsection

@section('content')

<div id="app" class="col-md-12">
    @if (Auth::guard('admins')->user()->hasPermission('daily-aud-ads.create'))
    <a href="{{ route('admin.marketing.daily-ads-audit.create')}}">
        <button class="btn btn-primary">Add</button>
    </a>
    @endif
    <marketing-daily-ads-audit-component />
</div>

@endsection