<?php

namespace App\Http\Controllers;


use App\Classes\FilePaths;
use App\Helpers\OptimizeTrait;
use App\Mail\StatusUpdate;
use App\Models\Block;
use App\Models\BlockOrderDistance;
use App\Models\ItemLabel;
use App\Models\OrderDeliveryInformation;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class RiderController extends Controller
{
    use OptimizeTrait;

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);

        }

        $user = User::with(['rider', 'rider.workingdays', 'rider.zones'])->whereRelation('roles', 'name', '=', 'Rider')->where('username', $request->username)->first();
        if (!is_null($user)) {
            if (Hash::check($request->password, $user->password->password)) {
                $token = $user->createToken($user->username);
                unset($user->password);
                Auth::login($user);
                return response()->json(['user' => $user, 'token' => $token->plainTextToken, 'message' => 'Successfully login', 'status' => 201], 201);
            }
            return response()->json(['message' => 'Invalid Credentials', 'status' => 401], 401);
        }
        return response()->json(['message' => 'Rider not found', 'status' => 404], 404);
    }


    public function editRider(Request $request)
    {

        $user = User::with(['rider', 'rider.workingdays', 'rider.zones'])->find(Auth::user()->id);
        //        dd($user);
        if ($request->has('image') && !is_null($request->image)) {

            $validator = Validator::make($request->all(), [
                'image' => 'mimes:jpg,jpeg,png,gif,svg|max:10000',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
            }

            $filename = time() . '.webp';
            Image::make($request->file('image')->getRealPath())->encode('webp', 100)->save(public_path('/riderimages/' . $filename), 60, 'webp');

            $path = url('/riderimages/' . $filename);
            $user->profile_picture = $path;
            $user->save();

            return response()->json(['user' => $user, 'message' => 'Profile Picture Updated Successfully', 'status' => 200], 200);
        }

        if ($request->has('name') && !is_null($request->name)) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
            }
            $user->name = $request->name;
            $user->save();
            // $returnInfo = User::with('rider')->find($user->id);
            return response()->json(['user' => $user, 'message' => 'Name Updated Successfully', 'status' => 200], 200);
        }

        // if ($request->has('passport') && !is_null($request->passport)) {
        //     $validator = Validator::make($request->all(), [
        //         'passport' => 'required',
        //     ]);

        //     if ($validator->fails()) {
        //         return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        //     }

        //     $user->rider()->update([
        //         'passport' => $request->passport,
        //     ]);

        //     $returnInfo = User::with('rider')->find($user->id);
        //     return response()->json(['user' => $returnInfo, 'message' => 'Passport Updated Successfully', 'status' => 200], 200);
        // }
    }

    public function assignOrders(Request $request)
    {
        if ($request->filter == 'completed') {
            $user = User::with([
                'assignedBlocks' => function ($query) {
                    $query->whereHas('orders', function ($subQuery) {
                        $subQuery->where('delivery_status', 'Delivered');
                    });
                },
                'assignedBlocks.orders',
                'assignedBlocks.orders.items',
                'assignedBlocks.orders.itemsScanInfo',
                'assignedBlocks.orders.items.scan_info',
                'assignedBlocks.orders.delivery_information'
            ])->find(Auth::user()->id);
        } else {
            $user = User::with([
                'assignedBlocks' => function ($query) {
                    $query->whereHas('orders', function ($subQuery) {
                        $subQuery->where('delivery_status', '!=', 'Delivered');
                    });
                },
                'assignedBlocks.orders.items',
                'assignedBlocks.orders.itemsScanInfo',
                'assignedBlocks.orders.items.scan_info',
                'assignedBlocks.orders.delivery_information'
            ])->find(Auth::user()->id);

        }

        $warehouse_ids = [];
        if (count($user->assignedBlocks) > 0) {
            $assignedBlocksIds = $user->assignedBlocks->pluck('id');
            // dd($assigned);
            $assigned = Block::with('orders.itemsScanInfo')->whereIn('id',$assignedBlocksIds)->get();

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

            $warehouse = Warehouse::find(array_unique($warehouse_ids)[0]);
            foreach ($assigned->toArray() as $akey => $a) {
                $assigned[$akey]['warehouse'] = $warehouse->name;
                $assigned[$akey]['warehouse_contact'] = '090078601';
                $totalPackages[$akey] = [];
                $totalVerifiedPackages[$akey] = [];
                foreach ($assigned[$akey]->orders->toArray() as $okey => $o) {
                    $arrayScanInfo[$okey] = [];
                    $qrs[$okey] = array_values(array_unique(array_column($o['items_scan_info'], 'qr_code')));
                    foreach ($qrs[$okey] as $ikey => $qr) {

                        $scan[$ikey] = ItemLabel::where(['order_id' => $o['id'], 'qr_code' => $qr])->get();

                        $arrayScanInfo[$okey][$ikey] = $scan[$ikey]->first();
                        $arrayScanInfo[$okey][$ikey]['items'] = $scan[$ikey]->pluck('item');
                        unset($arrayScanInfo[$okey][$ikey]['item']);
                        $assigned[$akey]['orders'][$okey]['scan_info'] = $arrayScanInfo[$okey];
                        $assigned[$akey]['orders'][$okey]['total_packages'] = ItemLabel::where(['order_id' => $o['id']])->distinct()->count('qr_code');
                        $assigned[$akey]['orders'][$okey]['verified_packages'] = ItemLabel::where(['order_id' => $o['id'], 'verified_by_rider' => 1])->distinct()->count('qr_code');

                    }
                    array_push($totalPackages[$akey], $assigned[$akey]['orders'][$okey]['total_packages']);
                    array_push($totalVerifiedPackages[$akey], $assigned[$akey]['orders'][$okey]['verified_packages']);

                    $assigned[$akey]['total_packages'] = array_sum($totalPackages[$akey]);
                    $assigned[$akey]['verified_packages'] = array_sum($totalVerifiedPackages[$akey]);
                }
            }

            $response['blocks'] = $assigned->toArray();

            // dd($response['blocks']);
            // $arrayData = [];

            // foreach($response['blocks'] as $orkey => $block){
            //     foreach($block as $borkey => $order){
            //         if($request->filter != 'completed' && $order['delivery_status'] == 'Delivered'){
            //             ($response['blocks'][$orkey]['order_details'][$borkey]);
            //         }
            //     }
            // }


            foreach ($response['blocks'] as $skey => $a) {
                $warehouse = [];
                $location[$skey] = [];
                $drops[$skey] = [];

                foreach ($a['orders'] as $key => $or) {
                    $warehouse = Warehouse::find($or['warehouse_id']);
                    if (isset($warehouse->coordinates)) {
                        array_push($location[$skey], $warehouse->coordinates);
                    }

                }
                foreach ($a['orders'] as $key => $o) {
                    array_push($location[$skey], $o['dropoff_coordinates']);
                }

                $response['blocks'][$skey]['location'] = $location[$skey];

            }

            $blocksArray = $response['blocks'];
            usort($blocksArray, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            $response['blocks'] = OptimizeTrait::paginatedData($blocksArray, 6);
            $response['status'] = 200;

            return response()->json($response, 200);
        } else {
            return response()->json(['message' => 'No Blocks Found', 'status' => 404], 404);
        }
    }

    public function changeOnlineStatus(Request $request)
    {

        //        dd(Auth::user());
        $validator = Validator::make($request->all(), [
            'online' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);

        }
        $user = Auth::user();
        $rider = $user->rider;
        //        dd($rider);

        if ($request->online == 1) {
            $rider->is_online = 1;
            $rider->save();

            return response()->json(['rider' => $rider, 'message' => "You're online now, and visible to other users.", 'status' => 200], 200);
        } else {
            $rider->is_online = 0;
            $rider->save();

            return response()->json(['rider' => $rider, 'message' => "You're offline now, other users can't see your profile now.", 'status' => 200], 200);
        }

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
        $user = Auth::user();
        $order = Orders::with('items')->find($request->order_id);
        $order->delivery_status = $request->status;

        $order->save();
        // $order->enduser_email = 'hamzarazzaq96@gmail.com';
        $status = $request->status;
        $delivery_time = null;
        // $order->is_grocery = 0;
        $trackingurl = OptimizeTrait::gettrackingURL(Auth::user()->rider->id,$request->order_id);
        // dd($order);
        if ($order->is_grocery == '1') {

            if (strtolower($status) === "out for delivery") {
                $rider_status = "shipped";
                $message = "Dispatched";
                $order->deliverytime = date('H:i', strtotime('+15 minutes'));
                $order->save();
                $carbonTime = Carbon::parse($order->deliverytime);
                $delivery_time = $carbonTime->addMinutes(15);
                $apiEndpoint = config('app.grocery_url')."api/update_order_status?status=shipped&order_number=$order->order_number&tracking_url=$trackingurl";
                // $apiEndpoint = "https://ab20-2400-adc5-1e1-d400-c97e-b94e-c539-51f6.ngrok-free.app/api/update_order_status?status=shipped&order_number=$order->order_number&tracking_url=$trackingurl";
                $response = Http::get($apiEndpoint);
                $response_json = json_decode($response);
                // dd($response_json);
                $email = new StatusUpdate("Dispatched", $response_json->data);
                Mail::to($response_json->data->users[0]->email)->send($email);

            }
        } else {
            if (strtolower($status) === "out for delivery") {
                // dd($order);
                $order->deliverytime = date('H:i', strtotime('+15 minutes'));
                $order->save();
                $carbonTime = Carbon::parse($order->deliverytime);
                $delivery_time = $carbonTime->addMinutes(15);
                $data['trackingurl'] = $trackingurl;
                $data['order'] = $order;
                Mail::send('email.dispatched-logistics', $data, function ($message) use ($order) {
                    $message->to($order->enduser_email, $order->enduser_name)
                            ->subject('Endup: Your Order is on the way | '.$order->order_number);
                });
            }
        }
        $order->statuses()->create([
            'status' => $request->status,
        ]);

        return response()->json(['order' => $order, 'status' => 200], 200);


    }

    public function deliveryTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }
        $orderNumber = Orders::select('id', 'order_number')->where('order_number', $request->order_number)->first();

        if (isset($orderNumber->id)) {
            $order = OrderStatus::where('status', 'Out for delivery')->where('order_id', $orderNumber->id)->first();
        }
