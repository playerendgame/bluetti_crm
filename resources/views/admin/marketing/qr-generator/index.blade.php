@extends('layouts.admin.main')

@section('current_page')
QR Code Generator
@endsection

@section('content')

<div id="app" class="col-md-12">
    
    <qr-generator-component />
    
</div>

@endsection