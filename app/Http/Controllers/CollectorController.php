<?php

namespace App\Http\Controllers;

use App\Helpers\OptimizeTrait;
use App\Models\Collection;
use App\Models\CollectionOrderDistance;
use App\Models\CollectorOrderDeliveryInformation;
use App\Models\ItemLabel;
use App\Models\Items;
use App\Models\OrderDeliveryInformation;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\ReturnBlock;
use App\Models\ReturnOrderDeliveryInformation;
use App\Models\User;
use App\Models\Roles;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Resource\Item;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Hash;
use App\Helpers\SecretKey;
use App\Classes\FilePaths;

class CollectorController extends Controller
{
    use OptimizeTrait;
    public function assignOrders(Request $request)
    {
        // if ($request->has('return') && $request->return == 1) {
        //     if ($request->filter == 'completed') {
        //         $user = User::with([
        //         'assignedCollectionsReturn',
        //             'assignedCollectionsReturn.orders' => function ($q) {
        //                 $q->whereIn('return_delivery_status', ['Returned to Retailer']);
        //             },
        //             'assignedCollectionsReturn.orders.itemsScanInfo',
        //             'assignedCollectionsReturn.orders.items.scan_info',
        //             'assignedCollectionsReturn.orders.collector_delivery_information'
        //         ])
        //             ->whereHas('assignedCollectionsReturn.orders', function ($q) {
        //                 $q->whereIn('return_delivery_status', ['Returned to Retailer']);
        //             })
        //             ->find(Auth::user()->id);
        //     } else {
        //         $user = User::with([
        //             'assignedCollectionsReturn',
        //             'assignedCollectionsReturn.orders',
        //             'assignedCollectionsReturn.orders.itemsScanInfo',
        //             'assignedCollectionsReturn.orders.items.scan_info',
        //             'assignedCollectionsReturn.orders.collector_delivery_information'
        //         ])->find(Auth::user()->id);
        //     }

        //     $assigned = $user->assignedCollectionsReturn;

        //     $warehouse_ids = [];

        //     foreach ($assigned as $key => $assign) {
        //         foreach ($assign->orders->pluck('warehouse_id') as $warehouse_id) {
        //             array_push($warehouse_ids, $warehouse_id);
        //         }
        //     }
        // } else {
            if ($request->filter == 'completed') {
                $user = User::with([
                    'assignedCollections' => function ($query) {
                        $query->whereHas('orders', function ($subQuery) {
                            $subQuery->where('collector_delivery_status', 'In Warehouse');
                        });
                    },
                    'assignedCollections.orders',
                    'assignedCollections.orders.itemsScanInfo',
                    'assignedCollections.orders.items.scan_info',
                    'assignedCollections.orders.collector_delivery_information'
                ])
                    ->whereHas('assignedCollections.orders', function ($q) {
                        $q->whereIn('collector_delivery_status', ['In Warehouse', 'In Wirehouse']);
                    })
                    ->find(Auth::user()->id);
            } else {
                $user = User::with([
                    'assignedCollections' => function ($query) {
                        $query->whereHas('orders', function ($subQuery) {
                            $subQuery->where('collector_delivery_status', '!=', 'In Warehouse');
                        });
                    },
                    'assignedCollections.orders',
                    'assignedCollections.orders.itemsScanInfo',
                    'assignedCollections.orders.items.scan_info',
                    'assignedCollections.orders.collector_delivery_information'
                ])->find(Auth::user()->id);
            }


            $warehouse_ids = [];
            if (isset($user->assignedCollections)) {
                $getReturns = $user->assignedCollections;
            } else {
                $getReturns = [];
            }


            if(isset($getReturns) && empty($getReturns)){
                return response()->json(['message' => 'No Collections Found', 'status' => 404], 404);
            }

            $assignedBlocksIds = $user->assignedCollections->pluck('id');
            $assigned = Collection::with('orders.itemsScanInfo')->whereIn('id',$assignedBlocksIds)->where('return', 0)->get();

            foreach ($assigned as $key => $assign) {

                foreach ($assigned[$key]->orders->pluck('warehouse_id') as $warehouse_id) {
                    array_push($warehouse_ids, $warehouse_id);
                }
            }
            // dd(array_unique($warehouse_ids)[0]);
        // }
        // dd($assigned->orders);
        if (count($assigned) < 1) {
            return response()->json(['message' => 'No Collections Found', 'status' => 404], 404);
        }
        // if(isset(array_unique($warehouse_ids)[0])){
        // $warehouse = Warehouse::find(array_unique($warehouse_ids)[0]);
        // }


        $assigned->mapWithKeys(function ($order) {
            $order['order_details'] = $order->order;
            return $order;
        });

        $assigned->mapWithKeys(function ($order) {
            unset($order['order']);
            return $order;
        });
        // $WAREHOUSE = Warehouse::first();
        foreach ($assigned->toArray() as $akey => $a) {

            $assigned[$akey]['warehouse_contact'] = '090078601';
            $totalPackages[$akey] = [];
            $totalVerifiedPackages[$akey] = [];
            foreach ($assigned[$akey]->orders->toArray() as $okey => $o) {

                $warehouse[$akey] = Warehouse::find($o['warehouse_id']);
                $assigned[$akey]['warehouse'] = $warehouse[$akey]->name;
                // $assigned[$akey]['orders'][$okey]['scanInfo'] = [];
                $arrayScanInfo[$okey] = [];
                $qrs[$okey] = array_values(array_unique(array_column($o['items_scan_info'], 'qr_code')));
                foreach ($qrs[$okey] as $ikey => $qr) {

                    $scan[$ikey] = ItemLabel::where(['order_id' => $o['id'], 'qr_code' => $qr])->get();

                    $arrayScanInfo[$okey][$ikey] = $scan[$ikey]->first();
                    $arrayScanInfo[$okey][$ikey]['items'] = $scan[$ikey]->pluck('item');
                    unset($arrayScanInfo[$okey][$ikey]['item']);
                    // array_push(, $scan[$ikey]);
                    // unset($assigned[$akey]['orders'][$okey]['scanInfo'][$ikey]['item']);
                    // unset($assigned[$akey]['orders'][$okey]['scanInfo']['item']['items_scan_info']);
                    $assigned[$akey]['orders'][$okey]['scan_info'] = $arrayScanInfo[$okey];
                    if ($request->has('return') && $request->return == 1) {
                        $assigned[$akey]['orders'][$okey]['total_packages'] = ItemLabel::where(['order_id' => $o['id']])->count();
                        $assigned[$akey]['orders'][$okey]['verified_packages'] = ItemLabel::where(['order_id' => $o['id'], 'verified_by_return_rider_warehouse' => 1])->count();
                    }
                }
                if ($request->has('return') && $request->return == 1) {
                    array_push($totalPackages[$akey], $assigned[$akey]['orders'][$okey]['total_packages']);
                    array_push($totalVerifiedPackages[$akey], $assigned[$akey]['orders'][$okey]['verified_packages']);
                }
                // dd($assigned[$akey]['orders'][$okey]);
                //  = ;/
                // dd();
                //  array_push($assigned[$akey]->orders, $arrayScanInfo[$okey]);
                // $this->super_unique($assigned[$akey]['orders'][$okey]['scanInfo'], 'qr_code');
                // dd($o)
                if ($request->has('return') && $request->return == 1) {
                    $assigned[$akey]['total_packages'] = array_sum($totalPackages[$akey]);
                    $assigned[$akey]['verified_packages'] = array_sum($totalVerifiedPackages[$akey]);
                }
            }
        }

        $response['blocks'] = $assigned;

        foreach ($response['blocks'] as $skey => $a) {
            $warehouse = [];
            $location[$skey] = [];
            foreach ($a['orders'] as $key => $o) {
                // dd($o);
                $warehouse = Warehouse::find($o->warehouse_id);
                $location[$skey][$key] = $o['collector_pickup_coordinates'];
            }
            // if(isset($warehouse)){


            if (isset($warehouse->coordinates)) {
                array_push($location[$skey], $warehouse->coordinates);
            }
            // }
            $response['blocks'][$skey]['location'] = $location[$skey];

        }
        $blocksArray = $response['blocks']->toArray();
        usort($blocksArray, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $response['blocks'] = OptimizeTrait::paginatedData($blocksArray, 6);
        $response['status'] = 200;
        return response()->json($response, 200);
    }

    public function updateOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $order = Orders::find($request->order_id);
        if ($request->has('return') && $request->return == 1) {
            $order->return_delivery_status = $request->status;
        } else {
            $order->collector_delivery_status = $request->status;
        }
        $order->save();

        $order->statuses()->create([
            'status' => $request->status,
        ]);

        return response()->json(['order' => $order, 'status' => 200], 200);
    }

