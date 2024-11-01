@extends('layouts.app')
@isset($postal)
    @section('title', 'Edit Postal')
@else
    @section('title', 'Add New Postal')
@endisset
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0 ps-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Currencies</li>
                        @isset($postal)
                            <li class="breadcrumb-item active">Edit Postal</li>
                        @else
                            <li class="breadcrumb-item active">Add New Postal</li>
                        @endisset
                    </ol>
                </div>
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    @isset($postal)
                        <h4 class="mb-sm-0 font-size-18">Edit Postal</h4>
                    @else
                        <h4 class="mb-sm-0 font-size-18">Add New Postal</h4>
                    @endisset
                    {{--                {{ $errors }}--}}


                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid cShadow card p-4 rounded">
        @isset($postal)
            <form action="{{ route('postal-codes.update', $postal->id) }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ route('postal-codes.insert') }}" method="post" enctype="multipart/form-data">
                        @endisset
                        @csrf


                        <div class="row">
                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Zone<span class="text-danger">*</span></label>
                                <select name="zone" required class="form-control" id="">
                                    @foreach($zones as $z)
                                        <option @if(isset($postal) && $postal->zone_id == $z->id) selected @endif value="{{$z->id}}">{{ $z->name }}</option>
                                    @endforeach
                                </select>
                                @error('zone')
                                <span class="invalid-feedback" @error('zone')style="display: block" @enderror role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group col-sm-6 mb-2">
                                <label for="">Code<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" required class="form-control" name="code" @isset($postal)value="{{strtoupper($postal->postal)}}" @endisset placeholder="Enter Code">
                                </div>
                                @error('code')
                                <span class="invalid-feedback mt-0" @error('code')style="display: block" @enderror role="alert">
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
