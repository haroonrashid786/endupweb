@extends('layouts.app')
@isset($warehouse)
    @section('title', 'Edit Warehouse')
@else
    @section('title', 'Add New Warehouse')
@endisset
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 ps-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Warehouses</li>
                        @isset($currency)
                            <li class="breadcrumb-item active">Edit Warehouse</li>
                        @else
                            <li class="breadcrumb-item active">Add New Warehouse</li>
                        @endisset
                    </ol>
                </div>
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    @isset($warehouse)
                        <h4 class="mb-sm-0 font-size-18">Edit Warehouse</h4>
                    @else
                        <h4 class="mb-sm-0 font-size-18">Add New Warehouse</h4>
                    @endisset
                    {{--                {{ $errors }}--}}


                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid cShadow card p-4 rounded">
        @isset($warehouse)
            <form action="{{ route('warehouse.update', $warehouse->id) }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ route('warehouse.insert') }}" method="post" enctype="multipart/form-data">
                        @endisset
                        @csrf


                        <div class="row">

                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Name<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" required name="name" @isset($warehouse)value="{{$warehouse->name}}" @endisset placeholder="Enter Name">
                                </div>
                                @error('name')
                                <span class="invalid-feedback mt-0" @error('name')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Coordinates<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" required name="coordinates" @isset($warehouse)value="{{$warehouse->coordinates}}" @endisset placeholder="Enter Coordinates">
                                </div>
                                @error('coordinates')
                                <span class="invalid-feedback mt-0" @error('coordinates')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Address<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    {{-- <input type="text" class="form-control" name="name" @isset($warehouse)value="{{$currency->name}}" @endisset placeholder="Enter Name"> --}}
                                    <textarea name="address" class="form-control" required id="" cols="30" rows="3">@isset($warehouse){{ $warehouse->address }}@endisset</textarea>
                                </div>
                                @error('address')
                                <span class="invalid-feedback mt-0" @error('address')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>



                            <div class="form-group col-sm-12 mb-2">
                                <input type="submit" value="Submit" class="btn btn-primary btn-sm">
                            </div>

                        </div>
                    </form>
    </div>
@endsection
