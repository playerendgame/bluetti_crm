@extends('layouts.admin.main')

@section('current_page')
Summary
@endsection

@section('content')

<div id="app" class="col-md-12">
    <retails-report-summary-component :filters="{{$filters}}" />
</div>

@endsection