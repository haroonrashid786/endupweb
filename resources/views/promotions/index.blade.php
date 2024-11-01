@extends('layouts.app')
@section('title', 'Promotion')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                <h4 class="mb-sm-0 font-size-18">Promotion</h4>

                {{--                {{ $errors }}--}}
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Promotion</li>

                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <form action="{{route('promotion.update', $retailer->id)}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="form-group col-sm-6 mb-2">
                            <label for="" class="mb-0">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date"
                                   @isset($retailer->promotion->start_date) value="{{$retailer->promotion->start_date}}"@endif>
                        </div>
                        <div class="form-group col-sm-6 mb-2">
                            <label for="" class="mb-0">Last Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="last_date"
                                   @isset($retailer->promotion->end_date) value="{{$retailer->promotion->end_date}}"@endif>
                        </div>
                        <div class="form-group col-sm-6 mb-2">
                            <label for="" class="mb-0">Pecentage <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="form-control" name="percentage"
                                   @isset($retailer->promotion->percentage) value="{{$retailer->promotion->percentage}}"@endif>
                        </div>
                        <div class="form-group col-sm-6  mb-2">
                            <label for="" class="mb-0">Min Order <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="form-control" name="min_order_value"
                                   @isset($retailer->promotion->min_order_value) value="{{$retailer->promotion->min_order_value}}"@endif>
                        </div>
                        <div class="form-group py-2">
                            <input type="submit" value="Submit" class="btn btn-primary btn-sm">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
