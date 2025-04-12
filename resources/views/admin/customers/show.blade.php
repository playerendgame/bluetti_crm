@extends('layouts.admin.main')

@section('current_page')
Customer
@endsection

@section('content')

<div id="app" class="col-md-12">
    <view-customers-component :customer="{{$customer}}"><!--:customer={{$customer}} need to declare this key if fetching data from controller-->
</div>

@endsection