@extends('layouts.admin.main')

@section('current_page')
Orders
@endsection

@section('content')

<div id="app" class="col-md-12">

    {{-- <div class="row">
        <div class="col-md-12 mb-3">
            <div class="d-flex justify-content-center float-right">
                <form action="{{ route('admin.orders.destroy', ['id' => $order->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                
                    <div class="justify-content-center float-right">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</button>
                    </div>
                </form>

            </div>
        </div>
    </div> --}}
   
    <show-orders-component :order="{{ $order }}" :customers="{{ $customers }}" :attribution="{{ $attribution }}" :admin="{{ $admin }}" :region="{{ $region }}" :province="{{ $province }}" :city="{{ $city }}"  :has-permission="{{ json_encode($hasPermission) }}" />

    </div>

@endsection