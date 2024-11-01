@extends('layouts.app')
@section('title', 'Send Message')
@section('content')
    <h1>New Conversation</h1>

    <form method="post" action="{{ route('messages.send') }}">
        @csrf
        <div class="form-group">
            <br>
            <label for="recipient">To:</label>
            <select id="recipient" name="recipient_id" class="form-control">
            <option>-Select Store-</option>

                @foreach ($stores as $store)
                    <option value="{{ $store->user_id }}">{{ $store->store_name }}</option>
                @endforeach
            </select>
        </div>
        <br>
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" name="message" class="form-control" rows="5"></textarea>
        </div>

        <div class="form-group">
            <label for="media">Media:</label>
            <input type="file" id="media" name="media[]" class="form-control" multiple>
        </div>
<br>
        <button type="submit" class="btn btn-success">Send</button>
    </form>
@endsection
