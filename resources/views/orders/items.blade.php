@extends('layouts.app')
@section('title', 'Items')
@section('content')
    <style>
        body {
            margin-top: 20px;
        }

        .timeline-steps {
            display: flex;
            justify-content: center;
            flex-wrap: wrap
        }

        .timeline-steps .timeline-step {
            align-items: center;
            display: flex;
            flex-direction: column;
            position: relative;
            margin: 1rem
        }

        @media (min-width: 768px) {
            .timeline-steps .timeline-step:not(:last-child):after {
                content: "";
                display: block;
                border-top: .25rem dotted #c1e0cc;
                width: 3.46rem;
                position: absolute;
                left: 7.5rem;
                top: .3125rem
            }

            .timeline-steps .timeline-step:not(:first-child):before {
                content: "";
                display: block;
                border-top: .25rem dotted #c1e0cc;
                width: 3.8125rem;
                position: absolute;
                right: 7.5rem;
                top: .3125rem
            }
        }

        .timeline-steps .timeline-content {
            width: 10rem;
            text-align: center
        }

        .timeline-steps .timeline-content .inner-circle {
            border-radius: 1.5rem;
            height: 1rem;
            width: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #90c6a3
        }

        .timeline-steps .timeline-content .inner-circle:before {
            content: "";
            background-color: #c1e0cc;
            display: inline-block;
            height: 3rem;
            width: 3rem;
            min-width: 3rem;
            border-radius: 6.25rem;
            opacity: .5
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item">Order</li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Orders</h4>
                {{--                {{ $errors }} --}}


            </div>
        </div>
    </div>
    <div class="w-100">
        {{--        <a href="{{ route('add.rider') }}" class="btn btn-primary">Add Rider</a> --}}

        <div class="row">
            <div class="col-sm-4 p-3">
                <div class="card p-2 rounded shadow">
                    <h4>Order Information @if($order->is_grocery == 1)
                            <span style="font-size: .71rem" class="text-sm badge rounded-pill bg-success">Grocery</span>
                        @endif</h4>
                    <p class="mb-0"><b>Retailer: </b>{{ ucwords($order->retailer->user->name) }}</p>
                    <p class="mb-0"><b>Retailer Website: </b>{{ $order->retailer->website }}</p>
                    <p class="mb-0"><b>Retailer Address: </b>{{ $order->retailer->address }}</p>
                    <p class="mb-0"><b>Order Key: </b>{{ $order->order_key }}</p>
                    <p class="mb-0"><b>Delivery Status: </b>{{ $order->delivery_status }}</p>
                    <p class="mb-0"><b>Order Type: </b>
                        @if ($order->order_type == 'express')
                            <span class="badge badge-soft-success" style=""> {{ ucwords($order->order_type) }}</span>
                        @else
                            <span class="badge badge-soft-info" style=""> {{ ucwords($order->order_type) }}</span>
                        @endif
                    </p>
                    {{-- <a href="{{ route('generate.label', $order->id) }}" target="_blank"
                        class="btn btn-success btn-sm">Generate Label</a> --}}
                    <p class="mb-0 mt-2">
                        @if ($order->return == 1)
                            <span class="badge badge-soft-info" style="font-size: 1rem">Return Initiated </span>
                            @if (Auth::user()->isRetailer())
                                <a href="{{ route('accept-return', $order->id) }}" class="btn btn-sm btn-primary">Accept
                                    Return</a>
                            @endif
                        @elseif($order->return == 2)
                            <span class="badge badge-soft-warning" style="font-size: 1rem">Return Accepted by
                                Retailer</span>
                        @elseif($order->return == 4)
                            <span class="badge badge-soft-success" style="font-size: 1rem">Returned to Customer</span>
                        @endif


                    </p>
                    @if ($order->return != 0 && !is_null($order->returnOrder))
                        <p class="mb-0"><b>Return Reason: </b>{{ $order->returnOrder->reason }}</p>
                    @endif

                    @if(isset($order->delivery_information->signature))
                        <p class="mb-0"><b>Signature: </b><a target="_blank"
                                                             href="{{ $order->delivery_information->signature }}">View
                                Signature</a></p>
                    @endif
                    @if(isset($order->delivery_information->pacakge_image))
                        <p class="mb-0"><b>Package: </b><a target="_blank"
                                                             href="{{ $order->delivery_information->pacakge_image }}">View
                                Package</a></p>
                    @endif
                    @if(isset($order->delivery_information->reason))
                        <p class="mb-0"><b>Reason: </b>{{ $order->delivery_information->reason }}</p>
                    @endif
                    @if(isset($order->delivery_information->received_by))
                        <p class="mb-0"><b>Received By: </b>{{ $order->delivery_information->received_by }}</p>
                    @endif
                    @if(isset($order->delivery_information->name))
                        <p class="mb-0"><b>Name: </b>{{ $order->delivery_information->name }}</p>
                    @endif
                </div>
            </div>
            @if (Auth::user()->isAdmin())

                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-3 p-3 ">
                            @if (isset($order->block[0]))
                                <div class="card p-2 rounded"
                                     style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">

                                    <h4>Block Information</h4>

                                    <p class="mb-0"><b>Block Number: </b>{{ ucwords($order->block[0]->number) }}</p>
                                    <p class="mb-0"><b>Pickup Address: </b>{{ $order->block[0]->pickup_location }}</p>

                                    <p class="mb-0"><b>Pickup Time:
                                        </b>{{ date('F, j, Y H:i a', strtotime($order->block[0]->pickup_date_time)) }}
                                    </p>

                                </div>
                            @else
                                <div class="card p-2 rounded shadow"
                                     style="height: 10.3rem;box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
                                    <h4>Block Information</h4>
                                    <p class="text-danger">Not Assigned to any rider yet!</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-3 p-3">
                            @if (isset($order->collection[0]))
                                <div class="card p-2 rounded"
                                     style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">


                                    <h4>Collection Information</h4>

                                    <p class="mb-0"><b>Collection Number:
                                        </b>{{ ucwords($order->collection[0]->number) }}</p>
                                    <p class="mb-0"><b>Pickup Address: </b>{{ $order->retailer_address }}
                                    </p>
                                    {{-- <p class="mb-0"><b>Pickup Coordinates:
                                        </b>{{ $order->retailer->address }}</p> --}}
                                    <p class="mb-0"><b>Pickup Time:
                                        </b>{{ date('F, j, Y H:i a', strtotime($order->collection[0]->pickup_date_time)) }}
                                    </p>

                                </div>
                            @else
                                <div class="card p-2 rounded shadow"
                                     style="height: 10.3rem;box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
                                    <h4>Collection Information</h4>
                                    <p class="text-danger">Not Assigned to any collector yet!</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-3 p-3">
                            @if (isset($order->returnBlock[0]))
                                <div class="card p-2 rounded"
                                     style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">

                                    <h4>Return Block Information</h4>

                                    <p class="mb-0"><b>Block Number: </b>{{ ucwords($order->returnBlock[0]->number) }}
                                    </p>
                                    <p class="mb-0"><b>DropOff
                                            Address: </b>{{ $order->returnBlock[0]->pickup_location }}
                                    </p>

                                    <p class="mb-0"><b>Pickup Time:
                                        </b>{{ date('F, j, Y H:i a', strtotime($order->returnBlock[0]->pickup_date_time)) }}
                                    </p>

                                </div>
                            @else
                                <div class="card p-2 rounded"
                                     style="height: 10.3rem;box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
                                    <h4>Return Block Information</h4>
                                    <p class="text-danger">Not Assigned to any collector yet!</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-3 p-3">
                            @if (isset($order->toRetailerCollection[0]))
                                <div class="card p-2 rounded"
                                     style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">

                                    <h4>To Retailer Block Information</h4>
                                    <p class="mb-0"><b>Block
                                            Number: </b>{{ ucwords($order->toRetailerCollection[0]->number) }}
                                    </p>
                                    <p class="mb-0"><b>Pickup
                                            Address: </b>{{ $order->toRetailerCollection[0]->pickup_location }}
                                    </p>
                                    {{-- <p class="mb-0"><b>Pickup Coordinates:
                                        </b>{{ $order->returnBlock[0]->pickup_location_cordinates }}</p> --}}
                                    <p class="mb-0"><b>Pickup Time:
                                        </b>{{ date('F, j, Y H:i a', strtotime($order->toRetailerCollection[0]->pickup_date_time)) }}
                                    </p>

                                </div>
                            @else
                                <div class="card p-2 rounded"
                                     style="height: 10.3rem;box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
                                    <h4>Return Block Information</h4>
                                    <p class="text-danger">Not Assigned to any collector yet!</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-6 card">

                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-12">
            {{-- <div class="row text-center justify-content-center mb-5">
                <div class="col-xl-6 col-lg-8">
                    <h2 class="font-weight-bold">A Company Evolution</h2>
                    <p class="text-muted">We’re very proud of the path we’ve taken. Explore the history that made us the company we are today.</p>
                </div>
            </div> --}}

            <div class="row">
                <div class="col">
                    <div class="timeline-steps aos-init aos-animate" data-aos="fade-up">
                        @foreach ($order->statuses as $status)
                            <div class="timeline-step">
                                <div class="timeline-content" data-toggle="popover" data-trigger="hover"
                                     data-placement="top" title=""
                                     data-content="And here's some amazing content. It's very engaging. Right?"
                                     data-original-title="2003">
                                    <div class="inner-circle"></div>
                                    <p class="h6 mt-3 mb-1">{{ date('M, j Y H:i A', strtotime($status->created_at)) }}</p>
                                    <p class="h6 text-muted mb-0 mb-lg-0">{{ $status->status }}</p>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12 mt-4">
                <div class="card p-4 rounded cShadow ">
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                        <tr>
                            <th></th>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Barcode</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            @if($order->is_grocery != 1)

                                <th>Weight</th>
                                <th>Length</th>
                                <th>Dimension</th>
                                <th>Vol. Weight</th>
                            @endif
                            <th>QR</th>
                            <th>Label Info</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->items as $key => $i)
                            <tr>
                                <td><input type="checkbox" @if (!is_null($i->scan_info)) disabled @endif
                                    value="{{ $i->id }}" class="inputCheckbox" name="item_id[]"></td>
                                <td>{{ $i->sku }}</td>
                                <td>{{ $i->name }}</td>
                                <td>{{ $i->barcode }}</td>
                                <td>{{ $i->price }}</td>
                                <td>{{ $i->quantity }}</td>
                                @if($order->is_grocery != 1)

                                    <td>{{ $i->weight }}</td>
                                    <td>{{ $i->length }}</td>
                                    <td>{{ $i->dimension }}</td>
                                    <td>{{ $i->volumetric_weight }}</td>
                                @endif
                                <td>{{ isset($i->scan_info->qr_code) ? $i->scan_info->qr_code : '' }}</td>
                                <td>
                                    @if (is_null($i->scan_info))
                                        <span class="text-danger">Label not found</span>
                                    @else
                                    <form id="print-pdf-form" action="{{ route('print.item.label',$i->scan_info->qr_code) }}" method="get">
                                        <button type="button" class="btn btn-warning btn-sm print-label-button" onclick="printPdf('print-pdf-form')">Print Label</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @php
        $showGenerate = false;

        foreach($order->items as $it){
            if(!isset($it->scan_info->qr_code)){
                 $showGenerate = true;
                 break;
            }
        }
    @endphp
    @if($showGenerate)
        <form action="{{ route('generate.item.label') }}" id="generateItemLabelForm" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="submit" value="Generate Label" class="btn btn-primary btn-sm" id="submitBtn">
        </form>
    @endif
    {{-- <button >Submit</button> --}}
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var checkboxes = document.querySelectorAll('.inputCheckbox');


        // console.log();
        $('#submitBtn').click(function () {
            // console.log(checkboxes[0].checked);
            $.each(checkboxes, function (index, rowId) {

                if (rowId.checked === true) {
                    $('#generateItemLabelForm').append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'itemid[]')
                            .val(rowId.value)
                    );
                    // console.log(rowId.value);
                }
            });

        });
    </script>


<script>
     function printPdf(formId) {

        Swal.fire({
            title: 'Please wait...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                Swal.showLoading()
            },
        });

        var pdfUrl = document.getElementById(formId).action;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', pdfUrl, true);
        xhr.responseType = 'blob';
        xhr.onload = function() {
            if (xhr.status === 200) {
                var pdfBlob = new Blob([xhr.response], {
                    type: 'application/pdf'
                });
                var objectUrl = URL.createObjectURL(pdfBlob);
                var iframeEl = document.createElement('iframe');
                iframeEl.setAttribute('src', objectUrl);
                iframeEl.style.width = '100%';
                iframeEl.style.height = '100%';
                iframeEl.onload = function() {
                    // Close the loader
                    Swal.close();
                    // Hide the iframe before triggering print dialog
                    iframeEl.style.display = 'block';
                    // Open the print dialog
                    // Restore the visibility after print dialog closes
                    setTimeout(function() {
                        iframeEl.contentWindow.print();
                    }, 500);

                    setTimeout(function() {
                        iframeEl.style.display = 'none';
                    }, 1000);

                };
                document.body.appendChild(iframeEl);
            }
        };
        xhr.send();
    }
</script>
@endsection
