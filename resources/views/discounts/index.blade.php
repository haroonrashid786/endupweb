@extends('layouts.app')
@section('title', 'Discounts')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-right">
            <ol class="breadcrumb m-0 ps-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active">Discounts</li>
            </ol>
        </div>
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Discounts</h4>
            {{-- {{ $errors }}--}}


        </div>
    </div>
</div>
<div class="w-100">
    <a href="{{ route('add.discount') }}" class="btn btn-primary">Add Discount</a>
    <div class="row justify-content-center">
        <div class="col-md-12 mt-4">
            <div class="card p-4 rounded cShadow">
                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Value (%)</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Domestic</th>
                            <th>International</th>
                            <th>Express</th>
                            <th>Status</th>
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
        ajax: '/discounts',
        columns: [{
                data: 'code',
                name: 'code'
            },
            {
                data: 'value',
                name: 'value'
            },

            {
                data: 'start_date',
                name: 'start_date'
            },
            {
                data: 'end_date',
                name: 'end_date'
            },
            {
                data: 'domestic',
                name: 'domestic'
            },
            {
                data: 'international',
                name: 'international'
            },
            {
                data: 'express',
                name: 'express'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'actions',
                name: 'actions'
            },
        ]
    });
</script>
@endsection