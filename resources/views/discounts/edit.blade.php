@extends('layouts.app')
@isset($discount)
@section('title', 'Edit Discount')
@else
@section('title', 'Add New Discount')
@endisset
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Discounts</li>
                    @isset($discount)
                    <li class="breadcrumb-item active">Edit Discount</li>
                    @else
                    <li class="breadcrumb-item active">Add New Discount</li>
                    @endisset
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                @isset($discount)
                <h4 class="mb-sm-0 font-size-18">Edit Discount</h4>
                @else
                <h4 class="mb-sm-0 font-size-18">Add New Discount</h4>
                @endisset
                {{-- {{ $errors }}--}}


            </div>
        </div>
    </div>
</div>
<div class="container-fluid cShadow card p-4 rounded">
    @isset($discount)
    <form action="{{ route('update.discount', $discount->id) }}" method="post" enctype="multipart/form-data">
        @else
        <form action="{{ route('insert.discount') }}" method="post" enctype="multipart/form-data">
            @endisset
            @csrf


            <div class="row">

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Code<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" required class="form-control" name="code" @isset($discount)value="{{strtoupper($discount->code)}}" @endisset placeholder="Enter Code">
                    </div>
                    @error('code')
                    <span class="invalid-feedback mt-0" @error('code')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-sm-6 mb-2">
                    <label for="">Value (%)<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" required class="form-control" name="value" @isset($discount)value="{{strtoupper($discount->value)}}" @endisset placeholder="Enter Value ">
                    </div>
                    @error('value')
                    <span class="invalid-feedback mt-0" @error('value')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Start Date<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input class="form-control" required type="date" @isset($discount)value="{{$discount->date_start_expiry}}" @endisset name="date_start_expiry" id="example-date-input">
                    </div>
                    @error('date_start_expiry')
                    <span class="invalid-feedback mt-0" @error('date_start_expiry')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">End Date<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input class="form-control" required type="date" @isset($discount)value="{{$discount->date_end_expiry}}" @endisset name="date_end_expiry" id="example-date-input">
                    </div>
                    @error('date_end_expiry')
                    <span class="invalid-feedback mt-0" @error('date_end_expiry')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group ps col-sm-6 mb-2 d-flex align-items-end">

                    <label for="">Single Time: </label>


                    <div class="ps-3 row">
                        <div class="col-6 form-check form-radio-outline form-radio-info mb-1">
                            <input class="form-check-input" type="radio" name="single_time" value="1" id="single_time1" @if(isset($discount) && $discount->single_time == 1) checked @endif>
                            <label class="form-check-label" for="single_time1">
                                Yes
                            </label>
                        </div>
                        <div class="col-6 form-check form-radio-outline form-radio-danger mb-2">
                            <input class="form-check-input" type="radio" name="single_time" value="0" id="single_time2" @if(isset($discount) && $discount->single_time == 0) checked @endif>
                            <label class="form-check-label" for="single_time2">
                                No
                            </label>
                        </div>
                    </div>

                </div>

                <div class="form-group ps col-sm-6 mb-2 d-flex align-items-end">

                    <label for="">Applicable on Express: </label>


                    <div class="ps-3 row">
                        <div class="col-6 form-check form-radio-outline form-radio-info mb-1">
                            <input class="form-check-input" type="radio" name="for_express" value="1" id="for_express1" @if(isset($discount) && $discount->for_express == 1) checked @endif>
                            <label class="form-check-label" for="for_express1">
                                Yes
                            </label>
                        </div>
                        <div class="col-6 form-check form-radio-outline form-radio-danger mb-2">
                            <input class="form-check-input" type="radio" name="for_express" value="0" id="for_express2" @if(isset($discount) && $discount->for_express == 0) checked @endif>
                            <label class="form-check-label" for="for_express2">
                                No
                            </label>
                        </div>
                    </div>

                </div>

                <div class="form-group ps col-sm-6 mb-2 d-flex align-items-end">

                    <label for="">Applicable on Domestic: </label>


                    <div class="ps-3 row">
                        <div class="col-6 form-check form-radio-outline form-radio-info mb-1">
                            <input class="form-check-input" type="radio" name="for_domestic" value="1" id="for_domestic1" @if(isset($discount) && $discount->for_domestic == 1) checked @endif>
                            <label class="form-check-label" for="for_domestic1">
                                Yes
                            </label>
                        </div>
                        <div class="col-6 form-check form-radio-outline form-radio-danger mb-2">
                            <input class="form-check-input" type="radio" name="for_domestic" value="0" id="for_domestic2" @if(isset($discount) && $discount->for_domestic == 0) checked @endif>
                            <label class="form-check-label" for="for_domestic2">
                                No
                            </label>
                        </div>
                    </div>

                </div>

                <div class="form-group ps col-sm-6 mb-2 d-flex align-items-end">

                    <label for="">Applicable on International: </label>


                    <div class="ps-3 row">
                        <div class="col-6 form-check form-radio-outline form-radio-info mb-1">
                            <input class="form-check-input" type="radio" name="for_international" value="1" id="for_international" @if(isset($discount) && $discount->for_international == 1) checked @endif>
                            <label class="form-check-label" for="for_international">
                                Yes
                            </label>
                        </div>
                        <div class="col-6 form-check form-radio-outline form-radio-danger mb-2">
                            <input class="form-check-input" type="radio" name="for_international" value="0" id="for_international2" @if(isset($discount) && $discount->for_international == 0) checked @endif>
                            <label class="form-check-label" for="for_international2">
                                No
                            </label>
                        </div>
                    </div>

                </div>

                <div class="form-group col-sm-6 mb-2 d-flex align-items-end">

                    <label for="switch4" data-on-label="Yes" data-off-label="No">
                        <label for="">Status: </label>
                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">

                            <input class="form-check-input" name="status" type="checkbox" id="SwitchCheckSizelg" @if(isset($discount) && $discount->status == 1) checked="" @endif>
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
