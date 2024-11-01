@extends('layouts.app')
@section('title', 'Create Return Block')
@section('content')
    <style>
        #map_canvas {
            height: 250px;
            width: 500px;
            margin: 0px;
            padding: 0px
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item">Orders</li>
                    <li class="breadcrumb-item active">Create Return Block</li>
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Create Return Block</h4>
                {{--                {{ $errors }} --}}


            </div>
        </div>
    </div>
    <div class="w-100">
        <div class="row justify-content-between">
            <div class="col-sm-8 bg-light p-4">
                <div class="row">
                    @foreach ($orders as $order)
                        <div class="col-sm-6 p-2">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6 text-left">
                                            {{ $order->retailer->user->name }}
                                        </div>
                                        <div class="col-6 text-end">
                                            Distance: <b>{{ $order->return_distance }}</b>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-body">
                                    <b style="text-decoration: underline;">End User Information</b>
                                    <ul style="padding-left: 0;list-style: none;">
                                        <li><b>Name: </b>{{ $order->enduser_name }}</li>
                                        <li><b>Email: </b>{{ $order->enduser_email }}</li>
                                        <li><b>Address: </b>{{ $order->enduser_address }}</li>
                                        <li><b>Mobile: </b>{{ $order->enduser_mobile }}</li>
                                    </ul>
                                </div>
                                <div class="card-footer text-center">
                                    <button class="btn btn-info btn-sm" onclick="showInfo({{ $order->id }})">Order
                                        Information</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
                    aria-labelledby="myLargeModalLabel" aria-modal="true" id="infoModal">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myLargeModalLabel">Large modal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h2>Retailer Information</h2>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="">Retailer Name</label>
                                        <p id="modalRetailerName"></p>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="">Support Email</label>
                                        <p id="modalRetailerSupportEmail"></p>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="">Support Mobile</label>
                                        <p id="modalRetailerSupportMobile"></p>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="">Website</label>
                                        <p id="modalRetailerWebsite"></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="">Address</label>
                                        <p id="modalRetailerAddress"></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="">Latitude, Longitude</label>
                                        <p id="modalRetailerLatLong"></p>
                                    </div>
                                </div>
                                <h2>Items</h2>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th>SKU</th>
                                            <th>Name</th>
                                            <th>Barcode</th>
                                            <th>Image</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Weight</th>
                                            <th>Length</th>
                                            <th>Width</th>
                                            <th>Height</th>
                                            <th>Volumetric Weight</th>
                                        </thead>
                                        <tbody id="modalItemTable">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>

            </div>
            <div class="col-sm-4 bg-light p-4">
                <h2>Collectors</h2>

                <form action="{{ route('post.return.block') }}" method="post">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ json_encode($orders->pluck('id')->toArray()) }}">
                    <div class="row">
                        <div class="form-group col-6 mb-3">
                            <label for="">Select Collector</label>
                            <select name="rider_id" id="dd_rider_id" class="form-control">
                                <option value="">Choose Collector</option>
                                @foreach ($riders as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }} | {{ $r->email }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="form-group col-6 mb-3">
                            <label for="">Select Date</label>
                            <input type="date" class="form-control" onchange="assign(this)">
                        </div> --}}
                        <div class="form-group col-6 mb-3">
                            <label for="">Pickup DateTime<span class="text-danger">*</span></label>
                            <input type="text" onchange="assign(this)" required name="pickup_date_time" pattern="\d{4}/\d{2}/\d{2} \d{2}:\d{2}"
                                class="form-control datetimepicker">
                                <span class="error-message text-danger" id="datetime-error"></span>
                        </div>
                        {{-- <div class="form-group col-6 mb-3">
                            <label for="">Dropoff DateTime<span class="text-danger">*</span></label>
                            <input type="datetime-local" required name="dropoff_date_time" class="form-control">
                        </div> --}}

                        <div class="form-group col-6 mb-3">
                            <label for="">Warehouse<span class="text-danger">*</span></label>

                            <select name="warehouse" class="form-control" readonly required id="warehouse_id">
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $w)
                                    <option @if($orders[0]->warehouse_id !== null && $orders[0]->warehouse_id == $w->id) selected @endif value="{{ $w->id }}">{{ $w->name }}</option>
                                @endforeach

                            </select>
                            <a style="float: right" href="javascript:void(0);" onclick="verifyDistance()">Verify distance</a>
                        </div>

                        <div class="form-group col-6 mb-3">
                            <label for="">Per Hour Charges<span class="text-danger">*</span></label>
                            <input type="number" required class="form-control" step="any" name="per_hour_earning">
                        </div>

                        <div class="form-group col-12">
                            <label for="">Endup Notes</label>
                            <textarea name="endup_notes" class="form-control" id="" cols="30" rows="3"></textarea>
                        </div>
                        <div class="form-group text-end mt-4">
                            <input type="submit" class="btn btn-primary btn-sm">
                        </div>
                    </div>
                </form>
                <span id="error_rider_ele" class="text-danger"></span>

                <div class="mt-2">
                    <table class="table table-bordered tablesch" style="display: none">
                        <thead>
                            <tr>
                                <th>Tracking</th>
                                <th>Pickup/Drop Time</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTable"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="">
                        @csrf
                        <input type="hidden" id="_modal_rider_id">
                        <div class="row">
                            <h5>Rider Contact: </h5>
                            <div class="form-group col-4">
                                <label for=""><b>Rider Name</b></label>
                                <p class="mb-0" id="_modal_rider_name"></p>

                            </div>

                            <div class="form-group col-4">
                                <label for=""><b>Rider Email</b></label>
                                <p class="mb-0" id="_modal_rider_email"></p>
                            </div>

                            <div class="form-group col-4">
                                <label for=""><b>Rider Mobile</b></label>
                                <p class="mb-0" id="_modal_rider_mobile"></p>
                            </div>

                        </div>
                        <hr>
                        <h5>Rider Schedule:</h5>
                        <div class="form-group col-4">
                            <label>Select Date</label>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqJ8Q9HP3d2Lb1lxxtUrbpPCWiF4esJ5U"></script>
    <script>
        var droplat;
        var droplong;





        function assign(e) {
            document.querySelector('#error_rider_ele').innerText = '';

            var id = document.querySelector('#dd_rider_id').value;
            var date = e.value;
            if (id == '') {
                document.querySelector('#error_rider_ele').innerText = 'Please Select Rider';
                return;
            }
            const tablebody = document.querySelector('#scheduleTable');
            fetch(`/rider/orders/${id}/${date}`)
                .then(res => res.json())
                .then(data => {
                    tablebody.innerHTML = '';
                    var orders = data.rider.assigned_orders;

                    if (orders.length > 0) {
                        orders.forEach(function(o) {
                            // console.log(o);
                            let html = document.createElement('tr');
                            html.innerHTML = `
                            <td>${o.track_number}</td>

                            <td>${o.pickup_date_time} / ${o.dropoff_date_time}</td>
       <td>${o.delivery_type}</td>`;
                            tablebody.appendChild(html);
                            document.querySelector('.tablesch').style.display = 'block';
                        })
                    } else {
                        document.querySelector('.tablesch').style.display = 'none';
                        document.querySelector('#error_rider_ele').innerText =
                            'No Schedule found for the selected rider on this date.';
                    }
                });


            // console.log(id, date);

        }
    </script>

    <script>
        function showInfo(id) {
            // console.log(id);

            fetch('/order/json/' + id)
                .then(res => res.json())
                .then(data => {
                    $('#modalItemTable').empty();
                    var order = data.order;
                    console.log(order);
                    $('#modalRetailerName').text(order.retailer.user.name);
                    $('#modalRetailerSupportEmail').text(order.retailer.support_email);
                    $('#modalRetailerSupportMobile').text(order.retailer.support_mobile);
                    $('#modalRetailerWebsite').text(order.retailer.website);
                    $('#modalRetailerAddress').text(order.retailer.address);
                    $('#modalRetailerLatLong').text(`${order.retailer.latitude}, ${order.retailer.latitude}`);


                    var items = order.items;

                    items.forEach(function(i) {
                        let html = document.createElement('tr');

                        html.innerHTML =
                            `<td>${i.sku}</td>
                            <td>${i.name}</td>
                            <td>${i.barcode}</td>
                            <td><a href="${i.image}" target="_blank">View Image</a></td>
                            <td>${i.price}</td>
                            <td>${i.quantity}</td>
                            <td>${i.weight}</td>
                            <td>${i.length}</td>
                            <td>${i.width}</td>
                            <td>${i.height}</td>
                            <td>${i.volumetric_weight}</td>`;

                        $('#modalItemTable').append(html);
                    })

                    $('#infoModal').modal('toggle');
                })
        }

        function verifyDistance() {
            var warehouse = $('#warehouse_id').val();
            var orders = {{ json_encode($orders->pluck('id')->toArray()) }};
            console.log(orders);

            // let formData = new FormData();
            // formData.append('pickup_cordinates', pickupCode);
            // formData.append('orders', orders);
            // console.log(JSON.stringify(formData));

            // /verify/distance
            // fetch('/verify/distance')

            const data = {
                orders: orders,
                warehouse: warehouse
            };

        fetch('/return/verify_distance', {
                    method: 'POST', // or 'PUT'
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data['error']) {
                        Snackbar.show({
                            pos: 'bottom-center',
                            text: data['error'],
                            backgroundColor: '#dc3545',
                            actionTextColor: '#fff'
                        });
                    } else {
                        window.location.reload();
                    }
                    // window.location.reload();
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        }
    </script>
@endsection
