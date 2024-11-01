@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Users</h4>
{{--                {{ $errors }}--}}
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="container">
        <a href="{{ route('add.user') }}" class="btn btn-primary">Add User</a>
        <div class="row justify-content-center">
            <div class="col-md-12">

            </div>
        </div>
    </div>


{{--    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-lg">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title" id="mySmallModalLabel">Add User</h5>--}}
{{--                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    --}}
{{--                </div>--}}
{{--            </div><!-- /.modal-content -->--}}
{{--        </div><!-- /.modal-dialog -->--}}
{{--    </div><!-- /.modal -->--}}

    <script>
        @@error('username')

        @@enderror


    </script>
@endsection