    public function verifyCollection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection' => 'required',
            'order_id' => 'required',
            'qr_code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $check = Orders::whereRelation('collection', 'collections.id', '=', $request->collection)
            ->where(['id' => $request->order_id])
            ->whereHas('collector_delivery_information', function ($q) use ($request) {
                $q->where('order_qr_code', $request->qr_code);
            })->first();
        // dd($check);
        if (!is_null($check)) {

            // $order = Orders::find($request->order_id);
            // $order->verified_by_rider = 1;
            // $order->save();

            $check->items()->update(['verified_by_collector' => 1]);
            return response()->json(['message' => true, 'status' => 200], 200);
        }
        return response()->json(['message' => false, 'status' => 400], 400);
    }

    public function verifyItems(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order' => 'required',
            'item_id' => 'required',
            'item_barcode' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $check = Orders::whereHas('items', function ($i) use ($request) {
            $i->where('id', $request->item_id)->where('barcode', $request->item_barcode);
        })
            ->where('id', $request->order)
            ->first();

        if (!is_null($check)) {
            $item = Items::find($request->item_id);
            $item->verified_by_collector = 1;
            $item->save();
            return response()->json(['message' => true, 'status' => 200], 200);
        }
        return response()->json(['message' => false, 'status' => 404], 404);
        // dd($check);
    }

    public function caculateEarnings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $distances = [];
