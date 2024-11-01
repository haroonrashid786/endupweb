@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
    .hidden {
        display: none;
    }
    .btn-success {
    background-color: #8bd2a4; /* Green color */
    color: white;
    border: none;
}

.btn-success:hover {
    background-color: #218838; /* Darker green color on hover */
}
</style>
    <div class="w-100">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="row">
                    <div class="col-lg-12 d-none">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <img src="{{ URL::asset('/images/Endupfav.png') }}" alt=""
                                                    class="avatar-md rounded-circle img-thumbnail">
                                            </div>
                                            <div class="flex-grow-1 align-self-center">
                                                <div class="text-muted">
                                                    <p class="mb-2">Welcome to Endup</p>
                                                    <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-4 align-self-center">
                                        <div class="text-lg-center mt-4 mt-lg-0">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div>
                                                        <p class="text-muted text-truncate mb-2">Orders</p>
                                                        <h5 class="mb-0">48</h5>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div>
                                                        <p class="text-muted text-truncate mb-2">Riders/Collectors</p>
                                                        <h5 class="mb-0">40</h5>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div>
                                                        <p class="text-muted text-truncate mb-2">Retailers</p>
                                                        <h5 class="mb-0">18</h5>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    {{-- <div class="col-lg-4 d-none d-lg-block">
                                        <div class="clearfix mt-4 mt-lg-0">
                                            <div class="dropdown float-end">
                                                <button class="btn btn-primary" type="button" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="bx bxs-cog align-middle me-1"></i> Setting
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#">Action</a>
                                                    <a class="dropdown-item" href="#">Another action</a>
                                                    <a class="dropdown-item" href="#">Something else</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                                <!-- end row -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4">
                        <div class="card bg-primary bg-soft">
                            <div>
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-3">
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            @if (Auth::user()->isRetailer())
                                                <p>{{ Auth::user()->retailer->user->name }}</p>
                                            @else
                                                <p>EndUp</p>
                                            @endif


                                            {{-- <ul class="ps-3 mb-0">
                                                <li class="py-1">7 + Layouts</li>
                                                <li class="py-1">Multiple apps</li>
                                            </ul> --}}
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-xs me-3">
                                                <span
                                                    class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                                    <i class="bx bx-copy-alt"></i>
                                                </span>
                                            </div>
                                            <h5 class="font-size-14 mb-0">Orders</h5>
                                        </div>
                                        <div class="text-muted mt-4">
                                            @if (Auth::user()->isRetailer())
                                                <h4>{{ count(Auth::user()->retailer->orders) }}
                                                    {{-- <i
                                                        class="mdi mdi-chevron-up ms-1 text-success"></i> --}}
                                                </h4>
                                            @else
                                                <h4>{{ \App\Models\Orders::count() }}
                                                    {{-- <i
                                                        class="mdi mdi-chevron-up ms-1 text-success"></i> --}}
                                                </h4>
                                            @endif

                                            {{-- <div class="d-flex">
                                                <span class="badge badge-soft-success font-size-12"> + 0.2% </span> <span
                                                    class="ms-2 text-truncate">From previous period</span>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (Auth::user()->isRetailer())
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-xs me-3">
                                                    <span
                                                        class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                                        <i class="bx bx-copy-alt"></i>
                                                    </span>
                                                </div>
                                                <h5 class="font-size-14 mb-0">Total Payable to EndUp (This Month)</h5>
                                            </div>
                                            <div class="text-muted mt-4">
                                                <h4>£ {{ $cur }}
                                                    {{-- <i
                                                class="mdi mdi-chevron-up ms-1 text-success"></i> --}}
                                                </h4>

                                                {{-- <div class="d-flex">
                                                <span class="badge badge-soft-success font-size-12"> + 0.2% </span> <span
                                                    class="ms-2 text-truncate">From previous period</span>
                                            </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-xs me-3">
                                                    <span
                                                        class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                                        <i class="bx bx-copy-alt"></i>
                                                    </span>
                                                </div>
                                                <h5 class="font-size-14 mb-0">Secret Key</h5>
                                            </div>
                                            <div class="text-muted mt-4">
                                                <h4 id="contentToToggleH4">
                                                <code>{{ auth()->user()->retailer->secret_key }}</code>
                                                </h4>
                                            </div>
                                            <button id="toggleButton" class="btn btn-success">Show Key</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (Auth::user()->isAdmin())
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-xs me-3">
                                                    <span
                                                        class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                                        <i class="bx bx-cycling "></i>
                                                    </span>
                                                </div>
                                                <h5 class="font-size-14 mb-0">Riders</h5>
                                            </div>
                                            <div class="text-muted mt-4">
                                                <h4>{{ \App\Models\Rider::count() }}
                                                    {{-- <i class="mdi mdi-chevron-up ms-1 text-success"></i></h4> --}}
                                                    {{-- <div class="d-flex">
                                                    <span class="badge badge-soft-success font-size-12"> + 0.2% </span>
                                                    <span class="ms-2 text-truncate">From previous period</span>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-xs me-3">
                                                    <span
                                                        class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                                                        <i class="bx bx-store"></i>
                                                    </span>
                                                </div>
                                                <h5 class="font-size-14 mb-0">Retailers</h5>
                                            </div>
                                            <div class="text-muted mt-4">
                                                <h4>{{ \App\Models\Retailer::count() }}
                                                    {{-- <i class="mdi mdi-chevron-up ms-1 text-success"></i></h4> --}}

                                                    {{-- <div class="d-flex">
                                                    <span class="badge badge-soft-warning font-size-12"> 0% </span> <span
                                                        class="ms-2 text-truncate">From previous period</span>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                        <!-- end row -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Latest Orders</h4>
                                <div class="table-responsive">
                                    <table class="table align-middle table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>

                                                <th class="align-middle">Order Info</th>
                                                <th class="align-middle">Customer Name</th>
                                                <th class="align-middle">Date</th>
                                                <th class="align-middle">Shipping Charges</th>
                                                <th class="align-middle">Collector Delivery Status</th>
                                                <th class="align-middle">Rider Delivery Status</th>
                                                <th class="align-middle">View Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $o)
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
                                <!-- end table-responsive -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div id="chart">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- @if (Auth::user()->isAdmin()) --}}
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Orders/Zone Analytics</h4>

                                <div style="position: relative;">
                                    <div id="piechart">
                                    </div>

                                    <div class="resize-triggers">
                                        <div class="expand-trigger">
                                            <div style="width: 401px; height: 242px;"></div>
                                        </div>
                                        <div class="contract-trigger"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    {{-- @endif --}}
                </div>

                
            </div>
        </div>

        <script>
            var datesArr = <?php echo json_encode($datesArr); ?>;
            let finalDateArr = [];
            datesArr.map(ele => {
                finalDateArr.push({
                    date: ele.date,
                    delivered: ele.delivered ? ele.delivered : 0,
                    pending: ele.pending ? ele.pending : 0,
                })
            });
            let dates = finalDateArr.map(itm => itm.date)

            dates = new Set(dates)

            let output = []
            dates.forEach(date => {
                let date_group = finalDateArr.filter(itm => itm.date == date)
                // console.log(date_group.length)

                let new_array = {
                    date: '',
                    delivered: 0,
                    pending: 0
                }
                date_group.forEach(grp_obj => {
                    new_array.date = grp_obj.date
                    new_array.delivered = new_array.delivered + grp_obj.delivered
                    new_array.pending = new_array.pending + grp_obj.pending
                })
                output.push(new_array)
            })
            console.log(output)


            let datep = output.map(ite => ite.date);
            let delivered = output.map(ite => ite.delivered);
            let pending = output.map(ite => ite.pending);

            // console.log(finalDateArr);
            // var datesseven = <?php echo json_encode($dates); ?>;
            // let dates = datesseven.map(ite => ite.date);

            var options = {
                chart: {
                    height: 500,
                    type: "line",
                    stacked: false,
                    toolbar: {
                        show: false,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                title: {
                    text: 'Orders (Last 7 Days)',
                    align: 'left'
                },
                colors: ["#FF1654", "#247BA0"],
                series: [{
                        name: "Pending Orders",
                        data: pending
                    },
                    {
                        name: "Delivered Orders",
                        data: delivered
                    }
                ],
                stroke: {
                    width: [4, 4],
                    curve: 'smooth',
                },
                plotOptions: {
                    bar: {
                        columnWidth: "20%"
                    }
                },
                xaxis: {
                    categories: datep
                },
                yaxis: {
                    min: 0,
                    max: 200,
                    tickAmount: 5,
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    x: {
                        show: false
                    }
                },
                legend: {
                    tooltipHoverFormatter: function(val, opts) {
                        return val + ' - ' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + ''
                    }
                },
                markers: {
                    size: 0,
                    hover: {
                        sizeOffset: 6
                    }
                },
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);

            chart.render();


            var pieoptions = {
                series: <?php echo json_encode($ordersCount); ?>,
                labels: <?php echo json_encode($zoneNames); ?>,

                chart: {
                    type: 'donut',
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var piechart = new ApexCharts(document.querySelector("#piechart"), pieoptions);
            piechart.render();
        </script>
<script>
var contentH4 = document.getElementById("contentToToggleH4");
var toggleButton = document.getElementById("toggleButton");
contentH4.classList.add("hidden");
toggleButton.addEventListener("click", function () {
    contentH4.classList.toggle("hidden");    
    if (contentH4.classList.contains("hidden")) {
        toggleButton.textContent = "Show Key";
    } else {
        toggleButton.textContent = "Hide Key";
    }
});
</script>
    @endsection
