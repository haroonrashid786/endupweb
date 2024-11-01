<?php

namespace App\Http\Controllers;

use App\Helpers\OptimizeTrait;
use App\Models\Collection;
use App\Models\ItemLabel;
use App\Models\ReturnBlock;
use App\Models\ReturnBlockOrderDistance;
use App\Models\User;
use App\Models\Warehouse;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class ReturnController extends Controller
{
    public function assignOrders(Request $request)
    {
        if ($request->filter == 'completed') {
            $user = User::with([
                'assignedReturns',
                'assignedReturns.orders' => function ($q) {
                    $q->whereIn('collector_delivery_status', ['In Warehouse', 'In Wirehouse']);
                },
                'assignedReturns.orders.itemsScanInfo',
                'assignedReturns.orders.items.scan_info',
                'assignedReturns.orders.collector_delivery_information'
            ])->find(Auth::user()->id);
        } else {
            $user = User::with([
                'assignedReturns' => function ($query) {
                    $query->whereHas('orders', function ($subQuery) {
                        $subQuery->where('collector_delivery_status', '!=', 'In Warehouse')->orWhere('collector_delivery_status', '!=', 'in warehouse');
                    });
                },
                'assignedReturns.orders' ,
                'assignedReturns.orders.itemsScanInfo',
                'assignedReturns.orders.items.scan_info',
                'assignedReturns.orders.collector_delivery_information'
            ])->find(Auth::user()->id);
        }

        $getReturns = $user->assignedReturns;

        $warehouse_ids = [];

        $assignedBlocksIds = $user->assignedReturns->pluck('id');

        // $assigned = ReturnBlock::
        $assigned = ReturnBlock::with('orders.itemsScanInfo')->whereIn('id',$assignedBlocksIds)->get();


        if (count($assigned) < 1) {
            return response()->json(['message' => 'No Collections Found', 'status' => 404], 404);
        }
        $warehouse_ids = [];

        foreach ($assigned as $key => $assign) {
            foreach ($assign->orders->pluck('warehouse_id') as $warehouse_id) {
                array_push($warehouse_ids, $warehouse_id);
            }
        }
        $warehouse = Warehouse::find(array_unique($warehouse_ids)[0]);

        $assigned->mapWithKeys(function ($order) {
            $order['order_details'] = $order->order;
            return $order;
        });

        $assigned->mapWithKeys(function ($order) {
            unset($order['order']);
            return $order;
        });

        foreach ($assigned->toArray() as $akey => $a) {
            $assigned[$akey]['warehouse'] = $warehouse->name;
            $assigned[$akey]['warehouse_contact'] = '090078601';
            foreach ($assigned[$akey]->orders->toArray() as $okey => $o) {
                // dd($o['items_scan_info']);
                $arrayScanInfo[$okey] = [];
                $qrs[$okey] = array_values(array_unique(array_column($o['items_scan_info'], 'qr_code')));
                foreach ($qrs[$okey] as $ikey => $qr) {

                    $scan[$ikey] = ItemLabel::where(['order_id' => $o['id'], 'qr_code' => $qr])->get();

                    $arrayScanInfo[$okey][$ikey] = $scan[$ikey]->first();
                    $arrayScanInfo[$okey][$ikey]['items'] = $scan[$ikey]->pluck('item');
                    unset($arrayScanInfo[$okey][$ikey]['item']);
                    $assigned[$akey]['orders'][$okey]['scan_info'] = $arrayScanInfo[$okey];
                }

            }
        }

        $response['blocks'] = $assigned;

        foreach ($response['blocks'] as $skey => $a) {
            $warehouse = [];
            $location[$skey] = [];
            foreach ($a['orders'] as $key => $o) {
                $warehouse = Warehouse::find($o->warehouse_id);
                $location[$skey][$key] = $o['return_pickup_coordinates'];
            }
            // array_push($location[$skey], $warehouse->coordinates);
            if (isset($warehouse->coordinates)) {
                array_push($location[$skey], $warehouse->coordinates);
            }
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

    public function caculateEarnings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $collection = ReturnBlock::with(
            'orders.orderDistance',
            'orders',
            'orders.items',
            'orders.itemsScanInfo',
            'orders.items.scan_info',
            'orders.return_delivery_information'
        )->find($request->collection_id);
        // dd($request->collection_id);
        if (!is_null($collection)) {
            $drop = null;
            if (count(array_unique($collection->orders->pluck('collector_dropoff_coordinates')->toArray())) < 2) {
                $drop = array_unique($collection->orders->pluck('collector_dropoff_coordinates')->toArray())[0];
            }

            $locations = [];
            $last_pickup = '';
            $getDistance = [];
            $distances = [];
            $time_per_order = [];
            if (is_null($collection->total_earnings) || is_null($collection->total_time)) {
                foreach ($collection->orders as $key => $o) {
                    $last_pickup = $o['return_pickup_coordinates'];
                    $dropoff[$key] = (isset($collection->orders[$key + 1])) ? $collection->orders[$key + 1]['return_pickup_coordinates'] : $drop;
                    $getDistance[$key] = 'https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&units=imperial&origins=' . $last_pickup . '&destinations=' . $dropoff[$key] . '&key=' . env('GOOGLE_MAP_KEY');

                    $responsedis[$key] = Http::get($getDistance[$key])->json();

                    if (isset($responsedis[$key]['rows'][0]['elements'][0]['distance']['text'])) {
                        $distanceAdd[$key] = new ReturnBlockOrderDistance();
                        $distanceAdd[$key]->return_block_id = $collection->id;
                        $distanceAdd[$key]->order_id = $o['id'];
                        // $distanceAdd[$key]->distance = $responsedis[$key]['rows'][0]['elements'][0]['distance']['text'];
                        if (strpos($responsedis[$key]['rows'][0]['elements'][0]['distance']['text'], 'ft') !== false) {
                            $distanceAdd[$key]->distance = 0;
                        } else {
                            $distanceAdd[$key]->distance = $responsedis[$key]['rows'][0]['elements'][0]['distance']['text'];
                        }
                        $distanceAdd[$key]->save();
                    }

                    $time_per_order[$key] = isset($responsedis[$key]['rows'][0]['elements'][0]['duration_in_traffic']['value']) ? $responsedis[$key]['rows'][0]['elements'][0]['duration_in_traffic']['value'] : 0;
                }

                // $totalTime = gmdate("H:i", array_sum($time_per_order));
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
                // dd($warehouse);
                $drops = [];
                foreach ($collection->orders as $key => $o) {
                    array_push($location, $o['return_pickup_coordinates']);
                }
                array_push($location, $warehouse->coordinates);
            }

            $collection['location'] = $location;
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


                    $collection['orders'][$okey]['scan_info'] = $arrayScanInfo[$okey];

                    $collection['orders'][$okey]['total_packages'] = 0;
                    $collection['orders'][$okey]['verified_packages'] = 0;

                    $collectorOrderDistance[$ikey] = ReturnBlockOrderDistance::where('return_block_id', $collection->id)->where('order_id', $o['id'])->first();
                    if (!is_null($collectorOrderDistance[$ikey])) {
                        $collection['orders'][$okey]['extra_distance'] = $collectorOrderDistance[$ikey]->distance;
                    }

                }

                array_push($totalPackages, $collection['orders'][$okey]['total_packages']);
                array_push($totalVerifiedPackages, $collection['orders'][$okey]['verified_packages']);

                $collection['total_packages'] = array_sum($totalPackages);
                $collection['verified_packages'] = array_sum($totalVerifiedPackages);

            }
            return response()->json(['total_earnings' => round($pay), 'total_time' => $totalTime, 'distances' => $distances, 'block' => $collection, 'status' => 200], 200);
        }
    }


    public function returnToRetailerCollections(Request $request)
    {
        if ($request->filter == 'completed') {
            $user = User::with([
                'assignedCollectionsReturn' => function ($q) {
                    $q->whereIn('return_delivery_status', ['Returned to Retailer']);
                },
                'assignedCollectionsReturn.orders',
                'assignedCollectionsReturn.orders.itemsScanInfo',
                'assignedCollectionsReturn.orders.items.scan_info',
                'assignedCollectionsReturn.orders.collector_delivery_information'
            ])
                ->whereHas('assignedCollectionsReturn.orders', function ($q) {
                    $q->whereIn('return_delivery_status', ['Returned to Retailer']);
                })
                ->find(Auth::user()->id);

        } else {
            $user = User::with([
                'assignedCollectionsReturn' => function ($query) {
                    $query->whereHas('orders', function ($subQuery) {
                        $subQuery->where('return_delivery_status', '!=', 'Returned to Retailer');
                    });
                },
                'assignedCollectionsReturn.orders',
                'assignedCollectionsReturn.orders.itemsScanInfo',
                'assignedCollectionsReturn.orders.items.scan_info',
                'assignedCollectionsReturn.orders.collector_delivery_information'
            ])->find(Auth::user()->id);
        }

        if (count($user->assignedCollectionsReturn) < 1) {
            return response()->json(['message' => 'No Collections Found', 'status' => 404], 404);
        }

        $getReturns = $user->assignedCollectionsReturn;

        $warehouse_ids = [];

        $assignedBlocksIds = $user->assignedCollectionsReturn->pluck('id');

        $assigned = Collection::with('orders.itemsScanInfo')->whereIn('id',$assignedBlocksIds)->where('return', 1)->get();

        foreach ($assigned as $key => $assign) {
            foreach ($assign->orders->pluck('warehouse_id') as $warehouse_id) {
                array_push($warehouse_ids, $warehouse_id);
            }
        }



        $assigned->mapWithKeys(function ($order) {
            $order['order_details'] = $order->order;
            return $order;
        });

        $assigned->mapWithKeys(function ($order) {
            unset($order['order']);
            return $order;
        });

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
}
