@extends('layouts.app')
@section('title', 'Add New User')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                    @isset($user)
                    <li class="breadcrumb-item active">Edit Rider</li>
                    @else
                    <li class="breadcrumb-item active">Add New Rider</li>
                    @endisset
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                @isset($user)
                <h4 class="mb-sm-0 font-size-18">Edit Rider</h4>
                @else
                <h4 class="mb-sm-0 font-size-18">Add New Rider</h4>
                @endisset
                {{-- {{ $errors }} --}}


            </div>
        </div>
    </div>
</div>
<div class="container-fluid cShadow card p-4 rounded">
    @isset($user)
    <form action="{{ route('update.rider', $user->id) }}" method="post" enctype="multipart/form-data">
        @else
        <form action="{{ route('insert.rider') }}" method="post" enctype="multipart/form-data">
            @endisset
            @csrf
            <div class="row">
                <div class="form-group col-sm-6 mb-2">
                    <label for="">Name<span class="text-danger">*</span></label>
                    <input type="text" value="@isset($user){{ $user->name }} @else {{old('name')}} @endisset"
                           name="name" required class="form-control">
                    @error('name')
                    <span class="invalid-feedback" @error('name')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-sm-6 mb-2">
                    <label for="">Username<span class="text-danger">*</span></label>
                    <input type="text" name="username"
                           value="@isset($user){{ $user->username }} @else {{old('username')}} @endisset" required
                           class="form-control">
                    @error('username')
                    <span class="invalid-feedback" @error('username')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Email<span class="text-danger">*</span></label>
                    <input type="email" value="@isset($user){{ $user->email }} @else {{old('email')}} @endisset"
                           name="email" required class="form-control">
                    @error('email')
                    <span class="invalid-feedback" @error('email')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Mobile<span class="text-danger">*</span></label>
                    <input type="number" value="@isset($user){{ $user->mobile }} @else {{old('mobile')}} @endisset"
                           name="mobile" required class="form-control">
                    @error('mobile')
                    <span class="invalid-feedback" @error('mobile')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Phone</label>
                    <input type="number" value="@isset($user){{ $user->phone }} @else {{old('phone')}} @endisset"
                           name="phone" class="form-control">
                    @error('phone')
                    <span class="invalid-feedback" @error('phone')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="">Password<span class="text-danger">*</span></label>
                    <div class="input-group auth-pass-inputgroup ">
                        <input type="password" class="form-control" placeholder="Enter password" aria-label="Password"
                               aria-describedby="password-addon"
                               style="background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAAPhJREFUOBHlU70KgzAQPlMhEvoQTg6OPoOjT+JWOnRqkUKHgqWP4OQbOPokTk6OTkVULNSLVc62oJmbIdzd95NcuGjX2/3YVI/Ts+t0WLE2ut5xsQ0O+90F6UxFjAI8qNcEGONia08e6MNONYwCS7EQAizLmtGUDEzTBNd1fxsYhjEBnHPQNG3KKTYV34F8ec/zwHEciOMYyrIE3/ehKAqIoggo9inGXKmFXwbyBkmSQJqmUNe15IRhCG3byphitm1/eUzDM4qR0TTNjEixGdAnSi3keS5vSk2UDKqqgizLqB4YzvassiKhGtZ/jDMtLOnHz7TE+yf8BaDZXA509yeBAAAAAElFTkSuQmCC'); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;"
                               autocomplete="off" name="password">
                        <button class="btn btn-light " type="button" id="password-addon"><i
                                class="mdi mdi-eye-outline"></i></button>
                    </div>
                    @error('password')
                    <span class="invalid-feedback" @error('password')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">

                    <div class="row">
                        <div class="col-sm-6">
                            <label for="">Passport Number<span class="text-danger">*</span></label>
                            <input type="text"
                                   value="@isset($user->rider){{ $user->rider->passport }} @else {{old('passport')}} @endisset"
                                   required name="passport" class="form-control">
                            @error('passport')
                            <span class="invalid-feedback" @error('passport')style="display: block" @enderror
                            role="alert">
                            <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label for="">Passport File<span class="text-danger">*</span>
                                @if (isset($user->rider->passport_file) && $user->rider->passport_file != null)
                                <a href="{{ asset($user->rider->passport_file) }}" target="_blank">View File</a>
                                @endif
                            </label>
                            <input type="file" @if (!isset($user)) required @endif
                                   name="passport_file" class="form-control">
                            @error('passport_file')
                            <span class="invalid-feedback" @error('passport_file')style="display: block" @enderror
                            role="alert">
                            <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="">License Number<span class="text-danger">*</span></label>
                            <input type="text" required
                                   value="@isset($user->rider){{ $user->rider->license_number }} @else {{old('license_number')}} @endisset"
                                   name="license_number" class="form-control">
                            @error('license_number')
                            <span class="invalid-feedback" @error('license_number')style="display: block" @enderror
                            role="alert">
                            <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label for="">License File<span class="text-danger">*</span>
                                @if (isset($user->rider->license_file) && $user->rider->license_file != null)
                                <a href="{{ asset($user->rider->license_file) }}" target="_blank">View File</a>
                                @endif
                            </label>
                            <input type="file" @if (!isset($user)) required @endif name="license_file"
                                   class="form-control">
                            @error('license_file')
                            <span class="invalid-feedback" @error('license_file')style="display: block" @enderror
                            role="alert">
                            <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Address</label>
                    <textarea name="address" id="" cols="30" rows="3" class="form-control">
@isset($user)
{{ $user->address }}
 @else {{old('address')}} 
@endisset
</textarea>
                    @error('address')
                    <span class="invalid-feedback" @error('address')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Working Days<span class="text-danger">*</span></label>
                    <select name="working_days[]" required multiple class="form-control" id="">
                        @foreach ($workingDays as $wd)
                        <option @if (isset($user) && in_array($wd->id, $user->rider->workingdays->pluck('id')->toArray())) selected @endif value="{{ $wd->id }}">
                            {{ $wd->day }}
                        </option>
                        @endforeach
                    </select>
                    @error('working_days')
                    <span class="invalid-feedback" @error('working_days')style="display: block" @enderror
                    role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Zones<span class="text-danger">*</span></label>
                    <select name="zones[]" required multiple class="form-control" id="">
                        @foreach ($zones as $z)
                        <option @if (isset($user) && in_array($z->id, $user->rider->zones->pluck('id')->toArray()))
                            selected @endif value="{{ $z->id }}">
                            {{ $z->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('zones')
                    <span class="invalid-feedback" @error('zones')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-sm-3 mb-2 d-flex align-items-end">

                    <label for="switch4" data-on-label="Yes" data-off-label="No">
                        <label for="">Collector: </label>
                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">

                            <input class="form-check-input" name="collector" type="checkbox" id="SwitchCheckSizelg"
                                   @if (isset($user) && $user->rider->is_collector == 1) checked="" @endif>

                        </div>
                    </label>
                </div>
                <div class="form-group col-sm-3 mb-2 d-flex align-items-end">

                    <label for="switch4" data-on-label="Yes" data-off-label="No">
                        <label for="">Status: </label>
                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">

                            <input class="form-check-input" name="active" type="checkbox" id="SwitchCheckSizelg"
                                   @if (isset($user) && $user->active == 1) checked="" @endif>

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
