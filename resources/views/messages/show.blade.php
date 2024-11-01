@extends('layouts.app')
@section('title', 'Show Messages')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item ">Tickets</li>
                    <li class="breadcrumb-item active"> {{ $conversation->ticket_no }}</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Ticket |
                    {{ $conversation->initiator->id === Auth::id() ? $conversation->recipient->username : $conversation->initiator->username }}
                </h4>
                {{--                {{ $errors }} --}}

                {{-- <a role="button" href="{{ route('form.message') }}" class="btn btn-primary btn-sm" href="">New
                Ticket</a> --}}
                <form method="post" action="{{ route('conversation.end', $conversation) }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        End Conversation
                    </button>
                </form>
            </div>

        </div>
    </div>

    <div class="container-fluid" style="">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    {{-- <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #78bc8c; color: #fff;">

          @if ($conversation->initiator->id !== Auth::id() && $conversation->recipient->id !== Auth::id())
            <span style="font-size: 12px; font-weight: normal;">(ticket with {{ $conversation->initiator->full_name }} and {{ $conversation->recipient->full_name }})</span>
          @endif


        </div> --}}
                    {{-- {{ dd($conversation->initiator) }} --}}
                    {{-- @if (isset($conversation->initiator->retailer)) --}}
                        <div class="user-details" style="padding: 20px;">
                            <b>Subject: </b>{{ $conversation->subject }}
                            {{-- <h5>Business Details:</h5> --}}
                            <!-- Add user details here -->
                            {{-- <p>Retailer Website: {{ $conversation->initiator->retailer->website }}</p> --}}
                            {{-- <p>Business Email: {{$conversation->initiator->store->email}}</p>
          <p>Business Address: {{$conversation->initiator->store->address}}</p>
          <p>Business Contact: {{$conversation->initiator->country_code}}-{{$conversation->initiator->store->phone_number}}</p> --}}
                            <!-- Add more user details as needed -->
                        </div>
                    {{-- @endif --}}


                    @if ($conversation->status == 0)
                        <div class="accordion accordion-flush px-4" id="accordionFlushExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOne">
                                    <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapseOne" aria-expanded="true"
                                        aria-controls="flush-collapseOne">
                                        <b>Reply</b>
                                    </button>
                                </h2>
                                <div id="flush-collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample"
                                    style="">
                                    <form action="{{ route('messages.send') }}" method="post" enctype="multipart/form-data"
                                        class='p-3 pt-3'>
                                        @csrf
                                        <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                                        <input type="hidden" name="recipient_id"
                                            value="{{ $conversation->initiator->id === Auth::id() ? $conversation->recipient->id : $conversation->initiator->id }}">
                                        <div class="form-group">
                                            <div style="position: relative">
                                                <textarea name="message" rows="3" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}"
                                                    placeholder="Type your message..."></textarea>
                                                <label for="media"
                                                    class="btn btn-sm btn-outline-secondary position-absolute"
                                                    style="right: 10px; bottom: 10px;">
                                                    <i class="fas fa-paperclip"></i> Attach File
                                                    <input type="file" name="attachment[]" id="media" class="d-none"
                                                        multiple>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mt-2" style='text-align:right;'>
                                            <button type="submit" class="btn btn-success">Send</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                        <hr>
                    @endif
                    @foreach ($conversation->messages as $message)
                        <div class="card mx-3 border-1 shadow">
                            <div class="card-header">
                                {{-- @if ($message->sender->id !== Auth::id()) --}}
                                <div class="d-flex justify-content-between">
                                    <b>{{ $message->sender->username }}</b>
                                    <small>{{ $message->created_at->toDayDateTimeString() }}</small>
                                </div>
                                {{-- @endif --}}
                            </div>
                            <div class="card-body">
                                <div style="word-wrap: break-word;">{{ $message->message }}</div>
                                @if (count($message->media) > 0)
                                    <div class="mt-3">
                                        @foreach ($message->media as $media)
                                            <a href="{{ $media->path }}" target="_blank"
                                                class="btn btn-sm btn-primary mr-2">
                                                View Attachment
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach


                    {{-- <div class="card-body"
                        style="height: 400px; overflow-y: auto; display: flex; flex-direction: column-reverse;">
                        <ul class="list-unstyled" style="margin-bottom: 0;">
                            @foreach ($conversation->messages as $message)
                                <li class="{{ $message->sender->id === Auth::id() ? 'sent' : 'received' }}">
                                    <div class="media">

                                        <div class="media-body">
                                            <h5 class="mt-0"
                                                style="color: {{ $message->sender->id === Auth::id() ? '#78bc8c' : '#6c757d' }}; font-weight: bold;">
                                                {{ $message->sender->full_name }}
                                                @if ($message->sender->id !== Auth::id())
                                                    <span style="font-size: 12px; font-weight: normal;">(ticket with
                                                        {{ $message->sender->username }})</span>
                                                @endif
                                            </h5>

                                            <span
                                                style="color: #6c757d; font-size: 12px;">{{ $loop->last ? $message->created_at->diffForHumans() : '' }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div> --}}
                    <hr>
                    @if ($conversation->status == 0)
                        {{-- <form action="{{ route('messages.send') }}" method="post" enctype="multipart/form-data"
                            class='p-3 pt-1'>
                            @csrf
                            <input type="hidden" name="recipient_id"
                                value="{{ $conversation->initiator->id === Auth::id() ? $conversation->recipient->id : $conversation->initiator->id }}">
                            <div class="form-group">
                                <div style="position: relative">
                                    <textarea name="message" rows="3" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}"
                                        placeholder="Type your message..."></textarea>
                                    <label for="media" class="btn btn-sm btn-outline-secondary position-absolute"
                                        style="right: 10px; bottom: 10px;">
                                        <i class="fas fa-paperclip"></i> Attach File
                                        <input type="file" name="attachment[]" id="media" class="d-none" multiple>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mt-2" style='text-align:right;'>
                                <button type="submit" class="btn btn-success">Send</button>
                            </div>
                        </form> --}}
                    @else
                        {{-- <input type="hidden" name="recipient_id"
                            value="{{ $conversation->initiator->id === Auth::id() ? $conversation->recipient->id : $conversation->initiator->id }}">
                        <div class="form-group">
                            <div style="position: relative">
                                <textarea name="message" rows="3" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}"
                                    placeholder="Conversation has ended" disabled></textarea>
                                <label for="media" class="btn btn-sm btn-outline-secondary position-absolute"
                                    style="right: 10px; bottom: 10px;">
                                    <i class="fas fa-paperclip"></i> Attach File
                                    <input type="file" name="attachment[]" id="media" class="d-none" multiple
                                        disabled>
                                </label>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success" disabled>Send</button>
                        </div> --}}
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('input[type="file"]').change(function(e) {
                var fileName = e.target.files[0].name;
                $(this).next('.custom-file-label').html(fileName);
            });
        });


        $(document).ready(function() {
            // Get the conversation container
            var container = $('.card-body');

            // Scroll to the bottom of the container
            container.scrollTop(container.prop("scrollHeight"));
        });
    </script>

@endsection
