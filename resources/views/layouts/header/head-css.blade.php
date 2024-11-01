@vite(['public/assets/css/bootstrap.min.css', 'public/assets/css/icons.min.css'])
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/node-snackbar@latest/src/js/snackbar.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/node-snackbar@latest/dist/snackbar.min.css" />
<link href="{{ asset('assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/css/jquery.datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">


<style>
    @if (!\Illuminate\Support\Facades\Auth::check())
        body[data-sidebar="dark"] .navbar-brand-box {
            background: #fff;
        }

        footer {
            left: 0 !important;
        }

    @endif

    .bg-primary.bg-soft {
        background: rgba(149, 213, 172, 0.29) !important;
    }

    .text-primary {
        color: #8ad0a3 !important;
    }

    .btn-primary {
        background-color: #8bd2a4;
        border-color: #8bd2a4;
    }

    .btn-check:focus+.btn-primary,
    .btn-primary:focus,
    .btn-primary:hover {
        color: #fff;
        background-color: #8bd2a48c;
        border-color: #8bd2a4;
    }

    .page-item.active .page-link {
        background-color: #8bd2a4;
        border-color: #8bd2a4;
    }

    .cShadow {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
    }

    .dataTables_filter>label {
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }

    .dataTables_length>label {
        display: inline-flex;
        justify-content: center;
        align-items: center;

    }

    .dataTables_length select {
        margin-left: 1em;
        margin-right: 1em;
    }

    .btngroupcst a {
        margin-right: .3rem;
    }

    @media only screen and (min-width: 992px) {
        .topnav .dropdown .dropdown-menu {
            margin-top: 8.5rem;
        }


    }

    .vertical-collpsed .main-content {
        margin-left: 70px !important;
    }

    [class*="gap-"] {
        gap: 0 !important;
    }

    .gap-1>* {
        margin: calc(0.25rem / 2.25);
    }

    .gap-2>* {
        margin: calc(0.5rem / 2.25);
    }

    .gap-3>* {
        margin: calc(0.75rem / 2.25);

    }

    .gap-4>* {
        margin: calc(1rem / 2.25);
    }

    .gap-5>* {
        margin: calc(1.25rem / 2.25);
    }

    .gap-6>* {
        margin: calc(1.5rem / 2.25);
    }

    .gap-7>* {
        margin: calc(1.75rem / 2.25);

    }

    .gap-8>* {
        margin: calc(2rem / 2.25);

    }

    .gap-9>* {
        margin: calc(2.25rem / 2.25);
    }

    .gap-10>* {
        margin: calc(2.5rem / 2.25);
    }

    #search_list {
        position: absolute;
        background: white;
        width: 100%;
        padding: 10px;
        border-radius: 5%;
        list-style: none;
        box-shadow: rgb(100 100 111 / 20%) 0px 7px 29px 0px;
    }
    #search_list hr{
        margin-top: 3px;
    margin-bottom: 3px;
    }
    #search_list a{
        color: #333;
    }

    #postal_list {
        position: absolute;
        background: white;
        width: 96%;
        padding: 10px;
        border-radius: 5%;
        list-style: none;
        z-index: 1;
        max-height: 7rem;
        overflow: auto;
        box-shadow: rgb(100 100 111 / 20%) 0px 7px 29px 0px;
    }
    #postal_list p{
        padding-left: .5rem;
        margin-bottom: 0;
    }

    #postal_list p:hover{
        background: #8fc5a2;
    }

    .btngroupcst{
        white-space: nowrap;
    }
</style>
