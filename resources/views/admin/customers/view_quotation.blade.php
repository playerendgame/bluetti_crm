@extends('layouts.admin.main')

@section('current_page')

@endsection

@section('content')
<div id="app" class="col-md-12">
    <customers-view-quotations-component :quotation-id="{{ $quotationId ?? 'null' }}"></customers-view-quotations-component>
</div>
@endsection