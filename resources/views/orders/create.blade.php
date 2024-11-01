@extends('layouts.app')
@isset($order)
@section('title', 'Edit Order')
@else
@section('title', 'Add New Order')
@endisset
@section('content')
<style>
    label {
        margin-bottom: 0 !important
    }

    h5::after {
        display: block;
        content: '';
        margin-top: 5px;
        border-bottom: 1px solid #000;
    }

    .clonedata input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        display: none;
    }

</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-right">
                <ol class="breadcrumb m-0 ps-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                    @isset($order)
                    <li class="breadcrumb-item active">Edit Order</li>
                    @else
                    <li class="breadcrumb-item active">Add New Order</li>
                    @endisset
                </ol>
            </div>
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                @isset($order)
                <h4 class="mb-sm-0 font-size-18">Edit Order</h4>
                @else
                <h4 class="mb-sm-0 font-size-18">Add New Order</h4>
                @endisset



            </div>
        </div>
    </div>
</div>
<div class="container-fluid cShadow card p-4 rounded">
    @isset($order)
    <form action="{{ route('update.manual.order', $order->id) }}" method="post" enctype="multipart/form-data">
        @else
        <form action="{{ route('manual.order.post') }}" method="post" enctype="multipart/form-data">
            @endisset
            @csrf


            <div class="row">
                @if (Auth::user()->isAdmin())
                @if(!isset($order))


                <h5 class="mt-4">Order Information</h5>
                <div class="form-group col-sm-4">


                    <label for="">Retailer<span class="text-danger">*</span></label>


                    <select name="retailer" required class="form-select">
                        <option value="">Select Retailer</option>
                        @foreach ($retailers as $r)
                        <option @if (isset($order) && $order->retailer_id == $r->id) selected @endif value="{{ $r->id }}">
                            {{ ucwords($r->user->name) }}</option>
                        @endforeach
                    </select>

                </div>
                @elseif (isset($order) && $order->collection_ready == 0)
                <h5 class="mt-4">Order Information</h5>
                <div class="form-group col-sm-4">


                    <label for="">Retailer<span class="text-danger">*</span></label>


                    <select name="retailer" required class="form-select">
                        <option value="">Select Retailer</option>
                        @foreach ($retailers as $r)
                        <option @if (isset($order) && $order->retailer_id == $r->id) selected @endif value="{{ $r->id }}">
                            {{ ucwords($r->user->name) }}</option>
                        @endforeach
                    </select>

                </div>
                @else
                <input type="hidden" readonly name="retailer" value="{{ $order->retailer_id }}}}">
                @endif
                @else
                <input type="hidden" readonly name="retailer" value="{{ Auth::user()->retailer->id }}">
                @endif
                <div class="form-group col-sm-4">
                    <label for="">Order Number<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input @if (isset($order)) value="{{ $order->order_number }}" @endif type="text" class="form-control" required name="order_number" id="order_number">

                        <span class="input-group-text" id="generateOrNum"><i class="mdi mdi-refresh"></i></span>
                    </div>
                </div>

                <div class="form-group col-sm-4">
                    <label for="">Order Type<span class="text-danger">*</span></label>
                    <select name="order_type" required class="form-select">
                        <option>Select Order Type</option>
                        @foreach ($packages as $p)
                        <option @if (isset($p) && !empty($p->id)) value="{{$p->id}}" @endif   @if (isset($order) && $order->order_type == $p->name) selected @endif>{{$p->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-sm-4 mt-3">
                    <label for="">Dropoff Country<span class="text-danger">*</span></label>
                    <input @isset($order) value="{{ $order->dropoff_country }}" @endisset type="text" name="dropoff_country" required class="form-control">
                </div>
                {{-- <div class="form-group col-sm-4 mt-3">
                    <label for="">Dropoff City<span class="text-danger">*</span></label>
                    <input @isset($order) value="{{ $order->dropoff_city }}" @endisset
                type="text" name="dropoff_city" required class="form-control">
            </div> --}}
            <div class="form-group col-sm-4 mt-3">
                <label for="">Dropoff Postal<span class="text-danger">*</span></label>
                <input @isset($order) value="{{ $order->dropoff_postal }}" @endisset type="text" name="dropoff_postal" id="dropoff_postal" required class="form-control">
                <ul id="postal_list" style="display: none"></ul>
            </div>
            <div class="form-group col-sm-4 mt-3">
                <label for="">Payment Type<span class="text-danger">*</span></label>
                <input type="text" class="form-control" value="{{ 'card' }}" disabled>
                {{-- <select name="payment_type" required class="form-select">
                        <option>Select Payment Type</option>
                        <option @if (isset($order) && $order->payment_type == 'card') selected @endif value="card">Card</option>
                        <option @if (isset($order) && $order->payment_type == 'cash') selected @endif value="cash">Cash</option>
                    </select> --}}
            </div>

            <h5 class="mt-4">Customer Information</h5>

            <div class="form-group col-sm-4 mt-3">
                <label for="">Customer Name<span class="text-danger">*</span></label>
                <input type="text" @if (isset($order)) value="{{ $order->enduser_name }}" @endif name="enduser_name" required class="form-control">
            </div>
            <div class="form-group col-sm-4 mt-3">
                <label for="">Customer Email<span class="text-danger">*</span></label>
                <input type="email" @if (isset($order)) value="{{ $order->enduser_email }}" @endif name="enduser_email" required class="form-control">
            </div>
            <div class="form-group col-sm-4 mt-3">
                <label for="">Customer Mobile<span class="text-danger">*</span></label>
                <input type="number" @if (isset($order)) value="{{ $order->enduser_mobile }}" @endif name="enduser_mobile" required class="form-control">
            </div>
            <div class="form-group col-sm-4 mt-3">
                <label for="">Customer Address<span class="text-danger">*</span></label>
                {{-- <textarea name="enduser_address" required class="form-control" id="" cols="10" rows="3">
@if (isset($order))
{{ $order->enduser_address }}
                @endif
                </textarea> --}}
                <input type="text" class="form-control" id="google_address" name="enduser_address" required value="@isset($order){{ $order->enduser_address }}@endisset">
            </div>

            <h5 class="mt-4">Items Information</h5>
            {{-- <button id="button2" onlick="duplicate2()" style="float:right;">+</button> --}}
            @isset($order)
            @foreach ($order->items as $key => $item)
            <div>
                <div class="row clonedata">
                    <div class="div col-md-1 form-group my-2">
                        <label for="">SKU</label>
                        <input type="text" name="sku[]" value="{{ $item->sku }}" class="form-control">
                    </div>
                    <div class="div col-md-3 form-group my-2">
                        <label for="">Name<span class="text-danger">*</span></label>
                        <input type="text" name="name[]" required value="{{ $item->name }}" class="form-control">
                    </div>
                    {{-- <div class="div col-md-2 form-group my-2">
                                    <label for="">Barcode<span class="text-danger">*</span></label>
                                    <input type="text" name="barcode[]" required value="{{ $item->barcode }}"
                    class="form-control">
                </div> --}}

                <div class="div col-md-2 form-group my-2">
                    <label for="">Price<span class="text-danger">*</span></label>
                    <input type="number" step="any" required name="price[]" value="{{ $item->price }}" class="form-control">
                </div>
                <div class="div col-md-1 form-group my-2">
                    <label for="">Quantity<span class="text-danger">*</span></label>
                    <input type="number" step="any" name="quantity[]" value="{{ $item->quantity }}" required class="form-control">
                </div>
                <div class="div col-md-1 form-group my-2">
                    <label for="">Weight<span class="text-danger">*</span></label>
                    <input type="number" step="any" required name="weight[]" value="{{ $item->weight }}" class="form-control">
                </div>
                <div class="div col-md-1 form-group my-2">
                    <label for="">Length<span class="text-danger">*</span></label>
                    <input type="number" step="any" required name="length[]" value="{{ $item->length }}" class="form-control">
                </div>
                <div class="div col-md-1 form-group my-2">
                    <label for="">Height<span class="text-danger">*</span></label>
                    <input type="number" step="any" required name="height[]" value="{{ $item->height }}" class="form-control">
                </div>
                <div class="div col-md-1 form-group my-2">
                    <label for="">Width<span class="text-danger">*</span></label>
                    <input type="number" step="any" required name="width[]" value="{{ $item->width }}" class="form-control">
                </div>
                <div class="col-md-1 tn-buttons form-group d-flex align-items-end gap-1 pb-2">
                    <button type="button" class="mb-xs mr-xs btn btn-info addmore "><i class="fa fa-plus"></i></button>
                    @if ($key > 0)
                    <button type='button' class='mb-xs mr-xs btn btn-info removemore'><i class='fa fa-minus'></i></button>
                    @endif
                </div>
            </div>
</div>
@endforeach
@else
<div>
    <div class="row clonedata">
        <div class="div col-md-1 form-group my-2">
            <label for="">SKU</label>
            <input type="text" name="sku[]" class="form-control">
        </div>
        <div class="div col-md-3 form-group my-2">
            <label for="">Name<span class="text-danger">*</span></label>
            <input type="text" name="name[]" required class="form-control">
        </div>
        {{-- <div class="div col-md-2 form-group my-2">
                                <label for="">Barcode<span class="text-danger">*</span></label>
                                <input type="text" name="barcode[]" required class="form-control">
                            </div> --}}

        <div class="div col-md-2 form-group my-2">
            <label for="">Price<span class="text-danger">*</span></label>
            <input type="number" step="any" required name="price[]" class="form-control">
        </div>
        <div class="div col-md-1 form-group my-2">
            <label for="">Quantity<span class="text-danger">*</span></label>
            <input type="number" step="any" required name="quantity[]" class="form-control">
        </div>
        <div class="div col-md-1 form-group my-2">
            <label for="">Weight<span class="text-danger">*</span></label>
            <input type="number" step="any" required name="weight[]" class="form-control">
        </div>
        <div class="div col-md-1 form-group my-2">
            <label for="">Length<span class="text-danger">*</span></label>
            <input type="number" step="any" required name="length[]" class="form-control">
        </div>
        <div class="div col-md-1 form-group my-2">
            <label for="">Height<span class="text-danger">*</span></label>
            <input type="number" step="any" required name="height[]" class="form-control">
        </div>
        <div class="div col-md-1 form-group my-2">
            <label for="">Width<span class="text-danger">*</span></label>
            <input type="number" step="any" required name="width[]" class="form-control">
        </div>
        <div class="col-md-1 tn-buttons form-group d-flex align-items-end gap-1 pb-2">
            <button type="button" class="mb-xs mr-xs btn btn-info addmore "><i class="fa fa-plus"></i></button>


        </div>
    </div>
</div>
@endisset
<div id="packagingappendhere">
</div>
<div class="form-group col-sm-12 mb-2 mt-2">
    <input type="submit" value="Submit" class="btn btn-primary">
</div>

</div>
</form>
</div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places"></script>
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script>
    document.getElementById("generateOrNum").addEventListener("click", function() {
        document.getElementById("order_number").value = '';
        const d = new Date();
        let time = d.getTime();
        orderNumber = time;
        document.getElementById("order_number").value = orderNumber;
    });

    $(document).on('click', '.addmore', function(ev) {
        var $clone = $(this).parent().parent().clone(true);
        var $newbuttons =
            "<button type='button' class='mb-xs mr-xs btn btn-info addmore'><i class='fa fa-plus'></i></button><button type='button' class='mb-xs mr-xs btn btn-info removemore'><i class='fa fa-minus'></i></button>";
        $clone.find('.tn-buttons').html($newbuttons).end().appendTo($('#packagingappendhere'));
    });

    $(document).on('click', '.removemore', function() {
        $(this).parent().parent().remove();
    });

</script>

<script>
    var input = document.getElementById('google_address');
    input.addEventListener('input', updateValue);


    function updateValue() {

        var n = 5;
        if (input.value.length < n) {

            return
        } else {

            initialize();
        }
    }
    // if (input.value.length > 12) {


    function initialize() {



        var autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener('place_changed', function() {

            var place = autocomplete.getPlace();

            // place variable will have all the information you are looking for.

            $('#lat').val(place.geometry['location'].lat());

            $('#long').val(place.geometry['location'].lng());

        });

    }
    // }

</script>
@endsection
