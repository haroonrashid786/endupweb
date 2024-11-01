@extends('layouts.app')
@section('title', 'New Message')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item ">Tickets</li>
                    <li class="breadcrumb-item active">New Ticket</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">New Ticket</h4>
                {{--                {{ $errors }} --}}


            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">



                <form action="{{ route('messages.create') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="recipient">Recipient:</label>
                            <select name="recipient_id" id="recipient"
                                class="form-control{{ $errors->has('recipient_id') ? ' is-invalid' : '' }}">
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('recipient_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('recipient_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-6">
                            <label for="subject" >Subject:</label>
                            <input class="form-control{{ $errors->has('subject') ? ' is-invalid' : '' }}" type="text"
                                name="subject" id="subject" >
                            @if ($errors->has('subject'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('subject') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-12">
                            <label for="message">Message:</label>
                            <textarea name="message" id="message" rows="3"
                                class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}" ></textarea>
                            @if ($errors->has('message'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('message') }}</strong>
                                </span>
                            @endif

                        </div>
                        <div class="col-5 my-3">
                            <label for="media" class="btn btn-sm btn-outline-secondary "
                                style="right: 10px; bottom: 10px;">
                                <i class="fas fa-paperclip"></i> Attach File
                                <input type="file" name="attachment[]" id="media" class="d-none" multiple>
                            </label>
                        </div>
                    </div>
                    {{-- <br> --}}
                    <button type="submit" class="btn btn-primary"
                        style="background-color: #78bc8c; border-color: #78bc8c;">Send</button>
                </form>


            </div>
        </div>
    </div>

@endsection
