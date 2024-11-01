<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Track Your Order | {{ $order->order_number }}</title>
<script src="https://cdn.tailwindcss.com"></script>


<script src="https://cdn.socket.io/3.0.0/socket.io.min.js"></script>

<style>
    .shadow-lg {
        box-shadow: 0 1rem 3rem rgba(143, 197, 162, 0.575) !important
    }

    @media screen and (max-width: 991px) {
        .w-half {
            width: 75% !important;
        }
    }
</style>
    {{-- <button type="button"
        class="inline-block rounded bg-blue-600 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-blue-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-blue-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-blue-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] "
        data-te-toggle="modal" data-te-target="#exampleModal" data-te-ripple-init data-te-ripple-color="light">
        Launch demo modal
    </button> --}}
    <!-- Modal -->
    {{-- <div data-te-modal-init
        class="fixed left-0 top-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none"
        id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div data-te-modal-dialog-ref
            class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 min-[576px]:max-w-[500px]">
            <div
                class="min-[576px]:shadow-[0_0.5rem_1rem_rgba(#000, 0.15)] pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none ">
                <div
                    class="flex flex-shrink-0 items-center justify-between rounded-t-md border-b-2 border-neutral-100 border-opacity-100 p-4 ">
                    <!--Modal title-->
                    <h5 class="text-xl font-medium leading-normal text-neutral-800 "
                        id="exampleModalLabel">
                        Order Information
                    </h5>
                    <!--Close button-->
                    <button type="button"
                        class="box-content rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
                        data-te-modal-dismiss aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!--Modal body-->
                <div class="relative flex-auto p-4" data-te-modal-body-ref>
                    <div class="row">

                        <div class="col-sm-4">
                        <b>Your Name:</b> {{ $order->grocery_order['users'][0]['full_name'] }}<br>
                        <b>Mobile Number:</b> {{ $order->grocery_order['users'][0]['mobile_number'] }}<br>
                        </div>
                        <div class="col-sm-4">
                        <b>Address:</b> {{  $order->grocery_order['users'][0]['address'] }}<br>
                        <b>Postal:</b> {{ $order->grocery_order['users'][0]['postal_code'] }}<br>
                        </div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->grocery_order['items'] as $i)
                            <tr>
                                <td>{{ $i['product']['sku'] }}</td>
                                @if(isset($i['product']['images'][0]))
                                <td><img src="{{ $i['product']['images'][0]['url'] }}" width="20" style="border-radius: 25rem"></td>
                                @else
                                <td></td>
                                @endif
                                <td>{{ $i['product']['title'] }}</td>
                                <td>{{ $i['quantity'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!--Modal footer-->
                <div
                    class="flex flex-shrink-0 flex-wrap items-center justify-end rounded-b-md border-t-2 border-neutral-100 border-opacity-100 p-4 ">
                    <button type="button"
                        class="inline-block rounded bg-blue-100 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-blue-700 transition duration-150 ease-in-out hover:bg-blue-accent-100 focus:bg-blue-accent-100 focus:outline-none focus:ring-0 active:bg-blue-accent-200"
                        data-te-modal-dismiss data-te-ripple-init data-te-ripple-color="light">
                        Close
                    </button>
                    <button type="button"
                        class="ml-1 inline-block rounded bg-blue-600 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-blue-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-blue-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-blue-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] "
                        data-te-ripple-init data-te-ripple-color="light">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div> --}}
<div class="flex flex-col h-full">
    <div class="py-6 border-b">
        <div class="container mx-auto">
            <div class="grid grid-cols-2">
                <div class="">
                    <img src="{{ URL::asset('/images/logo.png') }}" class="h-[35px] object-contain">
                </div>
                <div class=" flex justify-end items-center">
                    <h4 class="text-end mb-0">Your Delivery</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-1 relative">
        <div id="map" class="w-full h-full"></div>
        {{-- <h4 class="text-center">Expected Delivery In: <span id="timetodeli"></span></h4> --}}
        <div
            class="absolute bottom-0 card w-[95vw] md:w-[35vw]  py-4 mx-auto px-4 mb-[2rem] shadow-lg border border-success border-0 -translate-x-1/2 left-1/2 bg-white rounded-md border">
            <div
                class="absolute top-0 -translate-x-1/2 -translate-y-1/2 bg-white px-3 py-2 rounded-1 border left-1/2 w-fit">
                <img src="{{ URL::asset('/images/logo.png') }}" alt="" class="h-[35px]">
            </div>

            <div class="text-sm flex flex-col gap-1 mt-[2rem]">
                <p class="text-center mb-4">Your order will be delivered in <span id="timetodeli">{{ $time_per_order }}</span></p>
                <p class="mb-0"><b>Order Number: </b>{{ $order->order_number }}</p>
                <p class="mb-0"><b>Name: </b>{{ $order->enduser_name }}</p>
                <p class="mb-0"><b>Address: </b>{{ $order->enduser_address }}</p>
            </div>

            <div
                class="flex gap-2 items-center justify-between w-[95%] md:w-[75%] mx-auto border rounded px-3 py-2 mt-4">
                <div class="flex items-center gap-2">
                    <div>
                        <img src="https://source.unsplash.com/random/?user"
                            class="rounded-full h-[60px] w-[60px] object-cover">
                    </div>
                    <div class="flex-1">
                        <p class="text-xs m-0 text-gray-400">Shipped By:</p>
                        <p class="m-0 text-sm font-bold">{{ $riderInfo->user->name }} </h1>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <a href="tel:{{ $riderInfo->mobile }}">
                        <div class="p-2 rounded-full border w-fit">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="#8fc5a2" stroke-width="2"
                                fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg>
                        </div>
                    </a>
                    <a href="sms:{{ $riderInfo->mobile }}">
                        <div class="p-2 rounded-full border w-fit">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="#8fc5a2" stroke-width="2"
                                fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>
<script type="module">
    // Initialization for ES Users
import {
  Modal,
  Ripple,
  initTE,
} from "tw-elements";

initTE({ Modal, Ripple });
</script> --}}

