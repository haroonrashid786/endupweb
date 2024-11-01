@extends('layouts.app')
@section('title', 'Orders')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Orders</h4>
                {{--                {{ $errors }} --}}

                <a role="button" href="{{ route('manual.order') }}" class="btn btn-primary btn-sm" href="">Add new
                    Order</a>
            </div>

        </div>
    </div>
    <div class="col-sm-12">
        <div class="card p-3 cShadow">
            <form action="{{ route('orders') }}">
                <div class="row d-flex align-items-end">
                    <div class="form-group col-4">
                        <label for="">Retailer (Website)</label>
                        <input type="text" name="retailer" value="{{ Request::get('retailer') }}" class="form-control">
                    </div>
                    {{-- <div class="form-group col-4">
                        <label for="">Website</label>
                        <input type="text" name="website" value="{{ Request::get('website') }}" class="form-control">
                    </div> --}}
                    <div class="form-group col-4">
                        <label for="">End User</label>
                        <input type="text" name="end_user" value="{{ Request::get('end_user') }}" class="form-control">
                    </div>

                    <div class="form-group col-4">
                        <label for="">Status</label>
                        {{-- <input type="text" name="status" value="{{ Request::get('end_user') }}" class="form-control"> --}}
                        <select name="status" class="form-control" id="">
                            <option value="">Select Status</option>
                            @foreach ($statuses as $s)
                                @if (is_null($s))
                                    <option @if (Request::get('status') == 'null' && Request::get('status') != '') selected @endif value="{{ 'null' }}">
                                        {{ 'Pending' }}</option>
                                @else
                                    <option @if (Request::get('status') == $s) selected @endif value="{{ $s }}">
                                        {{ $s }}</option>
                                @endif
                            @endforeach

                        </select>
                    </div>

                    <div class="form-group col-4">
                        <label for="">Search</label>
                        <input type="text" name="search_text" value="{{ Request::get('search_text') }}" class="form-control">
                    </div>

                    <div class="form-group col-3">
                        <label for="">Type</label>
                        <select name="type" class="form-control" id="">
                            <option value="">Select Type</option>
                            @foreach ($types as $t)
                                    <option value="{{ $t->shopifyPackage->id }}" @if (Request::get('type') == $t->shopifyPackage->id) selected @endif>
                                        {{ $t->shopifyPackage->name }}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="form-group col-2">
                        <label for="">Start Date:</label>
                        <input class="form-control" type="date" name="start_date"/>
                    </div>

                    <div class="form-group col-2">
                        <label for="">End Date:</label>
                        <input class="form-control" type="date" name="end_date"/>
                    </div>
                    
                    <div class="form-group w-auto ms-auto mt-3">
                        <input type="submit" class="btn btn-primary w-100">
                    </div>
                    <div class="col-sm-12 mt-2">
                        <input type="radio" id="collector_inwarehouse" @if (Request::get('radioCheck') == 'collector_inwarehouse') checked @endif name="radioCheck" class=""
                            value="collector_inwarehouse">
                        <label for="collector_inwarehouse" class="me-2"> <b>Collector</b> In Warehouse</label>

                        <input type="radio" id="return_inwarehouse" @if (Request::get('radioCheck') == 'return_inwarehouse') checked @endif name="radioCheck" value="return_inwarehouse">
                        <label for="return_inwarehouse" class="me-2"> <b>Return Collector</b> In Warehouse</label>

                        <input type="radio" id="return_retailer" @if (Request::get('radioCheck') == 'return_retailer') checked @endif name="radioCheck" value="return_retailer">
                        <label for="return_retailer" class="me-2"> <b>Return Collector</b> Returned to Retailer</label>

                        <a href="/orders" style="float:right"><i class='bx bx-refresh'></i>Reset Filters</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <div class="w-100">
        <div class="d-flex align-items-center gap-2">
            @if (Auth::user()->isAdmin())
                <form action="{{ route('assign.rider') }}" method="get" id="frm-example">
                    {{-- @csrf --}}
                    <input type="submit" value="Create a Block" class="btn btn-primary btn-sm">
                </form>

                <form action="{{ route('create.collection') }}" method="get" id="frm-collection">
                    {{-- @csrf --}}
                    <input type="submit" value="Create a Collection" class="btn btn-primary btn-sm">
                </form>

                <form action="{{ route('create.return.block') }}" method="get" id="frm-return">
                    {{-- @csrf --}}
                    <input type="submit" value="Create a Return Block" class="btn btn-primary btn-sm">
                </form>
                <form action="{{ route('create.collection.return') }}" method="get" id="frm-collector-return">
                    {{-- @csrf --}}
                    <input type="submit" value="Create a Return Collection (Retailer)" class="btn btn-primary btn-sm">
                </form>
            <div class="btn-group float-end">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Export <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('export.orders', ['type' => '50']) }}">Recent 50</a>
                    <a class="dropdown-item" href="{{ route('export.orders', ['type' => '100']) }}">Recent 100</a>
                    <a class="dropdown-item" href="{{ route('export.orders', ['type' => '200']) }}">Recent 200</a>
                    <a class="dropdown-item" href="{{ route('export.orders', ['type' => 'All']) }}">All</a>
                </div>
            </div>
            @endif

        </div>
        {{--        <button id="assign">Assign to Rider</button> --}}
        {{--        <a href="{{ route('add.rider') }}" class="btn btn-primary">Add Rider</a> --}}
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                <div class="card p-4 rounded cShadow ">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="width: 20%">Retailer</th>
                                {{-- <th>Order Number</th> --}}
                                {{-- <th>Website</th> --}}
                                <th>End User</th>
                                <th>Shipping Charges</th>
                                <th>Collector Delivery Status</th>
                                <th>Rider Delivery Status</th>
                                <th>Delivery Information</th>
                                <th>Zone</th>
                                <th>Order Type</th>
                                <th>Date</th>
                                <th style="width: 15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-modal="true" role="dialog"
        style=" padding-left: 0px;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Riders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{--                    <form action="{{ route('assign.order') }}" method="post"> --}}
                    @csrf
                    <input type="hidden" name="order_id" id="order_id">
                    <ul class="ps-0 ridersListData" style="list-style: none">
                    </ul>
                    <hr>
                    <input type="submit" class="btn-primary btn ">
                    </form>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
        rel="stylesheet" />
    <script type="text/javascript"
        src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
    <script>
        const isAdmin = '{!! Auth::user()->isAdmin() ? true : false !!}'
        const isRetailer = '{!! Auth::user()->isRetailer() ? true : false !!}'

        // console.log('{{ Request::fullUrl() }}'.split('&amp;').join('&'));
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            searching:false,
            ajax: '{{ Request::fullUrl() }}'.replace("http", "http"),

            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'retailer',
                    name: 'retailer',
                    searchable: true,
                    visible: isAdmin
                },
                // {
                //     data: 'order_number',
                //     name: 'order_number'
                // },

                {
                    data: 'enduser',
                    name: 'enduser',
                    searchable: true
                },
                {
                    data: 'shipping_charges',
                    name: 'shipping_charges'
                },
                {
                    data: 'collector_delivery_status',
                    name: 'collector_delivery_status'
                },
                {
                    data: 'delivery_status',
                    name: 'delivery_status'
                },
                {
                    data: 'delivery_info',
                    name: 'delivery_info',
                    searchable: true,
                    visible: isRetailer
                },
                {
                    data: 'zone',
                    name: 'zone'
                },
                {
                    data: 'order_type',
                    name: 'order_type'
                },

                {
                    data: 'date',
                    type: 'num',
                    render: {
                        _: 'display',
                        sort: 'timestamp'
                    }
                },
                {
                    data: 'actions',
                    name: 'actions'
                },
                // {data: 4, name: 'updated_at'}
            ],

            columnDefs: [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "createdCell": function(td, cellData, rowData, row, col) {
                        console.log(rowData);
                        // if (rowData["delivery_non_html"] == 'Assigned to rider') {
                        if (rowData['assigned_to_collector'] == 1 && rowData['assigned_to_rider'] == 1 && (
                                rowData['return'] != 2)) {
                            // console.log('bhrrrr');
                            let checkbox = td.querySelector('input');
                            checkbox.disabled = true;
                            checkbox.setAttribute('data-id', rowData["id"])
                            $(td).removeClass('dt-checkboxes-cell');
                        } else {
                            $(td).addClass('dt-checkboxes-cell');
                        }
                    }
                }

            ],
            select: {
                'style': 'multi'
            },
            order: [
                [7, "DESC"]
            ],
        });
        // $('#datatable tbody').on('click', 'tr', function () {
        //     $(this).toggleClass('selected');
        // });
        $('#frm-example').on('submit', function(e) {
            var form = this;

            var rows_selected = table.column(0).checkboxes.selected();
            // var row_status = table.column(4);
            // console.log(rows_selected.column());






            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId) {
                // console.log(rowId);

                $(form).append(
                    $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'id[]')
                    .val(rowId)
                );
            });



        });
        $('#frm-collection').on('submit', function(e) {
            var form = this;

            var rows_selected = table.column(0).checkboxes.selected();
            // var row_status = table.column(4);
            // console.log(rows_selected.column());






            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId) {
                // console.log(rowId);

                $(form).append(
                    $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'id[]')
                    .val(rowId)
                );
            });



        });

        $('#frm-return').on('submit', function(e) {
            var form = this;

            var rows_selected = table.column(0).checkboxes.selected();
            // var row_status = table.column(4);
            // console.log(rows_selected.column());






            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId) {
                // console.log(rowId);

                $(form).append(
                    $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'id[]')
                    .val(rowId)
                );
            });



        });
        $('#frm-collector-return').on('submit', function(e) {
            var form = this;

            var rows_selected = table.column(0).checkboxes.selected();
            // var row_status = table.column(4);
            // console.log(rows_selected.column());






            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId) {
                // console.log(rowId);

                $(form).append(
                    $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'id[]')
                    .val(rowId)
                );
            });



        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" defer></script>

    <script>
        function openModal(id) {
            var ulRiders = $('.ridersListData');
            $('#order_id').val(id);
            ulRiders.empty();
            $.ajax({
                url: '{{ route('rider.list') }}',
                success: function(result) {
                    const riders = result.riders
                    riders.forEach((rider, index) => {
                        let li = document.createElement('li');
                        li.innerHTML = `
                                <div class="row d-flex align-items-center mb-3">
                                    <div class="col-sm-3">
                                        <img src="https://blog.hubspot.com/hubfs/image8-2.jpg" class="rounded-circle avatar-sm height="65" width="65" alt="">
                                    </div>
                                    <div class="col-sm-5">
                                        ${rider.name}
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="radio" name="rider_id" value="${rider.id}">
                                    </div>
                                    <div class="col-sm-3">
                                        <select class="form-control-sm" name="delivery_type" id="">
                                            <option value="normal">Normal</option>
                                            <option value="instant">Instant</option>
                                        </select>
                                    </div>
                                </div>`;
                        $(ulRiders).append(li);

                        console.log(li);
                    })
                }
            });

            $('#myModal').modal('show');
        }
    </script>
@endsection
