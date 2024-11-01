@extends('layouts.app')
@section('title', 'Retailer Documents')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Retailer Documents</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Retailer Documents | {{ $retailer->website }}</h4>
                {{--                {{ $errors }}--}}


            </div>
        </div>
    </div>
    <div class="w-100">
        {{-- <a href="{{ route('add.retailer') }}" class="btn btn-primary">Add Retailer Document</a> --}}
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addModal">Add Retailer Document</button>

<div id="addModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Add Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('retailer.documents.add', request()->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="">Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="form-group mb-3">
                            <label for="">Document</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                        <div class="form-group">
                           <input type="submit" class="btn btn-success btn-sm">
                        </div>
                    </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                <div class="card p-4 rounded cShadow " >
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>View</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($docs as $d)
                                <tr>
                                    <td>{{ $d->name }}</td>
                                    <td><a href="{{ asset($d->path) }}" target="_blank">View</a></td>
                                    <td><a href="{{ route('retailer.delete.document', $d->id) }}" class="btn btn-danger btn-sm">Delete</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <script src="{{ asset('assets/libs/jquery/jquery.min.js')}}"></script>
    {{-- <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
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


    </script> --}}
@endsection
