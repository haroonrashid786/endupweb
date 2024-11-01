@extends('layouts.app')
@isset($price)
    @section('title', 'Edit Price')
@else
    @section('title', 'Add New Price')
@endisset
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    @isset($price)
                        <h4 class="mb-sm-0 font-size-18">Edit Price</h4>
                    @else
                        <h4 class="mb-sm-0 font-size-18">Add New Price</h4>
                    @endisset
                    {{--                {{ $errors }}--}}
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Prices</li>
                            @isset($price)
                                <li class="breadcrumb-item active">Edit Price</li>
                            @else
                                <li class="breadcrumb-item active">Add New Price</li>
                            @endisset
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="container">
        @isset($price)
            <form action="{{ route('update.currency', $price->id) }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ route('insert.price') }}" method="post" enctype="multipart/form-data">
                        @endisset
                        @csrf


                        <div class="row">

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">City From</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="city_from" @isset($price)value="{{strtoupper($price->city_from)}}" @endisset placeholder="Enter City From">
                                </div>
                                @error('city_from')
                                <span class="invalid-feedback mt-0" @error('city_from')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Country From</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="country_from" @isset($price)value="{{strtoupper($price->country_from)}}" @endisset placeholder="Enter Country From">
                                </div>
                                @error('country_from')
                                <span class="invalid-feedback mt-0" @error('country_from')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Postal Code From</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="postal_code_from" @isset($price)value="{{strtoupper($price->postal_code_from)}}" @endisset placeholder="Enter Postal Code From">
                                </div>
                                @error('postal_code_from')
                                <span class="invalid-feedback mt-0" @error('postal_code_from')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">City To</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="city_to" @isset($price)value="{{strtoupper($price->city_to)}}" @endisset placeholder="Enter City To">
                                </div>
                                @error('city_to')
                                <span class="invalid-feedback mt-0" @error('city_to')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Country To</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="country_to" @isset($price)value="{{strtoupper($price->country_to)}}" @endisset placeholder="Enter Country To">
                                </div>
                                @error('country_to')
                                <span class="invalid-feedback mt-0" @error('country_to')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Postal Code To</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="postal_code_to" @isset($price)value="{{strtoupper($price->postal_code_to)}}" @endisset placeholder="Enter Postal Code To">
                                </div>
                                @error('postal_code_to')
                                <span class="invalid-feedback mt-0" @error('postal_code_to')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Volumetric Weight</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="volumetric_weight" @isset($price)value="{{strtoupper($price->volumetric_weight)}}" @endisset placeholder="Enter Volumetric Weight">
                                </div>
                                @error('volumetric_weight')
                                <span class="invalid-feedback mt-0" @error('volumetric_weight')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Length</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="length" @isset($price)value="{{strtoupper($price->length)}}" @endisset placeholder="Enter Length">
                                </div>
                                @error('length')
                                <span class="invalid-feedback mt-0" @error('length')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Height</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="height" @isset($price)value="{{strtoupper($price->height)}}" @endisset placeholder="Enter Height">
                                </div>
                                @error('height')
                                <span class="invalid-feedback mt-0" @error('height')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Width</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="width" @isset($price)value="{{strtoupper($price->width)}}" @endisset placeholder="Enter Width">
                                </div>
                                @error('width')
                                <span class="invalid-feedback mt-0" @error('width')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Quantity Box</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="quantity_box" @isset($price)value="{{strtoupper($price->quantity_box)}}" @endisset placeholder="Enter Quantity Box">
                                </div>
                                @error('quantity_box')
                                <span class="invalid-feedback mt-0" @error('quantity_box')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Price</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="price" @isset($price)value="{{strtoupper($price->price)}}" @endisset placeholder="Enter Price">
                                </div>
                                @error('price')
                                <span class="invalid-feedback mt-0" @error('price')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-sm-4 mb-2">
                                <label for="">Currency</label>
                                <div class="input-group">
                                    <select class="form-select" name="currency">
                                        <option>Select</option>
                                        @foreach($currencies as $c)
                                            <option value="{{$c->id}}">{{ $c->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('currency')
                                <span class="invalid-feedback mt-0" @error('currency')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

{{--                            <div class="form-group col-sm-4 mb-2">--}}
{{--                                <label for="">Shipping Terms</label>--}}
{{--                                <div class="input-group">--}}
{{--                                    <select class="form-select" name="shipping_terms">--}}
{{--                                        <option>Select</option>--}}
{{--                                        @foreach($terms as $t)--}}
{{--                                            <option id="{{$t->id}}">{{ $t->code }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                                @error('currency')--}}
{{--                                <span class="invalid-feedback mt-0" @error('currency')style="display: block" @enderror role="alert">--}}
{{--                                        <strong>{{ $message }}</strong>--}}
{{--                                    </span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}


                            <div class="form-group col-sm-12 mb-2">
                                <input type="submit" value="Submit" class="btn btn-primary btn-sm">
                            </div>

                        </div>
                    </form>
    </div>
@endsection
