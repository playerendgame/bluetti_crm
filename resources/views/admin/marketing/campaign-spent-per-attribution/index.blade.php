@extends('layouts.admin.main')

@section('current_page')
Campaign Spent Per Attribution
@endsection

@section('content')


<div id="app" class="col-md-12">
    @if (Auth::guard('admins')->user()->hasPermission('campaign-spent.create'))
        <div class="col-md-3">
            <div class="button">
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#file_input">
                    <span class="d-flex pt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                        </svg>
                        &nbsp;
                        <h6>Import File</h6>
                    </span>
                </button>
            </div>

            <div class="modal fade" id="file_input">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="file_input">Import Ads File</h1>
                        </div>
                        <div class="modal-body">
                            <form class="ads_file_input" role="form" method="POST" action="{{ route('admin.marketing.campaign-spent-per-attribution.import')}}" enctype="multipart/form-data">
                                @csrf
                                <label class="form-label">File Input</label>
                                <input class="form-control" type="file" class="custom-file-input" id="import_file" name="import_file">
                                
                                <div class="button pt-3">
                                    <button type="submit" class="btn btn-primary">Import</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <br>
    <marketing-campaign-spent-per-attribution-component />
</div>

@endsection
