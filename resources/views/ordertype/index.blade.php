@extends('layouts.app')
@section('title', 'Order Types')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-right">
            <ol class="breadcrumb m-0 ps-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active">Order Types</li>
            </ol>
        </div>

    </div>
</div>
<div class="w-100">
    <a href="{{ route('ordertype.add') }}" class="btn btn-primary">Add Order Type</a>
    <div class="row justify-content-center">
        <div class="col-md-12 mt-4">
            <div class="card p-4 rounded cShadow">
                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script src="{{ asset('assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('order_types') }}",
        columns: [{
                data: 'name',
                name: 'name'
            }, {
                data: 'image',
                name: 'image'

            }, {
                data: 'status',
                name: 'status'
            }, {
                data: 'actions',
                name: 'actions'
            }

            ,
        ]
    });
</script>
@endsection