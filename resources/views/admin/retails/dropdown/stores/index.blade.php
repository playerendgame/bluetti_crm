@extends('layouts.admin.main')

@section('current_page')
Retail Stores
@endsection

@section('content')

<div id="app" class="col-md-12">
    <retails-dropdown-store-component />
</div>

@endsection