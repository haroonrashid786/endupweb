@extends('layouts.app')
@section('title', 'Add New Retailer')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Retailers</li>
                    @isset($user)
                    <li class="breadcrumb-item active">Edit Retailer</li>
                    @else
                    <li class="breadcrumb-item active">Add New Retailer</li>
                    @endisset
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                @isset($user)
                <h4 class="mb-sm-0 font-size-18">Edit Retailer</h4>
                @else
                <h4 class="mb-sm-0 font-size-18">Add New Retailer</h4>
                @endisset
                {{-- {{ $errors }}--}}
                @isset($user)
                <code><b>API KEY: </b>{{ $user->retailer->secret_key }}</code>
                @endisset
            </div>
        </div>
    </div>
</div>
<div class="container-fluid cShadow card p-4 rounded">
    @isset($user)
    <form action="{{ route('update.retailer', $user->id) }}" method="post" enctype="multipart/form-data">
        @else
        <form action="{{ route('insert.retailer') }}" method="post" enctype="multipart/form-data">
            @endisset
            @csrf
            <div class="row">
                <div class="form-group col-sm-6 mb-2">
                    <label for="">Name<span class="text-danger">*</span></label>
                    <input type="text" required value="@isset($user){{$user->name}}@else{{ old('name') }}@endisset" name="name" class="form-control">
                    @error('name')
                    <span class="invalid-feedback" @error('name')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-sm-6 mb-2">
                    <label for="">Username<span class="text-danger">*</span></label>
                    <input type="text" name="username" required value="@isset($user){{$user->username}}@else{{ old('username') }}@endisset" class="form-control">
                    @error('username')
                    <span class="invalid-feedback" @error('username')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Email<span class="text-danger">*</span></label>
                    <input type="email" required value="@isset($user){{$user->email}}@else{{ old('email') }}@endisset" name="email" class="form-control">
                    @error('email')
                    <span class="invalid-feedback" @error('email')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Mobile<span class="text-danger">*</span></label>
                    <input type="number" required value="@isset($user){{$user->mobile}}@else{{ old('mobile') }}@endisset" name="mobile" class="form-control">
                    @error('mobile')
                    <span class="invalid-feedback" @error('mobile')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Phone</label>
                    <input type="number" value="@isset($user){{$user->phone}}@else{{ old('phone') }}@endisset" name="phone" class="form-control">
                    @error('phone')
                    <span class="invalid-feedback" @error('phone')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="">Password @if (!isset($user))<span class="text-danger">*</span>@endif</label>
                    <div class="input-group auth-pass-inputgroup ">
                        <input type="password" @if (!isset($user)) required @endif class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon" style="background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAAPhJREFUOBHlU70KgzAQPlMhEvoQTg6OPoOjT+JWOnRqkUKHgqWP4OQbOPokTk6OTkVULNSLVc62oJmbIdzd95NcuGjX2/3YVI/Ts+t0WLE2ut5xsQ0O+90F6UxFjAI8qNcEGONia08e6MNONYwCS7EQAizLmtGUDEzTBNd1fxsYhjEBnHPQNG3KKTYV34F8ec/zwHEciOMYyrIE3/ehKAqIoggo9inGXKmFXwbyBkmSQJqmUNe15IRhCG3byphitm1/eUzDM4qR0TTNjEixGdAnSi3keS5vSk2UDKqqgizLqB4YzvassiKhGtZ/jDMtLOnHz7TE+yf8BaDZXA509yeBAAAAAElFTkSuQmCC'); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;" autocomplete="off" name="password">
                        <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                    </div>
                    @error('password')
                    <span class="invalid-feedback" @error('password')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group col-sm-6 mb-2">
                    <label for="">Website<span class="text-danger">*</span></label>
                    <input type="url" required value="@isset($user){{$user->retailer->website}}@else{{ old('website') }}@endisset" name="website" class="form-control">
                    @error('website')
                    <span class="invalid-feedback" @error('website')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Support Email</label>
                    <input type="email" value="@isset($user){{$user->retailer->support_email}}@else{{ old('support_email') }}@endisset" name="support_email" class="form-control">
                    @error('support_email')
                    <span class="invalid-feedback" @error('support_email')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Support Mobile</label>
                    <input type="number" value="@isset($user){{$user->retailer->support_mobile}}@else{{ old('support_mobile') }}@endisset" name="support_mobile" class="form-control">
                    @error('support_mobile')
                    <span class="invalid-feedback" @error('support_mobile')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group col-sm-6 mb-2">
                    <label for="">Business Type<span class="text-danger">*</span></label>
                    <select name="business_type_id" required class="form-select" id="">
                        <option value="">Select Business</option>
                        @foreach($bussinessTypes as $bt)
                        <option @if(isset($user) && $bt->id == $user->retailer->business_type_id) selected @endif value="{{$bt->id}}">{{ $bt->name }}</option>
                        @endforeach
                    </select>
                    @error('business_type_id')
                    <span class="invalid-feedback" @error('business_type_id') style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">License File<span class="text-danger">*</span> @isset($user->retailer->licensefile)| <a target="_blank" href="{{$user->retailer->licensefile}}">View File</a>@endisset</label>
                    <input type="file" name="licensefile" @if(!isset($user->retailer->licensefile)) required @endif class="form-control">
                    @error('licensefile')
                    <span class="invalid-feedback" @error('licensefile') style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Currency<span class="text-danger">*</span></label>
                    <select name="currency_id" required class="form-select" id="">
                        <option value="">Select Currency</option>
                        @foreach($currencies as $cur)
                        <option @if(isset($user) && $cur->id == $user->retailer->currency_id) selected @endif value="{{$cur->id}}">{{ $cur->code }}</option>
                        @endforeach
                    </select>
                    @error('currency_id')
                    <span class="invalid-feedback" @error('currency_id')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                {{-- <div class="form-group col-sm-6 mb-2">
                                <label for="">Latitude</label>
                                <input type="number" step="any" id="lat" value="@isset($user){{$user->retailer->latitude}}@endisset" name="latitude" class="form-control">
                @error('latitude')
                <span class="invalid-feedback" @error('latitude')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group col-sm-6 mb-2">
                <label for="">Longitude</label>
                <input type="number" step="any" id="long" value="@isset($user){{$user->retailer->longitude}}@endisset" name="longitude" class="form-control">
                @error('longitude')
                <span class="invalid-feedback" @error('longitude')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div> --}}



            <div class="form-group col-sm-6 mb-2">
                <label for="">Address<span class="text-danger">*</span></label>
                <textarea name="address" required id="address" cols="30" rows="3" class="form-control">@isset($user){{$user->address}}@endisset</textarea>
                @error('address')
                <span class="invalid-feedback" @error('address')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>


            <div class="form-group col-sm-6 mb-2">
                <label for="">Shipping Charges List<span class="text-danger">*</span></label>
                <select name="charges" required class="form-select" id="">
                    <option value="">Select Charges</option>
                    @foreach($chargesLists as $cl)
                    <option @if(isset($user) && $cl->id == $user->retailer->charges[0]->id) selected @endif value="{{$cl->id}}">{{ $cl->name }}</option>
                    @endforeach
                </select>
                @error('charges')
                <span class="invalid-feedback" @error('charges')style="display: block" @enderror role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>


            <div class="form-group col-sm-6 mb-2 d-flex align-items-end">

                <label for="switch4" data-on-label="Yes" data-off-label="No">
                    <label for="">Status: </label>
                    <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                        {{-- <label class="form-check-label" for="SwitchCheckSizelg">InActive</label>--}}
                        <input class="form-check-input" name="active" type="checkbox" id="SwitchCheckSizelg" @if(isset($user) && $user->active == 1) checked="" @endif>
                        {{-- <label class="form-check-label" for="SwitchCheckSizelg">Active</label>--}}
                    </div>
                </label>
            </div>


            <div class="form-group col-sm-12 mb-2">
                <input type="submit" value="Submit" class="btn btn-primary btn-sm">
            </div>

</div>
</form>
</div>

<script>
    var long = document.querySelector('#long');
    var lat = document.querySelector('#lat');
    long.addEventListener("keyup", function() {

        if (lat.value === '') {
            alert('please provide latitude first');
            return;
        }
        console.log(long.value);

        var api = `https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDLiY_bSZJpMWTCe25OqkI7W5fN2lZXwcU&latlng=${lat.value},${long.value}&sensor=false`;
        fetch(api)
            .then(response => response.json())
            .then(data => displayAddress(data));
    });

    function displayAddress(data) {
        document.querySelector('#address').innerText = '';
        // console.log(data.results[0].formatted_address);
        document.querySelector('#address').innerText = data.results[0].formatted_address;
    }

</script>
@endsection
