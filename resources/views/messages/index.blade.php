@extends('layouts.app')
@section('title', 'Messages')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tickets</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Tickets</h4>
                {{--                {{ $errors }} --}}

                <a role="button" href="{{ route('form.message') }}" class="btn btn-primary btn-sm" href="">New
                    Ticket</a>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="card-header bg-success text-white">Conversations</div> --}}

                    <div class="card-body">
                        {{-- @if ($conversations->count() > 0)
                        <ul class="list-group">
                            @foreach ($conversations as $conversation)
                                <li class="list-group-item">

                                @if ($conversation->status == 1)
                                <div class="text-sm text-gray-500 text-right">
                                <span class="badge bg-danger">Closed</span>
                                </div>
                                @else
                                <div class="text-sm text-gray-500 text-right">
                                <span class="badge bg-success">Active</span>
                                </div>
                                @endif
                                    <a href="{{ route('messages.show', $conversation) }}">
                                        @if ($conversation->lastMessage)
                                            {{ $conversation->lastMessage->sender->full_name }}: {{ $conversation->lastMessage->message }}
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No conversations yet.</p>
                    @endif --}}
                        <table class="table table-bordered">
                            <thead>
                                <th>Retailer Info</th>
                                <th>Subject</th>
                                <th>Number</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </thead>
                            <tbody>
                                @if (count($conversations) > 0)
                                    @foreach ($conversations as $conversation)
                                    <tr>
                                        <td>

                                            @if ($conversation->lastMessage)
                                                {{ $conversation->lastMessage->sender->username }}
                                            @endif
                                        </td>
                                        <td>

                                            {{ $conversation->subject }}
                                        </td>
                                        <td>
                                            <a href="{{ route('messages.show', $conversation) }}">
                                               {{ $conversation->ticket_no }}
                                            </a>
                                        </td>
                                        <td>
                                            @if ($conversation->status == 1)
                                                <div class="text-sm text-gray-500 text-right">
                                                    <span class="badge bg-danger">Closed</span>
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-500 text-right">
                                                    <span class="badge bg-success">Active</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $conversation->created_at->toDayDateTimeString() }}
                                        </td>
                                        <td>
                                            <div>
                                                @if ($conversation->status != 1)
                                                    <form style="display: inline" method="post"
                                                        action="{{ route('conversation.end', $conversation) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            End Conversation
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('messages.show', $conversation) }}"
                                                    class="btn btn-primary btn-sm">View</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
