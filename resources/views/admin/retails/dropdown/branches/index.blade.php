@extends('layouts.admin.main')

@section('current_page')
Retail Branches
@endsection

@section('content')

<div id="app" class="col-md-12">
    <retails-dropdown-branch-component />
</div>

@endsection