{{--<x-guest-layout>--}}
{{--    <x-auth-card>--}}
{{--        <x-slot name="logo">--}}
{{--            <a href="/">--}}
{{--                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />--}}
{{--            </a>--}}
{{--        </x-slot>--}}

{{--        <!-- Session Status -->--}}
{{--        <x-auth-session-status class="mb-4" :status="session('status')" />--}}

{{--        <!-- Validation Errors -->--}}
{{--        <x-auth-validation-errors class="mb-4" :errors="$errors" />--}}

{{--        <form method="POST" action="{{ route('login') }}">--}}
{{--            @csrf--}}

{{--            <!-- Email Address -->--}}
{{--            <div>--}}
{{--                <x-label for="email" :value="__('Email')" />--}}

{{--                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />--}}
{{--            </div>--}}

{{--            <!-- Password -->--}}
{{--            <div class="mt-4">--}}
{{--                <x-label for="password" :value="__('Password')" />--}}

{{--                <x-input id="password" class="block mt-1 w-full"--}}
{{--                                type="password"--}}
{{--                                name="password"--}}
{{--                                required autocomplete="current-password" />--}}
{{--            </div>--}}

{{--            <!-- Remember Me -->--}}
{{--            <div class="block mt-4">--}}
{{--                <label for="remember_me" class="inline-flex items-center">--}}
{{--                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">--}}
{{--                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>--}}
{{--                </label>--}}
{{--            </div>--}}

{{--            <div class="flex items-center justify-end mt-4">--}}
{{--                @if (Route::has('password.request'))--}}
{{--                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">--}}
{{--                        {{ __('Forgot your password?') }}--}}
{{--                    </a>--}}
{{--                @endif--}}

{{--                <x-button class="ml-3">--}}
{{--                    {{ __('Log in') }}--}}
{{--                </x-button>--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </x-auth-card>--}}
{{--</x-guest-layout>--}}


@extends('layouts.app')
@section('title', 'Login')
@section('content')
<style>
    .vertical-collpsed .main-content{
        margin-left: 0 !important;
    }
    .page-content.pt-3{
        padding: 94px 12px 60px !important;
    }
</style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card overflow-hidden">
                    <div class="bg-primary bg-soft">
                        <div class="row">
                            <div class="col-7">
                                <div class="text-primary p-4">
                                    <h5 class="text-primary">Welcome Back !</h5>
                                    <p>Sign in to continue to Skote.</p>
                                </div>
                            </div>
                            <div class="col-5 align-self-end">
                                <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="auth-logo">
                            <a href="index.html" class="auth-logo-light">
                                <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="assets/images/logo-light.svg" alt="" class="rounded-circle" height="34">
                                            </span>
                                </div>
                            </a>

                            <a href="index.html" class="auth-logo-dark">
                                <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="assets/images/logo.svg" alt="" class="rounded-circle" height="34">
                                            </span>
                                </div>
                            </a>
                        </div>
                        <div class="p-2">
                            <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="email" placeholder="Enter Email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group auth-pass-inputgroup">
                                        <input type="password" class="form-control" name="password" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
                                        <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                    </div>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <input type="hidden" name="ip_address" id="ip_address" >
                                <input type="hidden" name="country" id="country">
                                <input type="hidden" name="city" id="city">
                                {{--                                <div class="form-check">--}}
                                {{--                                    <input class="form-check-input" type="checkbox" id="remember-check">--}}
                                {{--                                    <label class="form-check-label" for="remember-check">--}}
                                {{--                                        Remember me--}}
                                {{--                                    </label>--}}
                                {{--                                </div>--}}

                                <div class="mt-3 d-grid">
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                                </div>

                                {{--                                <div class="mt-4 text-center">--}}
                                {{--                                    <h5 class="font-size-14 mb-3">Sign in with</h5>--}}

                                {{--                                    <ul class="list-inline">--}}
                                {{--                                        <li class="list-inline-item">--}}
                                {{--                                            <a href="javascript::void()" class="social-list-item bg-primary text-white border-primary">--}}
                                {{--                                                <i class="mdi mdi-facebook"></i>--}}
                                {{--                                            </a>--}}
                                {{--                                        </li>--}}
                                {{--                                        <li class="list-inline-item">--}}
                                {{--                                            <a href="javascript::void()" class="social-list-item bg-info text-white border-info">--}}
                                {{--                                                <i class="mdi mdi-twitter"></i>--}}
                                {{--                                            </a>--}}
                                {{--                                        </li>--}}
                                {{--                                        <li class="list-inline-item">--}}
                                {{--                                            <a href="javascript::void()" class="social-list-item bg-danger text-white border-danger">--}}
                                {{--                                                <i class="mdi mdi-google"></i>--}}
                                {{--                                            </a>--}}
                                {{--                                        </li>--}}
                                {{--                                    </ul>--}}
                                {{--                                </div>--}}

                                <div class="mt-4 text-center">
                                    <a href="auth-recoverpw.html" class="text-muted"><i class="mdi mdi-lock me-1"></i> Forgot your password?</a>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection

@section('login-Page-Get-Location')
<script>
$(document).ready(function(){
    fetch('https://ipinfo.io/?token=75a6ecc2b25c3c')
                .then(response => response.json())
                .then(data => {
                    data = JSON.stringify(data, null, 2);
                    // console.log();
                    parsedData = JSON.parse(data);
                    $('#ip_address').val(parsedData.ip);
                    $('#city').val(parsedData.city);
                    $('#country').val(parsedData.country);
                })
                .catch(error => {
                   console.log('something is wrong with ip');
                });
});
</script>
@endsection
