@extends('layouts.admin.main')

@section('current_page')
Dashboard
@endsection

@section('content')

<div class="row">
  <div id="app" class="col-md-12">
    <admin-dashboard />
  </div>
</div>

@endsection