@extends('layouts.app')
@isset($item)
    @section('title', 'Edit Item')
@else
    @section('title', 'Add New Item')
@endisset
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 ps-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item ">Shipping Charges</li>
                        <li class="breadcrumb-item ">items</li>
                        @isset($item)
                            <li class="breadcrumb-item active">Edit Item</li>
                        @else
                            <li class="breadcrumb-item active">Add New Item</li>
                        @endisset
                    </ol>
                </div>
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    @isset($item)
                        <h4 class="mb-sm-0 font-size-18">Edit Item</h4>
                    @else
                        <h4 class="mb-sm-0 font-size-18">Add New Item</h4>
                    @endisset
                    {{--                {{ $errors }}--}}


                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid cShadow card p-4 rounded">
        @isset($item)
            <form action="{{ route('update.charges.items', $item->id) }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ route('insert.charges.items', $parent_id) }}" method="post" enctype="multipart/form-data">
                        @endisset
                        @csrf


                        <div class="row">

                            <!-- <div class="form-group col-sm-6 mb-2">
                                <label for="">Min Volumetric Weight<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="min_volumetric_weight" @isset($item)value="{{strtoupper($item->min_volumetric_weight)}}" @endisset placeholder="Enter Min Volumetric Weight">
                                </div>
                                @error('min_volumetric_weight')
                                <span class="invalid-feedback mt-0" @error('min_volumetric_weight')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Max Volumetric Weight<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="max_volumetric_weight" @isset($item)value="{{strtoupper($item->max_volumetric_weight)}}" @endisset placeholder="Enter Max Volumetric Weight">
                                </div>
                                @error('max_volumetric_weight')
                                <span class="invalid-feedback mt-0" @error('max_volumetric_weight')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> -->
                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Price<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input required type="number" step="any" class="form-control" name="price" @isset($item)value="{{strtoupper($item->price)}}" @endisset placeholder="Enter Price">
                                </div>
                                @error('price')
                                <span class="invalid-feedback mt-0" @error('price')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-6 mb-2">
                            <label for="">Package<span class="text-danger">*</span></label>
                            <select name="shopify_package_id" required class="form-select" id="">
                            <option value="">Select Package (if apply)</option>
                            @foreach($packages as $p)
                            <option @if(isset($item) && $item->shopify_package_id == $p->id) selected @endif value="{{$p->id}}">{{ $p->name }}</option>
                            @endforeach
                            </select>
                            </div>

                            <div class="form-group col-sm-12 mb-2">
                                <input type="submit" value="Submit" class="btn btn-primary btn-sm">
                            </div>

                        </div>
                    </form>
    </div>
@endsection
