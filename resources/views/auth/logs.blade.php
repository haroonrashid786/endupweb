@extends('layouts.app')
@section('title', 'Auth Logs')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Auth Logs</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Auth Logs</h4>
                {{--                {{ $errors }} --}}


            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="table-responsive bg-white">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>Date</th>
                    </tr>
                </thead>
                @if (isset($user->auth_logs) && !empty($user->auth_logs))
                <tbody>
                    @foreach ($user->auth_logs as $logs)
                    <tr>
                        <td>{{ $logs->ip }}</td>
                        <td>{{ $logs->country }}</td>
                        <td>{{ $logs->city }}</td>
                        <td>{{ date('F d, Y H:i A', strtotime($logs->created_at)) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                @else

                @endif
            </table>
        </div>
    </div>
@endsection
