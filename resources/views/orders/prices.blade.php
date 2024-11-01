@extends('layouts.app')
@isset($order)
    @section('title', 'Edit Order')
@else
@section('title', 'Add New Order')
@endisset
@section('content')
<style>
    label {
        margin-bottom: 0 !important
    }

    h5::after {
        display: block;
        content: '';
        margin-top: 5px;
        border-bottom: 1px solid #000;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                @isset($order)
                    <h4 class="mb-sm-0 font-size-18">Edit Order</h4>
                @else
                    <h4 class="mb-sm-0 font-size-18">Add New Order</h4>
                @endisset

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                        @isset($order)
                            <li class="breadcrumb-item active">Edit Order</li>
                        @else
                            <li class="breadcrumb-item active">Add New Order</li>
                        @endisset
                    </ol>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="container">
    @isset($order)
        <form action="" method="post" enctype="multipart/form-data">
        @else
            <form action="{{ route('manual.order.post') }}" method="post" enctype="multipart/form-data">
            @endisset
            @csrf


                 
                <div class="form-group col-sm-12 mb-2 mt-2">
                    <input type="submit" value="Submit" class="btn btn-primary">
                </div>

            </div>
        </form>
</div>

@endsection
