@extends('layouts.app')
@isset($zone)
    @section('title', 'Edit Package')
@else
@section('title', 'Add New Package')
@endisset
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Shopify Package</li>
                    @isset($type)
                        <li class="breadcrumb-item active">Edit Package</li>
                    @else
                        <li class="breadcrumb-item active">Add New Package</li>
                    @endisset
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                @isset($type)
                    <h4 class="mb-sm-0 font-size-18">Edit Package</h4>
                @else
                    <h4 class="mb-sm-0 font-size-18">Add New Package</h4>
                @endisset


            </div>
        </div>
    </div>
</div>
<div class="container-fluid cShadow card p-4 rounded">
    <div class="row">
        <div class="col-sm-12">
            @isset($type)
                <form action="{{ route('update.shopify.package', $type->id) }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ route('add.shopify.package') }}" method="post" enctype="multipart/form-data">
                    @endisset
                    @csrf

                    <div class="row">

                        <div class="form-group col-sm-6 mb-2">
                            <label for="">Name<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" required class="form-control" name="name"
                                    @isset($type) value="{{ $type->name }}" @endisset
                                    placeholder="Enter Name">
                            </div>
                            @error('name')
                                <span class="invalid-feedback mt-0" @error('name')style="display: block" @enderror
                                    role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
<!-- 
                        <div class="form-group col-sm-6 mb-2">
                            <label for="">Price<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" required class="form-control" name="price"
                                    @isset($type)value="{{ $type->price }}" @endisset
                                    placeholder="Enter Price">
                            </div>
                            @error('price')
                                <span class="invalid-feedback mt-0" @error('price')style="display: block" @enderror
                                    role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> -->

                            <div id="packagingappendhere">
                                <div class="row clonedata">


                                    <div class="form-group col-sm-3 mb-2 d-flex align-items-end">

                                        <label for="switch4" data-on-label="Yes" data-off-label="No">
                                            <label for="">Status: </label>
                                            <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">

                                                <input class="form-check-input" name="active" type="checkbox"
                                                    id="SwitchCheckSizelg" @if (isset($type) && $type->status == 1) checked="" @endif>

                                            </div>
                                        </label>
                                    </div>

                                </div>
                            </div>



                            <div class="row">
        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="card-title text-lg font-bold mb-4">{{ ucfirst($day) }}</h2>
                            <button class="btn btn-sm btn-danger reset_All_Dates" type="button">Reset</button>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="{{ $day }}_open_time">Open Time</label>
                                    <input class="form-control" id="{{ $day }}_open_time" name="{{ $day }}_open_time" type="time" value="{{ isset($businessHoursData[$day]['open_time']) ? $businessHoursData[$day]['open_time'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="{{ $day }}_break_time">Break Time</label>
                                    <input class="form-control" id="{{ $day }}_break_time" name="{{ $day }}_break_time" type="time" value="{{ isset($businessHoursData[$day]['break_time_start']) ? $businessHoursData[$day]['break_time_start'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="{{ $day }}_break_time_end">Break Time End</label>
                                    <input class="form-control" id="{{ $day }}_break_time_end" name="{{ $day }}_break_time_end" type="time" value="{{ isset($businessHoursData[$day]['break_time_end']) ? $businessHoursData[$day]['break_time_end'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="{{ $day }}_close_time">Close Time</label>
                                    <input class="form-control" id="{{ $day }}_close_time" name="{{ $day }}_close_time" type="time" value="{{ isset($businessHoursData[$day]['close_time']) ? $businessHoursData[$day]['close_time'] : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


                            <div class="form-group col-sm-12 mb-2">
                                <input type="submit" value="Submit" class="btn btn-primary btn-sm">
                            </div>

                        </div>
                    </form>

                   
            </div>
           
        </div>
    </div>

    <div class="d-flex flex-column gap-2 w-fit">
    <div class="row row-cols-3 g-4">
        @foreach ($businessHoursData as $day => $businessHours)
            <div class="col">
                <div class="bg-white rounded shadow p-4">
                    <h2 class="text-lg fw-bold mb-4">{{ ucfirst($day) }}</h2>
                    <div class="d-flex align-items-center mb-2">
                        <svg class="text-success me-2" height='25' width='25' fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M0 10c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10S0 15.523 0 10zm10 8.75A8.75 8.75 0 0 0 18.75 10c0-4.31-3.136-7.862-7.25-8.593v3.044a.75.75 0 1 1-1.5 0V1.667a.75.75 0 1 1 1.5 0v.961c2.987.64 5.25 3.118 5.25 6.072z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            @if ($businessHours['open_time'] != '')
                                <span class="text-success fw-bold">{{ \Carbon\Carbon::parse($businessHours['open_time'])->format('g:i A') }}</span> - <span class="text-success fw-bold">{{ $businessHours['close_time'] ? \Carbon\Carbon::parse($businessHours['close_time'])->format('g:i A') : 'Business hours not available' }}</span>
                            @else
                                <span class="text-danger fw-bold">Business Hour Not Available</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded',()=>{
    let reset_All_Dates = document.querySelectorAll('.reset_All_Dates');
    reset_All_Dates.forEach(item=>{
        item.addEventListener('click',()=>{
            let parent = item.closest('.card-body');
            let all_time = parent.querySelectorAll('input[type="time"]');
            all_time.forEach(time=>{
                time.value = ''
            })
       })
    })
})
</script>

@endsection