<script src="https://maps.googleapis.com/maps/api/js?sensor=false&key={{ env('GOOGLE_MAP_KEY') }}"></script>
<script>
    const riderId = '{{ $rider }}';
    var drops = JSON.parse('{!! $location !!}');

    var curr = '{{ $current }}';

    var lat = parseFloat(curr.split(", ")[0]);
    var long = parseFloat(curr.split(", ")[1]);


    const socket = io('https://crm.enduptech.com');




    socket.on('connect', () => {
        console.log('socket connected');
    });

    socket.on("connect_error", (err) => {
        console.log(err); // prints the message associated with the error
    });
    socket.on("liveLocation:rider", (data) => {
        //console.log(data);
        if(riderId == data.rider){
            console.log('same');
            curr = data.coordinates;
            lat = parseFloat(curr.split(", ")[0]);
            long = parseFloat(curr.split(", ")[1]);
            var pointA = new google.maps.LatLng(drops[0]);
            var latlng = new google.maps.LatLng(lat, long);
            myMarker.setPosition(latlng);



            map.setCenter({
                lat: lat,
                lng: long
            });
        }

    });

    var mapOptions = {
        center: new google.maps.LatLng(lat, long),
        zoom: 10,
        mapTypeId: google.maps.MapTypeId.satellite
    }
    map = new google.maps.Map(document.getElementById("map"), mapOptions);


    // function changeMarkerPosition(marker) {
    //     var latlng = new google.maps.LatLng(40.748774, -73.985763);
    //     marker.setPosition(latlng);
    // }



    // let dropOffMarker = new google.maps.Marker({
    //     position: {
    //         lat: warehouseLat,
    //         lng: warehouseLong
    //     },
    //     icon: '{{ asset('warehouse.png') }}',
    //     map,
    // });
    // console.log(dropOffMarker);
    let myMarker = new google.maps.Marker({
        position: {
            lat,
            lng: long
        },
        icon: '{{ asset('RIDER.png') }}',
        map,
    });
    myMarker.setMap(map);


    var dropOFff = '{{ $order->dropoff_coordinates }}';

    droplat = parseFloat(dropOFff.split(", ")[0]);
    droplong = parseFloat(dropOFff.split(", ")[1]);
    console.log(dropOFff, droplat, droplong);

    let dropMarker = new google.maps.Marker({
        position: {
            lat:droplat,
            lng: droplong
        },
        icon: '{{ asset('DROP.png') }}',
        map,
    });
    dropMarker.setMap(map);






</script>
