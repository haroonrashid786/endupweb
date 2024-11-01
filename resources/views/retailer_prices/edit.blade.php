@extends('layouts.app')
@isset($price)
    @section('title', 'Edit Retailer Price')
@else
    @section('title', 'Add New Retailer Price')
@endisset
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    @isset($price)
                        <h4 class="mb-sm-0 font-size-18">Edit Retailer Price</h4>
                    @else
                        <h4 class="mb-sm-0 font-size-18">Add New Retailer Price</h4>
                    @endisset
                    {{--                {{ $errors }}--}}
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Retailer Prices</li>
                            @isset($price)
                                <li class="breadcrumb-item active">Edit Retailer Price</li>
                            @else
                                <li class="breadcrumb-item active">Add New Retailer Price</li>
                            @endisset
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid cShadow card p-4 rounded">
        @isset($price)
            <form action="{{ route('update.retailer.price', $price->id) }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ route('insert.retailer.price') }}" method="post" enctype="multipart/form-data">
                        @endisset
                        @csrf


                        <div class="row">
                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Retailer<span class="text-danger">*</span></label>
                                <select name="retailer_id" class="form-select" required id="">
                                    <option value="">Select Retailer</option>
                                    @foreach($retailers as $r)
                                        <option @if(isset($price) && $r->id == $price->retailer_id) selected @endif value="{{$r->id}}">{{ $r->website }}</option>
                                    @endforeach
                                </select>
                                @error('retailer_id')
                                <span class="invalid-feedback" @error('retailer_id')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Discount</label>
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control" name="extra_discount_percentage" @isset($price)value="{{$price->extra_discount_percentage}}" @endisset min="0" value="0" step="any">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                                @error('extra_discount_percentage')
                                <span class="invalid-feedback" @error('extra_discount_percentage')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Surcharge</label>
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control" name="extra_surcharge" @isset($price)value="{{$price->extra_surcharge}}" @endisset min="0" value="0" step="any">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                </div>
                                @error('extra_surcharge')
                                <span class="invalid-feedback" @error('extra_surcharge')style="display: block" @enderror role="alert">
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
