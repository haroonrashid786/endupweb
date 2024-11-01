@extends('layouts.app')
@isset($currency)
    @section('title', 'Edit Currency')
@else
    @section('title', 'Add New Currency')
@endisset
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 ps-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Currencies</li>
                        @isset($currency)
                            <li class="breadcrumb-item active">Edit Currency</li>
                        @else
                            <li class="breadcrumb-item active">Add New Currency</li>
                        @endisset
                    </ol>
                </div>
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    @isset($currency)
                        <h4 class="mb-sm-0 font-size-18">Edit Currency</h4>
                    @else
                        <h4 class="mb-sm-0 font-size-18">Add New Currency</h4>
                    @endisset
                    {{--                {{ $errors }}--}}


                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid cShadow card p-4 rounded">
        @isset($currency)
            <form action="{{ route('update.currency', $currency->id) }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ route('insert.currency') }}" method="post" enctype="multipart/form-data">
                        @endisset
                        @csrf


                        <div class="row">

                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Code<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" required class="form-control" name="code" @isset($currency)value="{{strtoupper($currency->code)}}" @endisset placeholder="Enter Code">
                                </div>
                                @error('code')
                                <span class="invalid-feedback mt-0" @error('code')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-sm-6 mb-2 d-flex align-items-end">

                                <label for="switch4" data-on-label="Yes" data-off-label="No">
                                    <label for="">Status: </label>
                                    <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">

                                        <input class="form-check-input" name="status" type="checkbox" id="SwitchCheckSizelg" @if(isset($currency) && $currency->status == 1) checked="" @endif>
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
