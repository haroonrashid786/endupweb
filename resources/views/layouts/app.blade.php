<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8"/>
    <title> @yield('title') | End Up</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description"/>
    <meta content="Themesbrand" name="author"/>
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">

    @include('layouts.header.head-css')


</head>


   @auth
        <body data-sidebar="light" data-layout="horizontal">
       @else
           <body class="sidebar-enable vertical-collpsed">
           @endauth
            @show
            <!-- Begin page -->
            <div id="layout-wrapper">

                @include('layouts.topbar')
                @auth
                   @include('layouts.sidebar')
                @endauth
                <!-- ============================================================== -->
                <!-- Start right Content here -->
                <!-- ============================================================== -->
                <div class="main-content">
                    <div class="page-content pt-3">
                        <div class="container-fluid">
                            @yield('content')
                        </div>
                        <!-- container-fluid -->
                    </div>
                    <!-- End Page-content -->
                    @include('layouts.footer')
                </div>
                <!-- end main content-->
            </div>
            <!-- END layout-wrapper -->

            <!-- Right Sidebar -->
            @include('layouts.right-sidebar')
            <!-- /Right-bar -->

            <!-- JAVASCRIPT -->
            @include('layouts.vendor-scripts')

            @yield('login-Page-Get-Location')
            </body>

</html>
