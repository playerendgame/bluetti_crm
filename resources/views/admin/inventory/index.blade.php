@extends('layouts.admin.main')

@section('current_page')
Inventory
@endsection

@section('content')

<div id="app" class="col-md-12">
    <inventory-component />
</div>

@endsection