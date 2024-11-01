@extends('layouts.app')
@section('title', 'Retailer Tickets')
@section('content')
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <style>
        td.details-control:before {
            content: "\25bc";
        }

        td.details-control {
            text-align: center;
        }
    </style>
    {{-- <link rel="stylesheet" href="/ /cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css"> --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Retailer Tickets</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Retailer Tickets</h4>
                {{--                {{ $errors }} --}}


            </div>
        </div>
    </div>

    <div class="w-100">
        <a href="{{ route('tickets.add') }}" class="btn btn-primary">Add Ticket</a>
        <div class="row justify-content-center">
            <div class="col-md-12">

            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                <div class="card p-4 rounded cShadow">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100 display">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Retailer</th>
                                <th>Subject</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>

        var table = $('#datatable').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '{{ route('tickets') }}',
            columns: [
                {
                    data: 'ticket_id',
                    name: 'ticket_id',
                    searchable: true
                },
                {
                    data: 'retailer',
                    name: 'retailer',
                    searchable: true
                },
                {
                    data: 'subject',
                    name: 'subject'
                },
                {
                    data: 'actions',
                    name: 'actions'
                },


            ]
        });


    </script>
@endsection
