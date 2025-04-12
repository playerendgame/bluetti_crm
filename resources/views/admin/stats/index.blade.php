@extends('layouts.admin.main')

@section('current_page')
My Stats
@endsection

@section('content')

<div id="app" class="col-md-12">
    <mystats-component :filters="{{$filters}}"/>
</div>

@endsection