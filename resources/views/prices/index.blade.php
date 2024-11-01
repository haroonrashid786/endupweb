@extends('layouts.app')
@section('title', 'Prices')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Prices</h4>
                {{--                {{ $errors }}--}}
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Prices</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="w-100">
        <a href="{{ route('add.price') }}" class="btn btn-primary">Add Price</a>
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                <div class="card p-4 rounded cShadow">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                        <tr>
                            <th>Location</th>
                            <th>Volumetric Weight</th>
                            <th>Length</th>
                            <th>Height</th>
                            <th>Width</th>
                            <th>Quantity Box</th>
                            <th>Price</th>
                            <th>Currency</th>
                            <th>Shipping Term</th>
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
            ajax: '/prices',
            columns: [
                {data: 'location', name: 'location'},
                {data: 'volumetric_weight', name: 'volumetric_weight'},
                {data: 'length', name: 'length'},
                {data: 'height', name: 'height'},
                {data: 'width', name: 'width'},
                {data: 'quantity_box', name: 'quantity_box'},
                {data: 'price', name: 'price'},
                {data: 'currency', name: 'currency'},

                {data: 'actions', name: 'actions'},
            ]
        });


    </script>
@endsection