//        $collection = Collection::with([
//            'orders' => function($k){
//                $k->where('collector_delivery_status', '!=' ,'In Warehouse');
//            },
//            'orders.orderDistance',
//            'orders.items',
//            'orders.itemsScanInfo',
//            'orders.items.scan_info',
//            'orders.collector_delivery_information'
//        ])->whereHas('orders', function ($q){
//            $q->where('collector_delivery_status', '!=' ,'In Warehouse');
//        })->find($request->collection_id);

        $collection = Collection::with([
            'orders' => function ($query) {
                $query->where('collector_delivery_status', '!=', 'In Warehouse')
                    ->with('orderDistance', 'items', 'itemsScanInfo', 'items.scan_info', 'collector_delivery_information');
            }
        ])->whereHas('orders', function ($query) {
            $query->where('collector_delivery_status', '!=', 'In Warehouse');
        })->find($request->collection_id);

        // dd($request->collection_id);
        if (!is_null($collection)) {
            $drop = null;
            if (count(array_unique($collection->orders->pluck('collector_dropoff_coordinates')->toArray())) < 2) {
                $drop = array_unique($collection->orders->pluck('collector_dropoff_coordinates')->toArray())[0];
            }

            $locations = [];
            $last_pickup = '';
            $getDistance = [];

            $time_per_order = [];
            if (is_null($collection->total_earnings) || is_null($collection->total_time)) {
                foreach ($collection->orders as $key => $o) {
                    $last_pickup = $o['collector_pickup_coordinates'];
                    $dropoff[$key] = (isset($collection->orders[$key + 1])) ? $collection->orders[$key + 1]['collector_pickup_coordinates'] : $drop;
                    $getDistance[$key] = 'https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&units=imperial&origins=' . $last_pickup . '&destinations=' . $dropoff[$key] . '&key=' . env('GOOGLE_MAP_KEY');

                    $responsedis[$key] = Http::get($getDistance[$key])->json();

                    if (isset($responsedis[$key]['rows'][0]['elements'][0]['distance']['text'])) {
                        $distanceAdd[$key] = new CollectionOrderDistance();
                        $distanceAdd[$key]->collection_id = $collection->id;
                        $distanceAdd[$key]->order_id = $o['id'];
                        // $distanceAdd[$key]->distance = $responsedis[$key]['rows'][0]['elements'][0]['distance']['text'];
                        if (strpos($responsedis[$key]['rows'][0]['elements'][0]['distance']['text'], 'ft') !== false) {
                            $distanceAdd[$key]->distance = 0;
                        }else{
                            $distanceAdd[$key]->distance = $responsedis[$key]['rows'][0]['elements'][0]['distance']['text'];
                        }
                        $distanceAdd[$key]->save();
                    }
                    $time_per_order[$key] = isset($responsedis[$key]['rows'][0]['elements'][0]['duration_in_traffic']['value']) ? $responsedis[$key]['rows'][0]['elements'][0]['duration_in_traffic']['value'] : 0;
                }

                $time_per_order_total = array_sum($time_per_order);
                $time_per_order_total += 900;
                $hours = floor($time_per_order_total / 3600);
                $minutes = floor(($time_per_order_total / 60) % 60);
                // $seconds = array_sum($time_per_order_total) % 60;
                $time = sprintf('%02d:%02d', $hours, $minutes);

                $totalTime = $time;
                // $totalTime = date('H:i', strtotime('+15 minutes', strtotime(date('H:i', strtotime($totalTime)))));
                $perHourEarning = $collection->per_hour_earning;
                $timeparts = explode(':', $totalTime);
                $pay = $timeparts[0] * $perHourEarning + $timeparts[1] / 60 * $perHourEarning;
                $collection->total_earnings = round($pay);
                $collection->total_time = $totalTime;
                $collection->save();

                $distances = $collection->ordersDistance;
            } else {
                $pay = $collection->total_earnings;
                $totalTime = $collection->total_time;
                $distances = $collection->ordersDistance;
            }

            $location = [];
            if (is_countable(array_unique($collection->orders->pluck('warehouse_id')->toArray())) && count(array_unique($collection->orders->pluck('warehouse_id')->toArray())) > 0) {
                $warehouse = Warehouse::where('id', array_unique($collection->orders->pluck('warehouse_id')->toArray())[0])->first();

                $drops = [];
                foreach ($collection->orders as $key => $o) {
                    array_push($location, $o['collector_pickup_coordinates']);
                }
                array_push($location, $warehouse->coordinates);
            }
            $collection['location'] = $location;

            $collection['warehouse'] = $warehouse->name;
            $collection['warehouse_contact'] = '090078601';
            $totalPackages = [];
            $totalVerifiedPackages = [];
            foreach ($collection->orders->toArray() as $okey => $o) {

                $arrayScanInfo[$okey] = [];
                $qrs[$okey] = array_values(array_unique(array_column($o['items_scan_info'], 'qr_code')));
                foreach ($qrs[$okey] as $ikey => $qr) {

                    $scan[$ikey] = ItemLabel::where(['order_id' => $o['id'], 'qr_code' => $qr])->get();

                    $arrayScanInfo[$okey][$ikey] = $scan[$ikey]->first();
                    $arrayScanInfo[$okey][$ikey]['items'] = $scan[$ikey]->pluck('item');
                    unset($arrayScanInfo[$okey][$ikey]['item']);


                    $collection['orders'][$okey]['scan_info'] = $arrayScanInfo[$okey];

                    $collection['orders'][$okey]['total_packages'] = 0;
                    $collection['orders'][$okey]['verified_packages'] = 0;

                    $collectorOrderDistance[$ikey] = CollectionOrderDistance::where('collection_id', $collection->id)->where('order_id', $o['id'])->first();
                    if (!is_null($collectorOrderDistance[$ikey])) {
                        $collection['orders'][$okey]['extra_distance'] = $collectorOrderDistance[$ikey]->distance;
                    }
                }

                array_push($totalPackages, $collection['orders'][$okey]['total_packages']);
                array_push($totalVerifiedPackages, $collection['orders'][$okey]['verified_packages']);

                $collection['total_packages'] = array_sum($totalPackages);
                $collection['verified_packages'] = array_sum($totalVerifiedPackages);

            }
        }

        return response()->json(['total_earnings' => round($pay), 'total_time' => $totalTime, 'distances' => $distances, 'block' => $collection, 'status' => 200], 200);
    }


    public function caculateEarningsReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $collection = Collection::with(
            'orders',
            'orders.orderDistance',
            'orders.items',
            'orders.itemsScanInfo',
            'orders.items.scan_info',
            'orders.collector_delivery_information'
        )->find($request->collection_id);
        // dd($request->collection_id);
        if (!is_null($collection)) {
            $drop = null;
            if (count(array_unique($collection->orders->pluck('collector_pickup_coordinates')->toArray())) < 2) {
                $drop = array_unique($collection->orders->pluck('collector_pickup_coordinates')->toArray())[0];
            }

            $locations = [];
            $last_pickup = '';
            $getDistance = [];
            $distances = [];
            $time_per_order = [];
            if (is_null($collection->total_earnings) || is_null($collection->total_time)) {
                foreach ($collection->orders as $key => $o) {
                    $last_pickup = $o['collector_dropoff_coordinates'];
                    $dropoff[$key] = (isset($collection->orders[$key + 1])) ? $collection->orders[$key + 1]['collector_dropoff_coordinates'] : $drop;
                    $getDistance[$key] = 'https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&units=imperial&origins=' . $last_pickup . '&destinations=' . $dropoff[$key] . '&key=' . env('GOOGLE_MAP_KEY');

                    $responsedis[$key] = Http::get($getDistance[$key])->json();
                    if (isset($responsedis[$key]['rows'][0]['elements'][0]['distance']['text'])) {
                        $distanceAdd[$key] = new CollectionOrderDistance();
                        $distanceAdd[$key]->collection_id = $collection->id;
                        $distanceAdd[$key]->order_id = $o['id'];
                        if (strpos($responsedis[$key]['rows'][0]['elements'][0]['distance']['text'], 'ft') !== false) {
                            $distanceAdd[$key]->distance = 0;
                        }else{
                            $distanceAdd[$key]->distance = $responsedis[$key]['rows'][0]['elements'][0]['distance']['text'];
                        }
                        $distanceAdd[$key]->save();
                    }
                    $time_per_order[$key] = isset($responsedis[$key]['rows'][0]['elements'][0]['duration_in_traffic']['value']) ? $responsedis[$key]['rows'][0]['elements'][0]['duration_in_traffic']['value'] : 0;
                }

                // $totalTime = gmdate("H:i", array_sum($time_per_order));
                // dd($totalTime);
                // $totalTime = date('H:i', strtotime('+15 minutes', strtotime(date('H:i', strtotime($totalTime)))));
                $time_per_order_total = array_sum($time_per_order);
                $time_per_order_total += 900;
                $hours = floor($time_per_order_total / 3600);
                $minutes = floor(($time_per_order_total / 60) % 60);
                // $seconds = array_sum($time_per_order_total) % 60;
                $time = sprintf('%02d:%02d', $hours, $minutes);

                $totalTime = $time;
                $perHourEarning = $collection->per_hour_earning;
                $timeparts = explode(':', $totalTime);
                $pay = $timeparts[0] * $perHourEarning + $timeparts[1] / 60 * $perHourEarning;
                $collection->total_earnings = round($pay);
                $collection->total_time = $totalTime;
                $collection->save();
                $distances = $collection->ordersDistance;
            } else {
                $pay = $collection->total_earnings;
                $totalTime = $collection->total_time;
                $distances = $collection->ordersDistance;
            }

            $location = [];
            if (is_countable(array_unique($collection->orders->pluck('warehouse_id')->toArray())) && count(array_unique($collection->orders->pluck('warehouse_id')->toArray())) > 0) {
                $warehouse = Warehouse::where('id', array_unique($collection->orders->pluck('warehouse_id')->toArray())[0])->first();

                $drops = [];
                foreach ($collection->orders as $key => $o) {
                    array_push($location, $o['collector_pickup_coordinates']);
                }
                array_push($location, $warehouse->coordinates);
            }
            $collection['location'] = $location;

            $collection['warehouse'] = $warehouse->name;
            $collection['warehouse_contact'] = '090078601';
            $totalPackages = [];
            $totalVerifiedPackages = [];
            foreach ($collection->orders->toArray() as $okey => $o) {

                $arrayScanInfo[$okey] = [];
                $qrs[$okey] = array_values(array_unique(array_column($o['items_scan_info'], 'qr_code')));
                foreach ($qrs[$okey] as $ikey => $qr) {

                    $scan[$ikey] = ItemLabel::where(['order_id' => $o['id'], 'qr_code' => $qr])->get();

                    $arrayScanInfo[$okey][$ikey] = $scan[$ikey]->first();
                    $arrayScanInfo[$okey][$ikey]['items'] = $scan[$ikey]->pluck('item');
                    unset($arrayScanInfo[$okey][$ikey]['item']);


                    $collection['orders'][$okey]['scan_info'] = $arrayScanInfo[$okey];

                    $collection['orders'][$okey]['total_packages'] = 0;
                    $collection['orders'][$okey]['verified_packages'] = 0;

                    $collectorOrderDistance[$ikey] = CollectionOrderDistance::where('collection_id', $collection->id)->where('order_id', $o['id'])->first();
                    if (!is_null($collectorOrderDistance[$ikey])) {
                        $collection['orders'][$okey]['extra_distance'] = $collectorOrderDistance[$ikey]->distance;
                    }
                }

                array_push($totalPackages, $collection['orders'][$okey]['total_packages']);
                array_push($totalVerifiedPackages, $collection['orders'][$okey]['verified_packages']);

                $collection['total_packages'] = array_sum($totalPackages);
                $collection['verified_packages'] = array_sum($totalVerifiedPackages);

            }
            // dd();
            return response()->json(['total_earnings' => round($pay), 'total_time' => $totalTime, 'distances' => $distances, 'block' => $collection, 'status' => 200], 200);
        }
    }
    public function updateDeliveryInformation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);

        }

        if (strtolower($request->status) == 'returned to retailer') {
            $validator = Validator::make($request->all(), [
                // 'signature' => 'required',
                'pacakge_image' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
            }

            $order = CollectorOrderDeliveryInformation::with('order')->where('order_id', $request->id)->first();

            $filename = time() . 'pacakge_image' . '.webp';
            Image::make($request->file('pacakge_image'))->encode('webp', 100)->save(public_path('/collector_pacakge_images/' . $filename), 60, 'webp');

            $pacakge_image_path = url('/return_collector_pacakge_images/' . $filename);
            $order->return_pacakge_image = $pacakge_image_path;
            // $order->return_signature = 'null';
            $order->save();

            $order->order->update([
                'return_delivery_status' => $request->status
            ]);

            $order->order->statuses()->create([
                'status' => $request->status
            ]);

            $orders = Orders::with('collector_delivery_information')->find($request->id);
            return response()->json(['orders' => $orders, 'status' => 200, 'message' => 'Order has been returned to retailer'], 200);
        }

        if (strtolower($request->status) == 'in warehouse') {
            $validator = Validator::make($request->all(), [
                // 'signature' => 'required',
                'pacakge_image' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
            }

            $order = CollectorOrderDeliveryInformation::with('order')->where('order_qr_code', $request->qr)->first();

            // if($order->order->is_premium == 1 && $order->order->premium_code !== $request->code){
            //     return response()->json(['status' => 400, 'message'=>'Invalid Code'], 400);
            // }
            // dd($request->all());
            $filename = time() . 'pacakge_image' . '.webp';
            Image::make($request->file('pacakge_image'))->encode('webp', 100)->save(public_path('/collector_pacakge_images/' . $filename), 60, 'webp');

            $pacakge_image_path = url('/collector_pacakge_images/' . $filename);
            $order->pacakge_image = $pacakge_image_path;
            $order->signature = 'null';
            $order->save();

            $order->order->update([
                'collector_delivery_status' => $request->status
            ]);

            $orders = Orders::with('collector_delivery_information')->find($order->order->id);
            return response()->json(['orders' => $orders, 'status' => 200, 'message' => 'Order Delivered'], 200);
        } else {

            $validator = Validator::make($request->all(), [
                'pacakge_image' => 'required',
                'reason' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);

            }

            $order = CollectorOrderDeliveryInformation::with('order')->where('order_qr_code', $request->qr)->first();
            // dd($request->all());
            $filename = time() . '.webp';
            Image::make($request->file('pacakge_image'))->encode('webp', 100)->save(public_path('/collector_pacakge_images/' . $filename), 60, 'webp');

            $path = url('/collector_pacakge_images/' . $filename);
            $order->pacakge_image = $path;
            $order->reason = $request->reason;
            $order->save();

            $order->order->update([
                'collector_delivery_status' => $request->status
            ]);

            $orders = Orders::with('collector_delivery_information')->find($order->order->id);
            return response()->json(['orders' => $orders, 'status' => 200, 'message' => 'Order cancelled'], 200);

        }

    }

    public function updateMultiple(Request $request)
    {

        if ($request->has('return') && $request->return == "1") {
            $orders = ReturnOrderDeliveryInformation::with('order')->whereIn('order_id', $request->id)->get();
            // dd($orders);

            foreach ($orders as $key => $order) {
                $filename[$key] = time() . '-' . $order->order_id . 'pacakge_image' . '.webp';
                // Image::make($request->file('pacakge_image'))->encode('webp', 100)->save(public_path('/return_pacakge_images/' . $filename[$key]), 60, 'webp');
                Image::make(file_get_contents($request->file('pacakge_image')))->encode('webp', 100)->save(public_path('/return_pacakge_images/' . $filename[$key]), 60, 'webp');

                $pacakge_image_path[$key] = url('/return_pacakge_images/' . $filename[$key]);
                $order->pacakge_image = $pacakge_image_path;
                // $order->signature = 'null';
                $order->save();

                $order->order->update([
                    'return_delivery_status' => $request->status,
                    'return_to_warehouse' => $request->return
                ]);

                $order->order->statuses()->create([
                    'status' => $request->status,
                ]);
            }

            $collection = ReturnBlock::with('orders')->find($request->collection_id);
            // dd($collection);\
            return response()->json(['collection' => $collection, 'status' => 200], 200);
        } else {
            $orders = CollectorOrderDeliveryInformation::with('order')->whereIn('order_id', $request->id)->get();

            if ($request->has('signature')) {
                $signature_file = '_' . time() . '_signature_file' . '.' . $request->signature->extension();

                $signature_path = OptimizeTrait::uploadFile(FilePaths::collectorSignatureImage, $request->signature, 1, $signature_file);
            } else {
                $signature_path = null;
            }


            $orderUpdates = [];
            $statuses = [];

            $filename = '_' . time() . '_package_file.webp';

            $pacakge_image_path = OptimizeTrait::uploadFile(FilePaths::collectorPackageImage, $request->pacakge_image, 1, $filename);

            foreach ($orders as $key => $order) {
                $orderUpdates[] = [
                    'id' => $order->id,
                    'pacakge_image' => (isset($pacakge_image_path) && !empty($pacakge_image_path)) ? $pacakge_image_path : null,
                    'signature' => $signature_path,
                ];

                $statuses[] = [
                    'order_id' => $order->order_id,
                    'status' => $request->status,
                ];
            }

            // Perform bulk updates and inserts
            CollectorOrderDeliveryInformation::upsert($orderUpdates, 'id');
            $orderIds = collect($orders)->pluck('order_id')->toArray();
            Orders::whereIn('id', $orderIds)->update(['collector_delivery_status' => $request->status]);
            OrderStatus::insert($statuses);

            $collection = Collection::with('orders')->find($request->collection_id);
            // dd($collection);\
            return response()->json(['collection' => $collection, 'status' => 200], 200);
        }
        // return response()
    }

    public function qrScan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection' => 'required',
            'order_id' => 'required',
            'qr_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        if ($request->has('return') && $request->return == 1) {
            $collection = ReturnBlock::with('orders.itemsScanInfo')
                ->whereHas('orders', function ($q) use ($request) {
                    $q->whereHas(
                        'items',
                        function ($i) use ($request) {
                                $i->whereHas(
                                    'scan_info',
                                    function ($s) use ($request) {
                                                    $s->where('qr_code', $request->qr_code);
                                                }
                                );
                            }
                    )->where('orders.id', $request->order_id);
                })->where('id', $request->collection)->first();


            if (is_null($collection)) {
                return response()->json(['message' => false, 'status' => 400], 400);
            }


            $labels = ItemLabel::with('item')->where('qr_code', $request->qr_code)->get();
            if (count($labels) > 0) {
                foreach ($labels as $l) {
                    // if ($request->has('return') && $request->return == 2) {
                    //     $l->verified_by_return_rider_warehouse = 1;
                    //     $l->save();
                    // } else {

                    $l->verified_by_return_collector = 1;
                    $l->save();
                    // }
                }
            }

            $order = Orders::with('itemsScanInfo.item')->find($request->order_id);

            $response = [];


            $qrs = ItemLabel::where('order_id', $request->order_id)->get()->pluck('qr_code')->toArray();

            foreach (array_values(array_unique($qrs)) as $key => $QR) {

                $scan[$key] = ItemLabel::where(['qr_code' => $QR])->get();
                $response[$key] = $scan[$key]->first()->toArray();
                $response[$key]['items'] = $scan[$key]->pluck('item');

            }
        } else {
            if ($request->has('return') && $request->return == 2) {
                $collection = Collection::with('orders.itemsScanInfo')
                    ->whereHas('orders', function ($q) use ($request) {
                        $q->whereHas(
                            'items',
                            function ($i) use ($request) {
                                    $i->whereHas(
                                        'scan_info',
                                        function ($s) use ($request) {
                                                        $s->where('qr_code', $request->qr_code);
                                                    }
                                    );
                                }
                        );
                    })->where('id', $request->collection)->first();
            } else {
                $collection = Collection::with('orders.itemsScanInfo')
                    ->whereHas('orders', function ($q) use ($request) {
                        $q->whereHas(
                            'items',
                            function ($i) use ($request) {
                                    $i->whereHas(
                                        'scan_info',
                                        function ($s) use ($request) {
                                                        $s->where('qr_code', $request->qr_code);
                                                    }
                                    );
                                }
                        )->where('orders.id', $request->order_id);
                    })->where('id', $request->collection)->first();

            }
            if (is_null($collection)) {
                return response()->json(['message' => false, 'status' => 400], 400);
            }


            $labels = ItemLabel::with('item')->where('qr_code', $request->qr_code)->get();
            if (count($labels) > 0) {
                foreach ($labels as $l) {
                    if ($request->has('return') && $request->return == 2) {
                        $l->verified_by_return_rider_warehouse = 1;

                    } elseif ($request->has('return') && $request->return == 3) {
                        $l->verified_by_return_rider = 1;

                    } else {
                        $l->verified_by_collector = 1;

                    }
                    $l->save();
                }
            }

            if ($request->has('return') && $request->return == 2) {
                $block = Collection::with(
                    'orders.itemsScanInfo',
                    'orders.items',
                    'orders.items.scan_info',
                    'orders.collector_delivery_information'
                )->where('id', $request->collection)->first();
                $warehouse_ids = [];


                foreach ($block->orders->pluck('warehouse_id') as $warehouse_id) {
                    array_push($warehouse_ids, $warehouse_id);
                }

                $warehouse = Warehouse::find(array_unique($warehouse_ids)[0]);

                $block['warehouse'] = $warehouse->name;
                $block['warehouse_contact'] = '090078601';
                $totalPackages = [];
                $totalVerifiedPackages = [];
                foreach ($block->orders->toArray() as $okey => $o) {

                    $arrayScanInfo[$okey] = [];
                    $qrs[$okey] = array_values(array_unique(array_column($o['items_scan_info'], 'qr_code')));
                    foreach ($qrs[$okey] as $ikey => $qr) {

                        $scan[$ikey] = ItemLabel::where(['order_id' => $o['id'], 'qr_code' => $qr])->get();

                        $arrayScanInfo[$okey][$ikey] = $scan[$ikey]->first();
                        $arrayScanInfo[$okey][$ikey]['items'] = $scan[$ikey]->pluck('item');
                        unset($arrayScanInfo[$okey][$ikey]['item']);

                        $block['orders'][$okey]['scan_info'] = $arrayScanInfo[$okey];
                        $block['orders'][$okey]['total_packages'] = ItemLabel::where(['order_id' => $o['id']])->count();
                        $block['orders'][$okey]['verified_packages'] = ItemLabel::where(['order_id' => $o['id'], 'verified_by_return_rider_warehouse' => 1])->count();

                    }
                    array_push($totalPackages, $block['orders'][$okey]['total_packages']);
                    array_push($totalVerifiedPackages, $block['orders'][$okey]['verified_packages']);
                }

                $block['total_packages'] = (is_countable($totalPackages) && count($totalPackages) > 0) ? array_sum($totalPackages) : 0;
                $block['verified_packages'] = (is_countable($totalVerifiedPackages) && count($totalVerifiedPackages) > 0) ? array_sum($totalVerifiedPackages) : 0;
                return response()->json(['block' => $block, 'message' => true, 'status' => 200], 200);
            }

            $order = Orders::with('itemsScanInfo.item')->find($request->order_id);

            $response = [];


            $qrs = ItemLabel::where('order_id', $request->order_id)->get()->pluck('qr_code')->toArray();

            foreach (array_values(array_unique($qrs)) as $key => $QR) {

                $scan[$key] = ItemLabel::where(['qr_code' => $QR])->get();
                $response[$key] = $scan[$key]->first()->toArray();
                $response[$key]['items'] = $scan[$key]->pluck('item');

            }
        }
        return response()->json(['data' => $this->super_unique($response, 'qr_code'), 'message' => true, 'status' => 200], 200);
    }


    function super_unique($array, $key)
    {
        $temp_array = [];
        foreach ($array as &$v) {
            if (!isset($temp_array[$v[$key]]))
                $temp_array[$v[$key]] = &$v;
        }
        $array = array_values($temp_array);
        return $array;

    }


    public function earnings()
    {


        // $collectionsQuery = Collection::select('collections.id', 'per_hour_earning', 'collections.created_at')
        //     ->where('user_id', Auth::user()->id)
        //     ->join('collect_orders', 'collections.id', '=', 'collect_orders.collection_id')
        //     ->join('orders', 'orders.id', '=', 'collect_orders.order_id')
        //     ->whereHas('orders', function ($query) {
        //         $query->whereIn('collector_delivery_status', ['In Warehouse', 'Returned to Retailer'])
        //             ->orWhere('return_delivery_status', 'Returned to Retailer');
        //     });

        // $returnBlocksQuery = ReturnBlock::select('return_blocks.id', 'per_hour_earning', 'return_blocks.created_at')
        //     ->where('user_id', Auth::user()->id)
        //     ->join('return_block_orders', 'return_blocks.id', '=', 'return_block_orders.return_block_id')
        //     ->join('orders', 'orders.id', '=', 'return_block_orders.order_id')
        //     ->where('return_delivery_status', 'In Warehouse');


        $collectionsQuery = Collection::select('collections.id', 'collections.number', 'total_earnings', 'collections.created_at')
            ->where('user_id', Auth::user()->id)
            ->join('collect_orders', 'collections.id', '=', 'collect_orders.collection_id')
            ->join('orders', 'orders.id', '=', 'collect_orders.order_id')
            ->where(function ($query) {
                $query->whereHas('orders', function ($query) {
                    $query->whereIn('collector_delivery_status', ['In Warehouse', 'Returned to Retailer'])
                        ->orWhereIn('return_delivery_status', ['In Warehouse', 'Returned to Retailer', 'Out for return delivery to retailer']);
                });
            });
        // ->whereHas('orders', function ($query) {
        //     $query->whereIn('collector_delivery_status', ['In Warehouse', 'Returned to Retailer'])
        //         ->orWhereIn('return_delivery_status', ['In Warehouse', 'Returned to Retailer', 'Out for return delivery to retailer'])
        // });

        $returnBlocksQuery = ReturnBlock::select('return_blocks.id', 'return_blocks.number', 'total_earnings', 'return_blocks.created_at')
            ->where('user_id', Auth::user()->id)
            ->join('return_block_orders', 'return_blocks.id', '=', 'return_block_orders.return_block_id')
            ->join('orders', 'orders.id', '=', 'return_block_orders.order_id')
            ->where(function ($query) {
                $query->whereHas('orders', function ($query) {
                    $query->whereIn('return_delivery_status', ['In Warehouse', 'Returned to Retailer', 'Out for return delivery to retailer']);
                });
            });
        // ->whereHas('orders', function ($query) {
        //     $query->whereIn('return_delivery_status', ['In Warehouse', 'Returned to Retailer', 'Out for return delivery to retailer']);
        // });

        $results = $collectionsQuery->union($returnBlocksQuery)->get();
        // dd($results->pluck('number')->toArray());
        if (isset($results)) {
            $array_data = $results->toArray();
            $unique_blocks = array_unique(array_column($array_data, 'number'), SORT_REGULAR);

            $total_earnings = 0;
            foreach ($array_data as $block) {
                if (in_array($block['number'], $unique_blocks)) {
                    $total_earnings += $block['total_earnings'];
                    // Remove block from unique list to avoid duplicate summing
                    $key = array_search($block['number'], $unique_blocks);
                    unset($unique_blocks[$key]);
                }
            }

            $response['total_earnings'] = $total_earnings;
            $response['total_deliveries'] = count(array_values(array_unique(array_column($array_data, 'number'))));
        } else {
            $response['total_earnings'] = 0;
            $response['total_deliveries'] = 0;
        }
        return response()->json(['data' => $response, 'status' => 200], 200);


    }

    public function changeOrderVerificationStatusReturnCollector(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $order = Orders::find($request->order_id);
        if ($request->has('return') && ($request->return == '1' || $request->return == 1)) {
            $order->verified_by_return_rider_warehouse = $request->status;
            $order->save();
        }
        $order->verified_by_return_collector = $request->status;
        $order->save();
        return response()->json(['status' => 200], 200);
    }

    public function getAllEarnings(Request $request)
    {


        if ($request->has('month') && !empty($request->month) && $request->has('year') && !empty($request->year)) {
            $month = $request->month;
            $year = $request->year;

            $collectionsQuery = Collection::select('collections.id', 'collections.number', 'collections.total_earnings', 'collections.created_at')
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('YEAR(collections.created_at)'), '=', $request->year)
                ->where(\DB::raw('MONTH(collections.created_at)'), '=', $request->month)
                ->join('collect_orders', 'collections.id', '=', 'collect_orders.collection_id')
                ->join('orders', 'orders.id', '=', 'collect_orders.order_id')
                ->where(function ($query) {
                    $query->whereHas('orders', function ($query) {
                        $query->whereIn('collector_delivery_status', ['In Warehouse', 'Returned to Retailer'])
                        ->orWhereIn('return_delivery_status', ['In Warehouse', 'Returned to Retailer', 'Out for return delivery to retailer']);
                    });
                });

            $returnBlocksQuery = ReturnBlock::select('return_blocks.id', 'return_blocks.number', 'total_earnings', 'return_blocks.created_at')
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('YEAR(return_blocks.created_at)'), '=', $request->year)
                ->where(\DB::raw('MONTH(return_blocks.created_at)'), '=', $request->month)
                ->join('return_block_orders', 'return_blocks.id', '=', 'return_block_orders.return_block_id')
                ->join('orders', 'orders.id', '=', 'return_block_orders.order_id')
                ->where(function ($query) {
                    $query->whereHas('orders', function ($query) {
                        $query->whereIn('return_delivery_status', ['In Warehouse', 'Returned to Retailer', 'Out for return delivery to retailer']);
                    });
                });
            $collectionsIDs = $collectionsQuery->distinct()->pluck('id')->toArray();
            $returnBlockIDs = $returnBlocksQuery->distinct()->pluck('id')->toArray();

            $results = $collectionsQuery->union($returnBlocksQuery)->get();



            if (count($results) > 0) {
                $array_data = $results->toArray();
                $unique_blocks = array_unique(array_column($array_data, 'number'), SORT_REGULAR);

                $total_earnings = 0;
                foreach ($array_data as $block) {
                    if (in_array($block['number'], $unique_blocks)) {
                        $total_earnings += $block['total_earnings'];
                        // Remove block from unique list to avoid duplicate summing
                        $key = array_search($block['number'], $unique_blocks);
                        unset($unique_blocks[$key]);
                    }
                }

                $monthly['total'] = $total_earnings;
                $monthly['month'] = date('F', mktime(0, 0, 0, $request->month, 10));
                $monthly['year'] = $request->year;

                $collections = $this->getCollectionsWithOrders($request->month, $request->year, $collectionsIDs);
                $blocks = $this->getReturnBlocksWithOrders($request->month, $request->year, $returnBlockIDs);

                $totalBlocks = collect($collections)->merge($blocks);

                $collections = $totalBlocks;
            } else {
                $monthly = [
                    'month' => date('F', mktime(0, 0, 0, $request->month, 10)),
                    'year' => $request->year,
                    'total' => 0
                ];
                $collections = [];
            }

        } else {

            $collectionsQuery = Collection::select('collections.id', 'collections.number', 'collections.total_earnings', 'collections.created_at')
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('YEAR(collections.created_at)'), '=', date('Y'))
                ->where(\DB::raw('MONTH(collections.created_at)'), '=', date('m'))
                ->join('collect_orders', 'collections.id', '=', 'collect_orders.collection_id')
                ->join('orders', 'orders.id', '=', 'collect_orders.order_id')
                ->where(function ($query) {
                    $query->whereHas('orders', function ($query) {
                        $query->whereIn('collector_delivery_status', ['In Warehouse', 'Returned to Retailer'])
                        ->orWhereIn('return_delivery_status', ['In Warehouse', 'Returned to Retailer', 'Out for return delivery to retailer']);
                    });
                });

            $returnBlocksQuery = ReturnBlock::select('return_blocks.id', 'return_blocks.number', 'total_earnings', 'return_blocks.created_at')
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('YEAR(return_blocks.created_at)'), '=', date('Y'))
                ->where(\DB::raw('MONTH(return_blocks.created_at)'), '=', date('m'))
                ->join('return_block_orders', 'return_blocks.id', '=', 'return_block_orders.return_block_id')
                ->join('orders', 'orders.id', '=', 'return_block_orders.order_id')
                ->where(function ($query) {
                    $query->whereHas('orders', function ($query) {
                        $query->whereIn('return_delivery_status', ['In Warehouse', 'Returned to Retailer', 'Out for return delivery to retailer']);
                    });
                });

            $collectionsIDs = $collectionsQuery->distinct()->pluck('id')->toArray();
            $returnBlockIDs = $returnBlocksQuery->distinct()->pluck('id')->toArray();



            $results = $collectionsQuery->union($returnBlocksQuery)->get();

            if (count($results) > 0) {
                $array_data = $results->toArray();
                $unique_blocks = array_unique(array_column($array_data, 'number'), SORT_REGULAR);

                $total_earnings = 0;
                foreach ($array_data as $block) {
                    if (in_array($block['number'], $unique_blocks)) {
                        $total_earnings += $block['total_earnings'];
                        // Remove block from unique list to avoid duplicate summing
                        $key = array_search($block['number'], $unique_blocks);
                        unset($unique_blocks[$key]);
                    }
                }

                $monthly['total'] = $total_earnings;
                $monthly['month'] = date('F', mktime(0, 0, 0, date('m'), 10));
                $monthly['year'] = date('Y');

                $collections = $this->getCollectionsWithOrders(date('m'), date('Y'), $collectionsIDs);
                $blocks = $this->getReturnBlocksWithOrders(date('m'), date('Y'), $returnBlockIDs);
                $totalBlocks = collect($collections)->merge($blocks);

                $collections = $totalBlocks;

            } else {
                $monthly = [
                    'month' => date('F', mktime(0, 0, 0, date('m'), 10)),
                    'year' => date('Y'),
                    'total' => 0
                ];
                $collections = [];
            }

        }
        return response()->json(['data' => $monthly, 'blocks' => $collections, 'status' => 200], 200);
    }

    public function insertRetailer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', Rule::unique('users')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'mobile' => ['required', Rule::unique('users')],
            'password' => ['required'],
            'website' => ['required'],
            'licensefile' => ['mimes:pdf,docx', 'max:2048'],
            // 'business_type_id' => ['required'],
            // 'currency_id' => ['required'],
            // 'charges' => ['required']
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        if ($request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }

        $request['password'] = Hash::make($request->password);

        $user = User::create($request->toArray());
        $user->password()->create([
            'password' => $request->password
        ]);

        $business_type_id = '7';
        $currency_id = '1';
        $charges = '1';

        $role = Roles::where('name', 'Retailer')->first();
        $user->roles()->attach($role->id);

        $secret = new SecretKey('eu_sk_');
        $public = new SecretKey('eu_pk_');

        $licenseFile = null;

        if ($request->hasFile('licensefile')) {
            $fileName = time() . '_' . $request->licensefile->getClientOriginalName();
            $filePath = $request->file('licensefile')->storeAs('licenseFiles', $fileName, 'public');
            $name = time() . '_' . $request->licensefile->getClientOriginalName();
            $file_path = url('/storage/' . $filePath);
            $licenseFile = $file_path;
        }


        $user->retailer()->create([
            'business_type_id' => $business_type_id,
            'secret_key' => $secret->uuid,
            'public_key' => $public->uuid,
            'website' => $request->website,
            'currency_id' => $currency_id,
            'support_email' => $request->support_email,
            'support_mobile' => $request->support_mobile,
            'licensefile' => $licenseFile,
            'address' => $request->address,
        ]);
        $user->retailer->charges()->attach($charges);
        $response = [
            "secret_key" => $secret->uuid,
            "public_key" => $public->uuid,
        ];
        return response()->json(["data"=>$response , "success"=>200], 200);
    }

}
