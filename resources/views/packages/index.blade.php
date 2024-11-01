@extends('layouts.app')
@section('title', 'Packages')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-right">
            <ol class="breadcrumb m-0 ps-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active">Packages</li>
            </ol>
        </div>

    </div>
</div>
<div class="w-100">
    <a href="{{ route('package.add') }}" class="btn btn-primary">Add Packages</a>
    <div class="row justify-content-center">
        <div class="col-md-12 mt-4">
            <div class="card p-4 rounded cShadow">
                <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                    <thead>
                        <tr>
                            <th>Package Weight</th>
                            <th>Package Image</th>
                            <th>Package Width</th>
                            <th>Package Length</th>
                            <th>Package Height</th>
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
        processing: true
        , serverSide: true
        , ajax: "{{ route('packages') }}"
        , columns: [{
                data: 'weight'
                , name: 'weight'
            }
            , {
                data: 'image'
                , name: 'image'

            }
            , {
                data: "length"
                , name: "length"
            }
            , {
                data: "width"
                , name: 'width'
            }
            , {
                data: 'height'
                , name: "height"
            }
            , {
                data: 'status'
                , name: 'status'
            }
            , {
                data: 'actions'
                , name: 'actions'
            }

        , ]
    });

</script>
@endsection
