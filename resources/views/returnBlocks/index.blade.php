@extends('layouts.app')
@section('title', 'Return Blocks')
@section('content')
    <style>
        td.details-control:before {
            content: "\25bc";
        }

        td.details-control {
            text-align: center;
        }
    </style>
    {{-- <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css"> --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Return Blocks</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Return Blocks</h4>
                {{--                {{ $errors }} --}}


            </div>
        </div>
    </div>
    <div class="w-100">
        {{-- <a href="{{ route('add.currency') }}" class="btn btn-primary">Add Currency</a> --}}
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                <div class="card p-4 rounded cShadow">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100 display">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Number</th>
                                <th>Collector</th>
                                <th>Dropoff Location</th>
                                <th>Collector Earning / Hour</th>
                                <th>Date</th>
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
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script> --}}
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    {{-- <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script> --}}
    <script>
        function format(d) {
            // `d` is the original data object for the row
            return (
                '<h3>Return Block Information</h3>' +
                '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px; width: 100%">' +
                '<tr>' +
                '<td><b>Orders Count:</b></td>' +
                '<td>' +
                d.order_count +
                '</td>' +
                '</tr>' +
                '<tr>' +
                '<td><b>Collector Earning:</b></td>' +
                '<td>' +
                d.per_hour_earning +
                '</td>' +
                '</tr>' +
                '<tr>' +
                '<td><b>Orders info:</b></td>' +
                '<td><table class="table table-bordered" style="width:100%"><thead><tr><th>Retailer</th><th>Order Number</th><th>Order Type</th><th>Delivery Charges</th><th>Delivery Status</th><th>Actions</th></tr></thead>' +
                d.orders_table + '</table></td>' +
                '</tr>' +
                '<tr>' +
                '<td><b>Select Collector: ' + d.collectors + '</b></td>' +
                '</tr>' +
                '</table>'
            );
        }

        var table = $('#datatable').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '/return-blocks',
            columns: [{
                    "className": 'details-control',
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "defaultContent": ''
                },
                {
                    data: 'number',
                    name: 'number',
                    searchable: true
                },
                {
                    data: 'rider',
                    name: 'rider',
                    searchable: true
                },
                {
                    data: 'pickup_location',
                    name: 'pickup_location'
                },
                {
                    data: 'per_hour_earning',
                    name: 'per_hour_earning'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },


            ]
        });

        $('#datatable tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });


        // function changeRider(e, block) {

        //     // console.log(e.value, block);

        //     const data = {
        //         rider: e.value,
        //         block: block
        //     };

        //     fetch('/change/collector', {
        //             method: 'POST', // or 'PUT'
        //             headers: {
        //                 'Content-Type': 'application/json',
        //             },
        //             body: JSON.stringify(data),
        //         })
        //         .then((response) => response.json())
        //         .then((data) => {
        //             // console.log('Success:', );
        //             // window.location.reload();
        //             if (data.status === 200) {
        //                 Snackbar.show({
        //                     pos: 'bottom-center',
        //                     text: 'Collection has been assigned to Collector',
        //                     backgroundColor: '#8bd2a4',
        //                     actionTextColor: '#fff'
        //                 });
        //             } else {
        //                 Snackbar.show({
        //                     pos: 'bottom-center',
        //                     text: 'Collector has been removed from the Collection',
        //                     backgroundColor: '#dc3545',
        //                     actionTextColor: '#fff'
        //                 });
        //             }
        //         })
        //         .catch((error) => {
        //             // console.error('Error:', error);
        //         });
        // }
    </script>
@endsection
