@extends('layouts.app')
@section('title', 'Add New Ticket')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    @isset($user)
                        <h4 class="mb-sm-0 font-size-18">Edit Ticket</h4>
                    @else
                        <h4 class="mb-sm-0 font-size-18">Add New Ticket</h4>
                    @endisset
                    {{--                {{ $errors }} --}}
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Retailer Tickets</li>
                            @isset($user)
                                <li class="breadcrumb-item active">Edit Ticket</li>
                            @else
                                <li class="breadcrumb-item active">Add New Ticket</li>
                            @endisset
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <form action="{{ route('tickets.post-ticket') }}" method="post">
            @csrf
            <div class="row">
                <div class="form-group col-sm-12 mb-2">
                    <label for="">Subject</label>
                    <input type="text" name="subject" class="form-control">
                    @error('subject')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-sm-12 mb-2">
                    <label for="">Message</label>
                    <textarea name="message" id="" cols="30" rows="10" class="form-control"></textarea>
                    @error('message')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-sm-12 mb-2">
                    <input type="submit" value="Submit" class="btn btn-primary btn-sm">
                </div>

            </div>
        </form>
    </div>
@endsection
