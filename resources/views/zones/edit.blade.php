@extends('layouts.app')
@isset($zone)
    @section('title', 'Edit Zone')
@else
@section('title', 'Add New Zone')
@endisset
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Zones</li>
                    @isset($zone)
                        <li class="breadcrumb-item active">Edit Zone</li>
                    @else
                        <li class="breadcrumb-item active">Add New Zone</li>
                    @endisset
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                @isset($zone)
                    <h4 class="mb-sm-0 font-size-18">Edit Zone</h4>
                @else
                    <h4 class="mb-sm-0 font-size-18">Add New Zone</h4>
                @endisset
                {{--                {{ $errors }} --}}


            </div>
        </div>
    </div>
</div>
<div class="container-fluid cShadow card p-4 rounded">
    <div class="row">
        <div class="col-sm-12">
            @isset($zone)
                <form action="{{ route('zones.update', $zone->id) }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ route('zones.insert') }}" method="post" enctype="multipart/form-data">
                    @endisset
                    @csrf


                    <div class="row">



                        <div class="form-group col-sm-6 mb-2">
                            <label for="">Name<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" required class="form-control" name="name"
                                    @isset($zone)value="{{ $zone->name }}" @endisset
                                    placeholder="Enter Name">
                            </div>
                            @error('name')
                                <span class="invalid-feedback mt-0" @error('name')style="display: block" @enderror
                                    role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>




                        @isset($zone)





                                <div class="form-group col-sm-6 mb-2">
                                    <label for="">Import Postals File: </label>
                                    <input type="file" class="form-control" name="file"
                                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                    <small><b>Allowed Ext.</b> .XLSX</small>

                                </div>
                                <div class="form-group col-sm-3 mb-2 d-flex align-items-end">

                                    <label for="switch4" data-on-label="Yes" data-off-label="No">
                                        <label for="">Status: </label>
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">

                                            <input class="form-check-input" name="active" type="checkbox"
                                                id="SwitchCheckSizelg" @if (isset($zone) && $zone->active == 1) checked="" @endif>

                                        </div>
                                    </label>
                                </div>
                                <div class="mt-3">
                                    <p class="text-danger mb-1">After Submitting the file, Please do not close or refresh
                                        the tab/browser, This process will take some time.</p>

                                </div>

                        @else
                            <div id="packagingappendhere">
                                <div class="row clonedata">


                                    <div class="form-group col-sm-3 mb-2 d-flex align-items-end">

                                        <label for="switch4" data-on-label="Yes" data-off-label="No">
                                            <label for="">Status: </label>
                                            <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">

                                                <input class="form-check-input" name="active" type="checkbox"
                                                    id="SwitchCheckSizelg" @if (isset($zone) && $zone->active == 1) checked="" @endif>

                                            </div>
                                        </label>
                                    </div>

                                </div>
                            </div>
                            @endif
                            <div class="form-group col-sm-12 mb-2">
                                <input type="submit" value="Submit" class="btn btn-primary btn-sm">
                            </div>

                        </div>
                    </form>

                    @isset($zone)
                    <hr>
                    @php
                    $postalCodes = '';
                    if (isset($zone->postalcodes) && !empty($zone->postalcodes) && count($zone->postalcodes) > 0) {
                        $postals = $zone->postalcodes
                            ->where('postal', '!=', null)
                            ->pluck('postal')
                            ->toArray();
                        $postalCodes = implode(', ', $postals);
                    }

                @endphp
                <p>
                    {{ $postalCodes }}
                </p>
                    @endisset
            </div>
            {{-- <div class="col-sm-6">
                @isset($zone)
                    <h3>Import Postals</h3>
                    <form action="{{ route('import-postal-code', $zone->id) }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="">File: </label>
                            <input type="file" class="form-control" name="file"
                                accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            <small><b>Allowed Ext.</b> .XLSX</small>

                        </div>
                        <div class="mt-5">
                            <p class="text-danger mb-1">After Submitting the file, Please do not close or refresh the
                                tab/browser, This process will take some time.</p>
                            <input type="submit" class="btn btn-outline-primary btn-sm ">
                        </div>
                    </form>
                @endisset
            </div> --}}
        </div>


    </div>
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script>
        $(document).on('click', '.addmore', function(ev) {
            var $clone = $(this).parent().parent().clone(true);
            console.log($clone[0].querySelectorAll('.form-control'));
            var $newbuttons =
                "<button type='button' class='mb-xs mr-xs btn btn-info addmore'><i class='fa fa-plus'></i></button><button type='button' class='mb-xs mr-xs btn btn-info removemore'><i class='fa fa-minus'></i></button>";
            $clone.find('.tn-buttons').html($newbuttons).end().appendTo($('#packagingappendhere'));
        });

        $(document).on('click', '.removemore', function() {
            $(this).parent().parent().remove();
        });
    </script>
@endsection
