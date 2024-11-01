@extends('layouts.app')
@section('title', 'Retailers')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Retailers</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Retailers</h4>
                {{--                {{ $errors }}--}}


            </div>
        </div>
    </div>
    <div class="w-100">
        <a href="{{ route('add.retailer') }}" class="btn btn-primary">Add Retailer</a>
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                <div class="card p-4 rounded cShadow " >
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Website</th>
                            <th>License File</th>
                            <th>Active</th>
                            <th>Actions</th>
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
            ajax: '/retailers',
            columns: [
                {data: 'name', name: 'name'},
                {data: 'email', name: 'name'},
                {data: 'username', name: 'username'},
                {data: 'website', name: 'website'},
                {data: 'licensefile', name: 'licensefile'},
                {data: 'status', name: 'status'},
                {data: 'actions', name: 'actions'},
                // {data: 4, name: 'updated_at'}
            ]
        });


    </script>
@endsection
