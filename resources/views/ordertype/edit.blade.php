@extends('layouts.app')
@isset($pkgDetail)
@section('title', 'Edit Order Type')
@else
@section('title', 'Add Order Type')
@endisset
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Order Type</li>
                    @isset($pkgDetail)
                    <li class="breadcrumb-item active">Edit Order Type</li>
                    @else
                    <li class="breadcrumb-item active">Add New Order Type</li>
                    @endisset
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                @isset($pkgDetail)
                <h4 class="mb-sm-0 font-size-18">Edit Order Type</h4>
                @else
                <h4 class="mb-sm-0 font-size-18">Add New Order Type</h4>
                @endisset
                {{-- {{ $errors }}--}}


            </div>
        </div>
    </div>
</div>
<div class="container-fluid cShadow card p-4 rounded">
    @isset($pkgDetail)
    <form action="{{ route('ordertype.update', $pkgDetail->id) }}" method="POST" enctype="multipart/form-data">
        @else
        <form action="{{ route('add.new.ordertype') }}" method="POST" enctype="multipart/form-data">
            @endisset
            @csrf


            <div class="row">


                <div class="form-group col-sm-6 mb-2">
                    <label for="">Order Type Name:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" @isset($pkgDetail)value="{{strtoupper($pkgDetail->name)}}" @endisset placeholder="Enter order type name ">
                    </div>
                    @error('height')
                    <span class="invalid-feedback mt-0" @error('height')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group col-sm-6 mb-2">
                    <label for="">Order Type Image (In SVG Format)</label>
                    @if(isset($image))
                    <div class="form-group col-sm-6 mb-2">
                        <img width="200px" height="150px" src="{{ url('OrderTypes/'. $image ) }}" />
                    </div>
                    @else
                    <div class="form-group col-sm-6 mb-2">
                        <div class="input-group">
                            <input type="file" @if(!isset($type) || (isset($type) && is_null($type->image))) required @endif class="form-control" name="image" >
                        </div>
                        @error('image')
                        <span class="invalid-feedback mt-0" @error('image')style="display: block" @enderror role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    @endif
                    @error('height')
                    <span class="invalid-feedback mt-0" @error('height')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-sm-6 mb-2 d-flex align-items-end">

                    <label for="switch4" data-on-label="Yes" data-off-label="No">
                        <label for="">Status: </label>
                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                            <input class="form-check-input" name="active" type="checkbox" id="SwitchCheckSizelg" @if(isset($pkgDetail) && $pkgDetail->active == 1) checked @endif>
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
