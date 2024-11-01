@extends('layouts.app')
@section('title', 'Charges List Items')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item">Shipping Charges</li>
                    <li class="breadcrumb-item active">Items</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Items</h4>
                {{--                {{ $errors }}--}}


            </div>
        </div>
    </div>
    <div class="w-100">
        <a href="{{ route('add.charges.items', $id) }}" class="btn btn-primary">Add Item</a>
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                <div class="card p-4 rounded cShadow">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                        <tr>
                          
                            <th>Name</th>
                            <th>Min Volumetric Weight</th>
                            <th>Max Volumetric Weight</th>
                            <th>Price</th>
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
         var showWeights = '{!! $id == 1 ? true : false !!}'
         var showName = '{!! $id != 1 ? true : false !!}'


        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/charges/items/{{ $id }}',
            columns: [
                {data: 'package_name', name: 'package_name',visible: showName},
                {data: 'min_volumetric_weight', name: 'min_volumetric_weight', visible: showWeights},
                {data: 'max_volumetric_weight', name: 'max_volumetric_weight', visible: showWeights},
                {data: 'price', name: 'price'},
                {data: 'actions', name: 'actions'},
            ]
        });


    </script>
@endsection
