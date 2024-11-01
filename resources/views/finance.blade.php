@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card bg-white">
                <div>
                    <div class="p-3">
                        <h3>Financials</h3>

                        <div class="row">
                            <div class="col-sm-6">
                                <form class="row gy-2 gx-3 align-items-center">
                                    <div class="col-md-auto">
                                        <label class="visually-hidden" for="autoSizingInput">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" id="autoSizingInput">
                                    </div>
                                    <div class="col-md-auto">
                                        <label class="visually-hidden" for="autoSizingInput">End Date</label>
                                        <input type="date" class="form-control" name="end_date" id="autoSizingInput">
                                    </div>
                                    <div class="col-md-auto">
                                        <label class="visually-hidden" for="autoSizingInput">Zone</label>
                                        <select name="zone" class="form-control" id="">
                                            <option value="">Select zone</option>
                                            @foreach ($zones as $z)
                                                <option value="{{ $z->id }}">{{ $z->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-auto">
                                        <button type="submit" class="btn btn-primary w-md">Submit</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-6 text-center">
                                <div class="row">
                                    @if (isset($financials['total_orders']) && !empty($financials['total_orders']) && $financials['total_orders'] != 0)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <h3>{{ $financials['total_orders'] }}</h3>
                                                <p class="mb-0">Orders</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if (isset($financials['total_payable']) && !empty($financials['total_payable']) && $financials['total_payable'] != 0)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <h3>£{{ $financials['total_payable'] }}</h3>
                                                <p class="mb-0">Endup Commission</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if (isset($financials['orders_value']) && !empty($financials['orders_value']) && $financials['orders_value'] != 0)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <h3>£{{ $financials['orders_value'] }}</h3>
                                                <p class="mb-0">Orders Value</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class=" p-3">

                                <div class="table-responsive">
                                    <table class="table align-middle table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>

                                                <th class="align-middle">Order Info</th>
                                                <th class="align-middle">Customer Name</th>
                                                <th class="align-middle">Date</th>
                                                <th class="align-middle">Shipping Charges</th>
                                                <th class="align-middle">Order Value</th>
                                                <th class="align-middle">Collector Delivery Status</th>
                                                <th class="align-middle">Rider Delivery Status</th>
                                                <th class="align-middle">View Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($financials['orders'] as $o)
                                                <tr>

                                                    <td>
                                                        <a href="javascript: void(0);"
                                                            class="text-body fw-bold">#{{ $o->order_number }}</a><br>
                                                        <b>Retailer: </b>{{ $o->retailer->website }}
                                                    </td>
                                                    <td>
                                                        <b>Name: </b>{{ $o->enduser_name }} <br>
                                                        <b>Mobile: </b> {{ $o->enduser_mobile }}
                                                    </td>
                                                    <td>
                                                        {{ date('F j, Y', strtotime($o->created_at)) }}
                                                    </td>
                                                    <td>
                                                        £{{ $o->shipping_charges }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $order_value = '';
                                                            if (isset($o->items) && !empty($o->items)) {
                                                                $order_value = array_sum($o->items->pluck('price')->toArray());
                                                            }
                                                        @endphp
                                                        £{{ number_format($order_value, 1) }}
                                                    </td>
                                                    <td>


                                                        @if (strtolower($o->collector_delivery_status) == 'in warehouse')
                                                            <span
                                                                class="badge badge-pill badge-soft-success font-size-11">{{ ucwords($o->collector_delivery_status) }}</span>
                                                        @elseif (is_null($o->collector_delivery_status))
                                                            <span
                                                                class="badge badge-pill badge-soft-info font-size-11">{{ ucwords('pending') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-pill badge-soft-info font-size-11">{{ ucwords($o->collector_delivery_status) }}</span>
                                                        @endif

                                                    </td>
                                                    <td>

                                                        @if (strtolower($o->delivery_status) == 'delivered')
                                                            <span
                                                                class="badge badge-pill badge-soft-success font-size-11">{{ ucwords($o->delivery_status) }}</span>
                                                        @elseif (is_null($o->delivery_status))
                                                            <span
                                                                class="badge badge-pill badge-soft-info font-size-11">{{ ucwords('pending') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-pill badge-soft-info font-size-11">{{ ucwords($o->delivery_status) }}</span>
                                                        @endif


                                                    </td>
                                                    <td>
                                                        <!-- Button trigger modal -->
                                                        <a href="{{ route('order.items', $o->id) }}"
                                                            class="btn btn-primary btn-sm btn-rounded waves-effect waves-light">
                                                            View Details
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach


                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
