@extends('layouts.app')
@section('title', 'Business Hours')
@section('content')

<div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Add business hours</h4>
                {{--                {{ $errors }}--}}
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Business Hours</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

<div class="d-flex flex-column gap-2 w-fit">
<form action="{{ route('add.businessHours') }}" method="post" enctype="multipart/form-data">
    @csrf
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
        <div class='text-center'>
        <button id='submit-btn' class="btn btn-success  rounded-md px-4 py-2 !mt-[1rem] !ml-auto text-base font-bold" type="submit">
            Save
        </button>
    </div>
    </div>
</form>
</div>

<br>
<br>
  

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