//        dd($order);
        $delivery_time = '';
        if (isset($order)) {

            $carbonTime = Carbon::parse($order->created_at)->addMinutes(15)->format('d-m-Y H:i A');
            $delivery_time = $carbonTime;

            return response()->json(['delivery_time' => $delivery_time, 'status' => 200], 200);
        }
        return response()->json(['message' => 'Order not found', 'status' => 404], 404);
    }

    public function updateDeliveryInformation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);

        }

        if ($request->status == 'Delivered') {
            $validator = Validator::make($request->all(), [
                // 'signature' => 'required',
                'pacakge_image' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
            }

            $order = OrderDeliveryInformation::with('order')->where('order_id', $request->order_id)->first();
            // dd($request->code);
            if ($order->order->is_premium == 1 && $order->order->premium_code != $request->code) {
                return response()->json(['status' => 400, 'message' => 'Invalid Code'], 400);
            }
            // dd($request->all());
            if ($request->has('signature')) {
                $signature_file = $order->order->order_number . '_' . time() . '_signature_file' . '.' . $request->signature->extension();

                $signature_path = OptimizeTrait::uploadFile(FilePaths::riderSignatureImage, $request->signature, 1, $signature_file);
            } else {
                $signature_path = null;
            }

            $filename = $order->order->order_number . '_' . time() . '_package_file.webp';

            $pacakge_image_path = OptimizeTrait::uploadFile(FilePaths::riderPackageImage, $request->pacakge_image, 1, $filename);


            $order->pacakge_image = $pacakge_image_path;
            $order->signature = $signature_path;
            $order->reason = $request->reason;
            $order->received_by = $request->received_by;
            if ($request->received_by != 'customer') {
                $order->name = $request->name;
                $order->address = $request->address;
            }
            $order->save();

            $order->order->update([
                'delivery_status' => 'Delivered'
            ]);
            $order->order->statuses()->create([
                'status' => 'Delivered',
            ]);

            $orders = Orders::with('delivery_information')->find($order->order->id);
            return response()->json(['orders' => $orders, 'status' => 200, 'message' => 'Order Delivered'], 200);
        } elseif ($request->status == 'Undelivered') {


            $validator = Validator::make($request->all(), [
                'comment' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
            }


            $order = Orders::with('delivery_information')->find($request->order_id);
            $order->update([
                'delivery_status' => 'Undelivered',
                'undelivered' => 1,
                'unddelivered_comments' => $request->comment,
                'assigned_to_rider' => 0,
            ]);

            return response()->json(['orders' => $order, 'status' => 200, 'message' => 'Order Status Updated'], 200);
        } else {

            $validator = Validator::make($request->all(), [
                'pacakge_image' => 'required',
                'reason' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);

            }

            $order = OrderDeliveryInformation::with('order')->where('order_qr_code', $request->qr)->first();
            // dd($request->all());
            $filename = $order->order->order_number . '_' . time() . '_package_file.webp';
            $pacakge_image_path = OptimizeTrait::uploadFile(FilePaths::riderPackageImage, $request->pacakge_image, 1, $filename);
            // Image::make($request->file('pacakge_image'))->encode('webp', 100)->save(public_path('/pacakge_images/' . $filename), 60, 'webp');

            $path = $pacakge_image_path;
            $order->pacakge_image = $path;
            $order->reason = $request->reason;
            $order->save();

            $order->order->update([
                'delivery_status' => 'Cancelled'
            ]);
            $order->order->statuses()->create([
                'status' => 'Cancelled',
            ]);
            $orders = Orders::with('delivery_information')->find($order->order->id);
            return response()->json(['orders' => $orders, 'status' => 200, 'message' => 'Order cancelled'], 200);

        }

    }

    public function verifyBlock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block' => 'required',
            'qr' => 'required'
            // 'order_id' => 'required',
            // 'order_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $check = Orders::whereRelation('block', 'blocks.id', '=', $request->block)
            ->whereHas('delivery_information', function ($q) use ($request) {
                $q->where('order_qr_code', $request->qr);
            })->first();
        // dd($check);
        if (!is_null($check)) {
            // $order = Orders::find($request->order_id);
            $check->verified_by_rider = 1;
            $check->save();

            $block = Block::with([
                'orders' => function ($q) {
                    $q->orderBy('distance');
                },
                'orders.items',
                'orders.delivery_information'
            ])->find($request->block);


            // 'assignedBlocks.orders' => function ($q) {
            //     $q->orderBy('distance');
            // },
            // 'assignedBlocks.orders.items',
            // 'assignedBlocks.orders.delivery_information'
            return response()->json(['block' => $block, 'message' => true, 'status' => 200], 200);
        }
        return response()->json(['message' => false, 'status' => 404], 404);
    }

    public function calculateEarnings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        // $collection = Block::with('orders')->find($request->block_id);
        $collection = Block::with(
            'orders.orderDistance',
            'orders',
            'orders.items',
            'orders.itemsScanInfo',
            'orders.items.scan_info',
            'orders.delivery_information'
        )->find($request->block_id);
        // dd($request->collection_id);
        if (!is_null($collection)) {
            $pickup = null;
            if (count(array_unique($collection->orders->pluck('pickup_coordinates')->toArray())) < 2) {
                $pickup = array_unique($collection->orders->pluck('pickup_coordinates')->toArray())[0];
            }

            $locations = [];
            $lastDrop = '';
            $getDistance = [];

            $time_per_order = [];
            if (is_null($collection->total_earnings) || is_null($collection->total_time)) {
                foreach ($collection->orders as $key => $o) {
                    $lastDrop = $o['dropoff_coordinates'];
                    $dropoff[$key] = (isset($collection->orders[$key + 1])) ? $collection->orders[$key + 1]['dropoff_coordinates'] : $pickup;
                    $getDistance[$key] = 'https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&units=imperial&origins=' . $lastDrop . '&destinations=' . $dropoff[$key] . '&key=' . env('GOOGLE_MAP_KEY');

                    $responsedis[$key] = Http::get($getDistance[$key])->json();
                    if (isset($responsedis[$key]['rows'][0]['elements'][0]['distance']['text'])) {
                        $distanceAdd[$key] = new BlockOrderDistance();
                        $distanceAdd[$key]->block_id = $collection->id;
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

                $time_per_order_total = array_sum($time_per_order);
                $time_per_order_total += 900;
                $hours = floor($time_per_order_total / 3600);
                $minutes = floor(($time_per_order_total / 60) % 60);
                // $seconds = array_sum($time_per_order_total) % 60;
                $time = sprintf('%02d:%02d', $hours, $minutes);

                $totalTime = $time;

                // $totalTime = gmdate("H:i", array_sum($time_per_order));
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
                // dd($warehouse);
                $drops = [];
                array_push($location, $warehouse->coordinates);

                foreach ($collection->orders as $key => $o) {
                    array_push($location, $o['dropoff_coordinates']);
                }

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

                    $collection['orders'][$okey]['total_packages'] = ItemLabel::where(['order_id' => $o['id']])->count();
                    $collection['orders'][$okey]['verified_packages'] = ItemLabel::where(['order_id' => $o['id'], 'verified_by_rider' => 1])->count();

                    $collectorOrderDistance[$ikey] = BlockOrderDistance::where('block_id', $collection->id)->where('order_id', $o['id'])->first();
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

    public function earnings()
    {

        // dd();
        $response = [];
        // $deliveries = Auth::user()->assignedBlocks;
        // $deliveries = Block::where('user_id', Auth::user()->id)
        //     ->whereHas('orders', function ($query) {
        //         $query->where('delivery_status', 'Delivered');
        //     })
        //     // ->doesntHave('orders', 'and', function ($query) {
        //     //     $query->where('delivery_status', '!=', 'Delivered');
        //     // })
        //     ->get();

        $deliveries = Block::select('*')
            ->where('user_id', Auth::user()->id)
            ->join('block_orders', 'blocks.id', '=', 'block_orders.block_id')
            ->join('orders', 'orders.id', '=', 'block_orders.order_id')
            ->where(function ($query) {
                $query->whereDoesntHave('orders', function ($query) {
                    $query->where('delivery_status', '<>', 'Delivered');
                });
            })->get();

        if (isset($deliveries)) {

            $array_data = $deliveries->toArray();
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
            // $response['total_earnings'] = array_sum(array_values(array_unique(array_column($array_data, 'total_earnings'))));
            $response['total_deliveries'] = count(array_values(array_unique(array_column($array_data, 'number'))));
        } else {
            $response['total_earnings'] = 0;
            $response['total_deliveries'] = 0;
        }
        return response()->json(['data' => $response, 'status' => 200], 200);


    }

    public function qrScan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block' => 'required',
            // 'order_id' => 'required',
            'qr_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        if ($request->delivery == 1) {
            $collection = Block::
            with(
                'orders.itemsScanInfo',
            )
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

                })->where('id', $request->block)->first();
        } else {


            $collection = Block::
            with(
                'orders.itemsScanInfo',
            )
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

                })->where('id', $request->block)->first();
        }
        // dd($collection);
        if (is_null($collection)) {
            return response()->json(['message' => false, 'status' => 400], 400);
        }


        $labels = ItemLabel::with('item')->where('qr_code', $request->qr_code)->get();
        if (count($labels) > 0) {
            if ($request->has('delivery') && $request->delivery == 1) {
                foreach ($labels as $l) {
                    $l->verified_on_delivery = 1;
                    $l->save();
                }
            } else {

                foreach ($labels as $l) {
                    $l->verified_by_rider = 1;
                    $l->save();
                }
            }
        }

        if ($request->delivery == 1) {
            $order = Orders::with('itemsScanInfo.item')->find($request->order_id);

            $response = [];


            $qrs = ItemLabel::where('order_id', $request->order_id)->get()->pluck('qr_code')->toArray();

            foreach (array_values(array_unique($qrs)) as $key => $QR) {

                $scan[$key] = ItemLabel::where(['qr_code' => $QR])->get();
                $response[$key] = $scan[$key]->first()->toArray();
                $response[$key]['items'] = $scan[$key]->pluck('item');

            }

            return response()->json(['data' => $this->super_unique($response, 'qr_code'), 'message' => true, 'status' => 200], 200);
        }
        $block = Block::with(
            'orders.itemsScanInfo',
            'orders.items',
            'orders.items.scan_info',
            'orders.delivery_information'
        )->where('id', $request->block)->first();
        $warehouse_ids = [];


        foreach ($block->orders->pluck('warehouse_id') as $warehouse_id) {
            array_push($warehouse_ids, $warehouse_id);
            // }
        }

        $warehouse = Warehouse::find(array_unique($warehouse_ids)[0]);

        $block['warehouse'] = $warehouse->name;
        $block['warehouse_contact'] = '090078601';
        $totalPackages = [];
        $totalVerifiedPackages = [];
        foreach ($block->orders->toArray() as $okey => $o) {
            // dd([$okey]);
            // $assigned[$akey]['orders'][$okey]['scanInfo'] = [];
            $arrayScanInfo[$okey] = [];
            $qrs[$okey] = array_values(array_unique(array_column($o['items_scan_info'], 'qr_code')));
            foreach ($qrs[$okey] as $ikey => $qr) {

                $scan[$ikey] = ItemLabel::where(['order_id' => $o['id'], 'qr_code' => $qr])->get();

                $arrayScanInfo[$okey][$ikey] = $scan[$ikey]->first();
                $arrayScanInfo[$okey][$ikey]['items'] = $scan[$ikey]->pluck('item');
                unset($arrayScanInfo[$okey][$ikey]['item']);

                $block['orders'][$okey]['scan_info'] = $arrayScanInfo[$okey];
                $block['orders'][$okey]['total_packages'] = ItemLabel::where(['order_id' => $o['id']])->count();
                $block['orders'][$okey]['verified_packages'] = ItemLabel::where(['order_id' => $o['id'], 'verified_by_rider' => 1])->count();

            }
            array_push($totalPackages, $block['orders'][$okey]['total_packages']);
            array_push($totalVerifiedPackages, $block['orders'][$okey]['verified_packages']);
        }

        $block['total_packages'] = (is_countable($totalPackages) && count($totalPackages) > 0) ? array_sum($totalPackages) : 0;
        $block['verified_packages'] = (is_countable($totalVerifiedPackages) && count($totalVerifiedPackages) > 0) ? array_sum($totalVerifiedPackages) : 0;
        return response()->json(['block' => $block, 'message' => true, 'status' => 200], 200);
        // return response()->json(['data' => $this->super_unique($response, 'qr_code'), 'message' => true, 'status' => 200], 200);
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

    public function changeOrderVerificationStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $order = Orders::find($request->order_id);
        $order->verified_by_rider = $request->status;
        $order->save();
        return response()->json(['status' => 200], 200);
    }

    public function getAllEarnings(Request $request)
    {
        $userId = Auth::user()->id;
        // dd($userId);
        if ($request->has('month') && !empty($request->month) && $request->has('year') && !empty($request->year)) {

            $blocksQuery = Block::select('*')
                ->where('user_id', Auth::user()->id)
                ->where(DB::raw('YEAR(blocks.created_at)'), '=', $request->year)
                ->where(DB::raw('MONTH(blocks.created_at)'), '=', $request->month)
                ->join('block_orders', 'blocks.id', '=', 'block_orders.block_id')
                ->join('orders', 'orders.id', '=', 'block_orders.order_id')
                ->where(function ($query) {
                    $query->whereDoesntHave('orders', function ($query) {
                        $query->where('delivery_status', '<>', 'Delivered');
                    });
                })->get();

            if (count($blocksQuery) > 0) {
                $array_data = $blocksQuery->toArray();
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
                $monthly['year'] = date('Y');
                $collections = $this->getBlocksWithOrders($request->month, $request->year);
            } else {
                $monthly = [
                    'month' => date('F', mktime(0, 0, 0, $request->month, 10)),
                    'year' => $request->year,
                    'total' => 0
                ];
                $collections = [];
            }

        } else {


            $blocksQuery = Block::select('*')
                ->where('user_id', Auth::user()->id)
                ->where(DB::raw('YEAR(blocks.created_at)'), '=', date('Y'))
                ->where(DB::raw('MONTH(blocks.created_at)'), '=', date('m'))
                ->join('block_orders', 'blocks.id', '=', 'block_orders.block_id')
                ->join('orders', 'orders.id', '=', 'block_orders.order_id')
                ->where(function ($query) {
                    $query->whereDoesntHave('orders', function ($query) {
                        $query->where('delivery_status', '<>', 'Delivered');
                    });
                })->get();


            // return $monthly;
            if (count($blocksQuery) > 0) {

                $array_data = $blocksQuery->toArray();
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
                $collections = $this->getBlocksWithOrders();
            } else {
                $monthly = [
                    'month' => date('F', mktime(0, 0, 0, date('m'), 10)),
                    'year' => date('Y'),
                    'total' => 0
                ];
                $collections = [];
            }


        }
        // $weekly = \DB::select('select date(created_at) as date, sum(total_earnings) as total from blocks where created_at > (now() - INTERVAL 7 day) and user_id = ' . Auth::user()->id . ' group by date(created_at)');
        // dd(count($monthly));
        return response()->json(['data' => $monthly, 'blocks' => $collections, 'status' => 200], 200);
    }


}
