@extends('layouts.app')
@isset($list)
    @section('title', 'Edit Shipping Charges')
@else
    @section('title', 'Add New Shipping Charges')
@endisset
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 ps-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Shipping Charges</li>
                        @isset($list)
                            <li class="breadcrumb-item active">Edit Shipping Charges</li>
                        @else
                            <li class="breadcrumb-item active">Add New Shipping Charges</li>
                        @endisset
                    </ol>
                </div>
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    @isset($list)
                        <h4 class="mb-sm-0 font-size-18">Edit Shipping Charges</h4>
                    @else
                        <h4 class="mb-sm-0 font-size-18">Add New Shipping Charges</h4>
                    @endisset
                    {{--                {{ $errors }}--}}


                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid cShadow card p-4 rounded">
        @isset($list)
            <form action="{{ route('update.currency', $currency->id) }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ route('insert.charges') }}" method="post" enctype="multipart/form-data">
                        @endisset
                        @csrf


                        <div class="row">

                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Name<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input required type="text" class="form-control" name="name" @isset($list)value="{{strtoupper($list->name)}}" @endisset placeholder="Enter Name">
                                </div>
                                @error('name')
                                <span class="invalid-feedback mt-0" @error('name')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-sm-6 mb-2 d-flex align-items-end">

                                <label for="switch4" data-on-label="Yes" data-off-label="No">
                                    <label for="">Status: </label>
                                    <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">

                                        <input class="form-check-input" name="active" type="checkbox" id="SwitchCheckSizelg" @if(isset($list) && $list->active == 1) checked="" @endif>
                                    </div>
                                </label>
                            </div>


                            <div class="form-group col-sm-12 mb-2">
                                <input type="submit" value="Submit" class="btn btn-primary btn-sm">
                            </div>

                        </div>
                    </form>
    </div>
@endsection
