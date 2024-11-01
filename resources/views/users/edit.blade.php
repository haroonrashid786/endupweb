@extends('layouts.app')
@section('title', 'Add New User')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    @isset($user)
                        <h4 class="mb-sm-0 font-size-18">Edit User</h4>
                    @else
                        <h4 class="mb-sm-0 font-size-18">Add New User</h4>
                    @endisset
                    {{--                {{ $errors }}--}}
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Users</li>
                            @isset($user)
                                <li class="breadcrumb-item active">Edit User</li>
                            @else
                                <li class="breadcrumb-item active">Add New User</li>
                            @endisset
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="container">

    <form action="{{ route('insert.user') }}" method="post">
        @csrf
        <div class="row">
            <div class="form-group col-sm-6 mb-2">
                <label for="">Name</label>
                <input type="text" name="name" class="form-control">
            </div>
            <div class="form-group col-sm-6 mb-2">
                <label for="">Username</label>
                <input type="text" name="username" class="form-control">
            </div>

            <div class="form-group col-sm-6 mb-2">
                <label for="">Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="form-group col-sm-6 mb-2">
                <label for="">Mobile</label>
                <input type="text" name="mobile" class="form-control">
            </div>

            <div class="form-group col-sm-6 mb-2">
                <label for="">Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <div class="col-sm-6 mb-2">
                <label for="">Password</label>
                <div class="input-group auth-pass-inputgroup ">
                    <input type="password" class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon"
                           style="background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAAPhJREFUOBHlU70KgzAQPlMhEvoQTg6OPoOjT+JWOnRqkUKHgqWP4OQbOPokTk6OTkVULNSLVc62oJmbIdzd95NcuGjX2/3YVI/Ts+t0WLE2ut5xsQ0O+90F6UxFjAI8qNcEGONia08e6MNONYwCS7EQAizLmtGUDEzTBNd1fxsYhjEBnHPQNG3KKTYV34F8ec/zwHEciOMYyrIE3/ehKAqIoggo9inGXKmFXwbyBkmSQJqmUNe15IRhCG3byphitm1/eUzDM4qR0TTNjEixGdAnSi3keS5vSk2UDKqqgizLqB4YzvassiKhGtZ/jDMtLOnHz7TE+yf8BaDZXA509yeBAAAAAElFTkSuQmCC'); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;"
                           autocomplete="off" name="password">
                    <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                </div>
            </div>
            <div class="form-group col-sm-6 mb-2">
                <label for="">Address</label>
                <textarea name="address" id="" cols="30" rows="3" class="form-control"></textarea>
            </div>

            <div class="form-group col-sm-6 mb-2">
                <label for="">Roles</label>
                <select name="roles[]" multiple class="form-control" id="">
                    @foreach($roles as $r)
                        <option value="{{$r->id}}">{{ $r->name }}</option>
                    @endforeach
{{--                    <option value=""></option>--}}
                </select>
            </div>
            <div class="form-group col-sm-6 mb-2 d-flex align-items-end">

                <label for="switch4" data-on-label="Yes" data-off-label="No">
                    <label for="">Status: </label>
                    <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
{{--                        <label class="form-check-label" for="SwitchCheckSizelg">InActive</label>--}}
                        <input class="form-check-input" name="active" type="checkbox" id="SwitchCheckSizelg" checked="">
{{--                        <label class="form-check-label" for="SwitchCheckSizelg">Active</label>--}}
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
