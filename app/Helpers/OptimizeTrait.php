<?php

namespace App\Helpers;

use App\Models\ItemLabel;
use App\Models\Orders;
use App\Models\Price;
use App\Models\Retailer;
use App\Models\RetailerChargesList;
use App\Models\RetailerChargesListItem;
use App\Models\User;
use App\Models\UserSession;
use App\Models\Warehouse;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

trait OptimizeTrait
{

    function generateCode($len, $prefix = null)
    {
        $randomCode = $prefix . bin2hex(openssl_random_pseudo_bytes($len));
        return $randomCode;
    }

    function getShippingRate($arr, $order, $retailer)
    {
        foreach ($arr as $key => $i) {
            $arr[$key]['volumetric_weight'] = $i['length'] * $i['width'] * $i['height'] / 5000;
            $arr[$key]['order_id'] = $order->id;
            $arr[$key]['dimension'] = $i['width'] . " x " . $i['height'];
            $arr[$key]['height'] = $i['height'];
            $arr[$key]['width'] = $i['width'];
            $arr[$key]['length'] = $i['length'];
            $arr[$key]['created_at'] = now();
            $arr[$key]['updated_at'] = now();
        }
        //        dd($items);
        $order->items()->insert($arr);


        $checkRate = array(
            'height' => array_sum(array_column($arr, 'height')),
            'length' => array_sum(array_column($arr, 'length')),
            'width' => array_sum(array_column($arr, 'width')),
            'volumetric_weight' => number_format(array_sum(array_column($arr, 'length')) * array_sum(array_column($arr, 'width')) * array_sum(array_column($arr, 'height')) / 5000, 2),
            'city_from' => 'Lahore',
            'city_to' => $order->drop_info['city'],
            'country_from' => 'Pakistan',
            'country_to' => $order->drop_info['country'],
            'postal_code_to' => $order->drop_info['postal'],
            'postal_code_from' => '54000',
        );
        //        dd($checkRate);
        $rates = $this->calculateShipping($checkRate, $retailer);
        //        dd($rates);
        return $rates;
    }

    function calculateShipping($arr, $retailer)
    {
        DB::enableQueryLog();
        //         $getPrice = Price::where('height', '<=', $arr['height'])
//             ->where('length', '<=', $arr['length'])
//             ->where('width', '<=', $arr['width'])
//             ->where('volumetric_weight', '<=', $arr['volumetric_weight'])
//             ->where('city_from', $arr['city_from'])
//             ->where('city_to', $arr['city_to'])
//             ->where('country_from', $arr['country_from'])
//             ->where('country_to', $arr['country_to'])
// //            ->where('postal_code_to', $arr['postal_code_to'])
// //            ->where('postal_code_from', $arr['postal_code_from'])
//             ->get();

        // dd($arr['volumetric_weight']);

        // dd((float)$arr['volumetric_weight']);
        $retailerChargesid = $retailer->charges[0]->id;
        // dd($retailerChargesid);

        // dd((float)$arr['volumetric_weight']);
        $getPrice = RetailerChargesListItem::where('retailer_charges_list_id', $retailerChargesid)
            ->where('max_volumetric_weight', '>=', (float) $arr['volumetric_weight'])
            ->where('min_volumetric_weight', '<=', (float) $arr['volumetric_weight'])
            ->first();

        // dd($getPrice);
        if (is_null($getPrice)) {
            // dd((float)$arr['volumetric_weight']);
            $getPrice = RetailerChargesListItem::where('retailer_charges_list_id', $retailerChargesid)
                ->where('min_volumetric_weight', '>', (float) $arr['volumetric_weight'])->orWhere('min_volumetric_weight', '<', (float) $arr['volumetric_weight'])
                ->first();
            // dd($getPrice);
        }
        // dd($getPrice);
        // $getPrice = Price::where('volumetric_weight', '<=', (float)$arr['volumetric_weight'])->first();

        //    dd($arr, DB::getQueryLog(),$getPrice);

        return $getPrice;
    }

    public function pricesResource($prices)
    {

        $returnPrices = [];

        // foreach ($prices as $key=>$p){
        //     $returnPrices[$key]['cities'] = $p->city_from.' to '.$p->city_to;
        //     $returnPrices[$key]['countries'] = $p->country_from.' to '.$p->country_to;
        //     $returnPrices[$key]['postals'] = $p->postal_code_to.' to '.$p->postal_code_from;
        //     $returnPrices[$key]['volumetric_weight'] = $p->volumetric_weight;
        //     $returnPrices[$key]['length'] = $p->length;
        //     $returnPrices[$key]['height'] = $p->height;
        //     $returnPrices[$key]['width'] = $p->width;
        //     $returnPrices[$key]['quantity_box'] = $p->quantity_box;
        //     $returnPrices[$key]['price'] = $p->price;
        //     $returnPrices[$key]['currency'] = $p->currency->code;
        //     $returnPrices[$key]['id'] = $p->id;

        // }

        // $returnPrices['cities'] = $prices->city_from.' to '.$prices->city_to;
        //     $returnPrices['countries'] = $prices->country_from.' to '.$prices->country_to;
        //     $returnPrices['postals'] = $prices->postal_code_to.' to '.$prices->postal_code_from;
        //     $returnPrices['volumetric_weight'] = $prices->volumetric_weight;
        //     $returnPrices['length'] = $prices->length;
        //     $returnPrices['height'] = $prices->height;
        //     $returnPrices['width'] = $prices->width;
        //     $returnPrices['quantity_box'] = $prices->quantity_box;
        // $returnPrices['currency'] = $prices->currency->code;
        $returnPrices['price'] = $prices->price;

        $returnPrices['id'] = $prices->id;
        return $returnPrices;
    }


    function getShippingRateUpdate($arr, $order, $retailer)
    {
        foreach ($arr as $key => $i) {
            $arr[$key]['volumetric_weight'] = $i['length'] * $i['width'] * $i['height'] / 5000;
            $arr[$key]['order_id'] = $order->id;
            $arr[$key]['dimension'] = $i['width'] . " x " . $i['height'];
            $arr[$key]['height'] = $i['height'];
            $arr[$key]['width'] = $i['width'];
            $arr[$key]['length'] = $i['length'];
            $arr[$key]['created_at'] = now();
            $arr[$key]['updated_at'] = now();
        }
        //        dd($items);
        $order->items()->delete();
        $order->items()->insert($arr);


        $checkRate = array(
            'height' => array_sum(array_column($arr, 'height')),
            'length' => array_sum(array_column($arr, 'length')),
            'width' => array_sum(array_column($arr, 'width')),
            'volumetric_weight' => number_format(array_sum(array_column($arr, 'length')) * array_sum(array_column($arr, 'width')) * array_sum(array_column($arr, 'height')) / 5000, 2),
            'city_from' => 'Lahore',
            'city_to' => $order->drop_info['city'],
            'country_from' => 'Pakistan',
            'country_to' => $order->drop_info['country'],
            'postal_code_to' => $order->drop_info['postal'],
            'postal_code_from' => '54000',
        );
        //        dd($checkRate);
        $rates = $this->calculateShipping($checkRate, $retailer);
        //        dd($rates);
        return $rates;
    }

    function get7DaysDates($days, $format = 'd/m')
    {
        $m = date("m");
        $de = date("d");
        $y = date("Y");
        $dateArray = array();
        for ($i = 0; $i <= $days - 1; $i++) {
            $dateArray[] = '"' . date($format, mktime(0, 0, 0, $m, ($de - $i), $y)) . '"';
        }
        return array_reverse($dateArray);
    }

    function getCollectionsWithOrders($month = null, $year = null, $collectionsIDs = [])
    {

        $user = User::with([
            'assignedCollections' => function ($q) use ($month, $year, $collectionsIDs) {
                if (!is_null($month) && !is_null($year)) {
                    $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
                } else {
                    $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
                }
                if (count($collectionsIDs) > 0) {
                    $q->whereIn('id', $collectionsIDs);
                }
            },
            'assignedCollections.orders',
            'assignedCollections.orders.itemsScanInfo',
            'assignedCollections.orders.items.scan_info',
            'assignedCollections.orders.collector_delivery_information'
        ])->find(\Auth::user()->id);
        $assigned = $user->assignedCollections;
        if (count($assigned) > 0) {
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
            // $WAREHOUSE = Warehouse::first();
            foreach ($assigned->toArray() as $akey => $a) {
                $assigned[$akey]['warehouse'] = $warehouse->name;
                $assigned[$akey]['warehouse_contact'] = '090078601';
                $totalPackages[$akey] = [];
                $totalVerifiedPackages[$akey] = [];
                foreach ($assigned[$akey]->orders->toArray() as $okey => $o) {
                    // dd([$okey]);
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
                        // if ($request->has('return') && $request->return == 1) {
                        //     $assigned[$akey]['orders'][$okey]['total_packages'] = ItemLabel::where(['order_id' => $o['id']])->count();
                        //     $assigned[$akey]['orders'][$okey]['verified_packages'] = ItemLabel::where(['order_id' => $o['id'], 'verified_by_return_rider_warehouse' => 1])->count();
                        // }
                    }
                    // if ($request->has('return') && $request->return == 1) {
                    //     array_push($totalPackages[$akey], $assigned[$akey]['orders'][$okey]['total_packages']);
                    //     array_push($totalVerifiedPackages[$akey], $assigned[$akey]['orders'][$okey]['verified_packages']);
                    // }
                    // dd($assigned[$akey]['orders'][$okey]);
                    //  = ;/
                    // dd();
                    //  array_push($assigned[$akey]->orders, $arrayScanInfo[$okey]);
                    // $this->super_unique($assigned[$akey]['orders'][$okey]['scanInfo'], 'qr_code');
                    // dd($o)
                    // if ($request->has('return') && $request->return == 1) {
                    //     $assigned[$akey]['total_packages'] = array_sum($totalPackages[$akey]);
                    //     $assigned[$akey]['verified_packages'] = array_sum($totalVerifiedPackages[$akey]);
                    // }
                }
            }

            $response['blocks'] = $assigned;

            foreach ($response['blocks'] as $skey => $a) {

                $location[$skey] = [];
                foreach ($a['orders'] as $key => $o) {

                    $location[$skey][$key] = $o['collector_pickup_coordinates'];
                }
                array_push($location[$skey], $warehouse->coordinates);
                $response['blocks'][$skey]['location'] = $location[$skey];

            }
            return $response['blocks'];
        }
        return [];
    }


    function getReturnBlocksWithOrders($month = null, $year = null, $returnBlockIDs = [])
    {
        // dd($returnBlockIDs);
        $user = User::with([
            'assignedReturns' => function ($q) use ($month, $year, $returnBlockIDs) {
                // if (!is_null($month) && !is_null($year)) {
                //     $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
                // } else {
                //     $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
                // }
                if (count($returnBlockIDs) > 0) {
                    $q->whereIn('id', $returnBlockIDs);
                }
            },
            'assignedReturns.orders',
            'assignedReturns.orders.itemsScanInfo',
            'assignedReturns.orders.items.scan_info',
            'assignedReturns.orders.collector_delivery_information'
        ])->find(\Auth::user()->id);
        $assigned = $user->assignedReturns;
        if (count($assigned) > 0) {
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
            // $WAREHOUSE = Warehouse;::first();
            // return $assigned;
            foreach ($assigned->toArray() as $akey => $a) {

                $assigned[$akey]['warehouse'] = $warehouse->name;
                $assigned[$akey]['warehouse_contact'] = '090078601';
                $totalPackages[$akey] = [];
                $totalVerifiedPackages[$akey] = [];
                foreach ($assigned[$akey]->orders->toArray() as $okey => $o) {
                    // dd([$okey]);
                    // $assigned[$akey]['orders'][$okey]['scanInfo'] = [];
                    $arrayScanInfo[$okey] = [];
                    if (isset($o['items_scan_info'])) {
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
                            // if ($request->has('return') && $request->return == 1) {
                            //     $assigned[$akey]['orders'][$okey]['total_packages'] = ItemLabel::where(['order_id' => $o['id']])->count();
                            //     $assigned[$akey]['orders'][$okey]['verified_packages'] = ItemLabel::where(['order_id' => $o['id'], 'verified_by_return_rider_warehouse' => 1])->count();
                            // }
                        }
                    } else {
                        $assigned[$akey]['orders'][$okey]['scan_info'] = [];
                    }
                    // if ($request->has('return') && $request->return == 1) {
                    //     array_push($totalPackages[$akey], $assigned[$akey]['orders'][$okey]['total_packages']);
                    //     array_push($totalVerifiedPackages[$akey], $assigned[$akey]['orders'][$okey]['verified_packages']);
                    // }
                    // dd($assigned[$akey]['orders'][$okey]);
                    //  = ;/
                    // dd();
                    //  array_push($assigned[$akey]->orders, $arrayScanInfo[$okey]);
                    // $this->super_unique($assigned[$akey]['orders'][$okey]['scanInfo'], 'qr_code');
                    // dd($o)
                    // if ($request->has('return') && $request->return == 1) {
                    //     $assigned[$akey]['total_packages'] = array_sum($totalPackages[$akey]);
                    //     $assigned[$akey]['verified_packages'] = array_sum($totalVerifiedPackages[$akey]);
                    // }
                }
            }

            $response['blocks'] = $assigned;

            foreach ($response['blocks'] as $skey => $a) {

                $location[$skey] = [];
                foreach ($a['orders'] as $key => $o) {

                    $location[$skey][$key] = $o['return_pickup_coordinates'];
                }
                array_push($location[$skey], $warehouse->coordinates);
                $response['blocks'][$skey]['location'] = $location[$skey];

            }
            return $response['blocks'];
        }
        return [];
    }

    function getBlocksWithOrders($month = null, $year = null)
    {

        // $user = User::with([
        //     'assignedBlocks' => function ($q) use ($month, $year) {
        //         if (!is_null($month) && !is_null($year)) {
        //             $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
        //         }else{
        //             $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
        //         }
        //     },
        //     'assignedBlocks.orders',
        //     'assignedBlocks.orders.itemsScanInfo',
        //     'assignedBlocks.orders.items.scan_info',
        //     'assignedBlocks.orders.delivery_information'
        // ])
        // ->whereHas('assignedBlocks.orders', function($o){
        //     $o->whereIn('delivery_status', ['Delivered']);
        // })
        // ->find(\Auth::user()->id);

        // $user = User::with([
        //     'assignedBlocks' => function ($q) use ($month, $year) {
        //         if (!is_null($month) && !is_null($year)) {
        //             $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
        //         } else {
        //             $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
        //         }
        //     },
        //     'assignedBlocks.orders',
        //     'assignedBlocks.orders.itemsScanInfo',
        //     'assignedBlocks.orders.items.scan_info',
        //     'assignedBlocks.orders.delivery_information'
        // ])
        //     ->whereExists(function ($query) {
        //         $query->select(DB::raw(1))
        //             ->from('blocks')
        //             ->join('block_orders', 'blocks.id', '=', 'block_orders.block_id')
        //             ->join('orders', 'orders.id', '=', 'block_orders.order_id')
        //             ->whereColumn('users.id', 'blocks.user_id')
        //             ->where('orders.delivery_status', '=', 'Delivered')
        //             ->groupBy('blocks.id')
        //             ->havingRaw('COUNT(*) = COUNT(CASE WHEN orders.delivery_status = "Delivered" THEN 1 END)');
        //     })
        //     ->find(\Auth::user()->id);

        $user = User::with([
            'assignedBlocks' => function ($q) use ($month, $year) {
                if (!is_null($month) && !is_null($year)) {
                    $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
                } else {
                    $q->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
                }
                // add a subquery to filter only assignedBlocks where all orders are delivered
                $q->where(function ($query) {
                    $query->whereDoesntHave('orders', function ($query) {
                        $query->where('delivery_status', '<>', 'Delivered');
                    });
                });
            },
            'assignedBlocks.orders',
            'assignedBlocks.orders.itemsScanInfo',
            'assignedBlocks.orders.items.scan_info',
            'assignedBlocks.orders.delivery_information'
        ])->find(\Auth::user()->id);

        $warehouse_ids = [];
        $assigned = $user->assignedBlocks;
        if (count($assigned) > 0) {
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
                    // dd([$okey]);
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
                        $assigned[$akey]['orders'][$okey]['total_packages'] = ItemLabel::where(['order_id' => $o['id']])->count();
                        $assigned[$akey]['orders'][$okey]['verified_packages'] = ItemLabel::where(['order_id' => $o['id'], 'verified_by_rider' => 1])->count();
                        // array_push($totalPackages[$akey], $assigned[$akey]['orders'][$okey]['total_packages']);
                        // array_push($totalVerifiedPackages[$akey], $assigned[$akey]['orders'][$okey]['verified_packages']);
                    }
                    array_push($totalPackages[$akey], $assigned[$akey]['orders'][$okey]['total_packages']);
                    array_push($totalVerifiedPackages[$akey], $assigned[$akey]['orders'][$okey]['verified_packages']);
                    // dd($assigned[$akey]['orders'][$okey]);
                    //  = ;/
                    // dd();
                    //  array_push($assigned[$akey]->orders, $arrayScanInfo[$okey]);
                    // $this->super_unique($assigned[$akey]['orders'][$okey]['scanInfo'], 'qr_code');
                    // dd($o)
                    $assigned[$akey]['total_packages'] = (is_countable($totalPackages[$akey]) && count($totalPackages[$akey]) > 0) ? array_sum($totalPackages[$akey]) : 0;
                    $assigned[$akey]['verified_packages'] = (is_countable($totalVerifiedPackages[$akey]) && count($totalVerifiedPackages[$akey]) > 0) ? array_sum($totalVerifiedPackages[$akey]) : 0;
                }
            }

            $response['blocks'] = $assigned->toArray();


            foreach ($response['blocks'] as $skey => $a) {

                $location[$skey] = [];
                $drops[$skey] = [];
                array_push($location[$skey], $warehouse->coordinates);
                foreach ($a['orders'] as $key => $o) {

                    // $drops[$skey][$key] = $o['dropoff_coordinates'];
                    array_push($location[$skey], $o['dropoff_coordinates']);
                }

                // array_push($location[$skey], $warehouse->coordinates);
                // array_push($location[$skey], $drops[$skey]);

                $response['blocks'][$skey]['location'] = $location[$skey];

            }

            return $response['blocks'];
        }
        return [];
    }

    public static function gettrackingURL($riderId, $orderId)
    {
        $encryptedRider = Crypt::encrypt($riderId);
        $encryptedOrder = Crypt::encrypt($orderId);

        $url = url("/check/location?rider={$encryptedRider}&order={$encryptedOrder}");
        return $url;
    }

    public static function generateUniqStr($length = 6)
    {
        // Define the character set for the random string
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // Initialize an empty string to hold the unique string
        $uniqueString = '';

        // Generate the unique string
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen($characters) - 1);
            $uniqueString .= $characters[$randomIndex];
        }

        return $uniqueString;

    }

    public static function uploadFile($filepath, $file, $s3 = 0, $fileName = '')
    {

        $ext = strtolower($file->getClientOriginalExtension());
        if(empty($fileName)){
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
        }

        $filepath = $filepath.'/'. $fileName; // Adjust the S3 directory as needed

        if ($s3 == 0) {


            $path = public_path('/') . $filepath;

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Check if a file was uploaded
            if (isset($file) && !empty($file)) {

                // Generate a unique filename
                $filename = uniqid() . '_' . $file->getClientOriginalName();

                // Store the uploaded file in the specified directory
                $file->move($path, $filename);

                // Get the full path of the uploaded file
                $filePath = $filepath . '/' . $filename;

                // Optionally, return the path to the uploaded file
                return $filePath;
            }
        } else {
            if (isset($file) && !empty($file)) {


                if (in_array($ext, ['jpeg', 'png', 'jpg', 'webp'])) {
                    $image = Image::make($file);
                    $image->encode($ext, config('custom.image_quality'));
                    $fileContent = $image->stream()->__toString();
                } elseif (in_array($ext, ['pdf', 'docx'])) {
                    $fileContent = file_get_contents($file);
                } else {
                    return 'Unsupported file type';
                }

                // Upload the file to Amazon S3
                Storage::disk('s3')->put($filepath, $fileContent);

                // Get the URL of the uploaded file
                $url = Storage::disk('s3')->url($filepath);

                return $url;
            }
        }
    }



    public static function uploadMultipleFiles($file)
    {
        $url = [];
        if ($file) {
            foreach ($file as $key => $image) {
                $img_name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $img_name = $img_name . '-' . $key . time() . '.' . $image->getClientOriginalExtension();
                $ext = strtolower($image->getClientOriginalExtension());
                if ($ext == 'jpeg' || $ext == 'png' || $ext == 'jpg' || $ext == 'webp') {
                    // $url[$key] = url('images') . '/' . $img_name;
                    // $image = Image::make($image->getRealPath());
                    // $image->save('images/' . $img_name, Config::get('custom.image_quality'), Config::get('custom.image_encode'));

                    $image = Image::make($image);
                    $image->encode($ext, config('custom.image_quality'));
                    $path = 'images/' . $img_name;
                    Storage::disk('s3')->put($path, $image->stream()->__toString());
                    $url[$key] = Storage::disk('s3')->url($path);
                }

                if ($ext == 'pdf' || $ext == 'docx') {
                    // $url[$key] = url('documents') . '/' . $img_name;
                    // $image->move(public_path('documents'), $img_name);
                    $path = 'documents/' . $img_name;
                    Storage::disk('s3')->put($path, file_get_contents($image));
                    $url[$key] = Storage::disk('s3')->url($path);

                }
            }

        }
        return $url;
    }

    public static function getFinancials()
    {

        // dd(request()->all());
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $orders = Orders::with('items')->where('retailer_id', Auth::user()->retailer->id)
            ->where('delivery_status', 'delivered')->latest();


        if (isset(request()->zone) && !empty(request()->zone)) {
            $zone = request()->zone;
            $orders = $orders->where('zone_id', $zone);
        }

        if ((isset(request()->start_date) && isset(request()->end_date)) && !empty(request()->start_date) && !empty(request()->end_date)) {
            $orders = $orders->whereBetween('created_at', [request()->start_date, request()->end_date]);
        }

        $orders = $orders->get();
        $data['orders'] = $orders;

        if (isset($orders) && !empty($orders)) {
            $data['total_orders'] = count($orders);
            $data['total_payable'] = array_sum($orders->pluck('shipping_charges')->toArray());
            $data['orders_value'] = OptimizeTrait::itemsSum($orders);
        }

        return $data;
    }

    public static function itemsSum($orders)
    {
        $totalItems = [];

        if (isset($orders) && !empty($orders) && is_countable($orders)) {

            foreach ($orders as $order) {
                $itemsSum = 0;
                if (isset($order->items) && !empty($order->items)) {
                    $itemsSum = array_sum($order->items->pluck('price')->toArray());
                }
                array_push($totalItems, $itemsSum);
            }

        }
        if (isset($totalItems) && !empty($totalItems)) {
            return array_sum($totalItems);
        } else {
            return 0;
        }
    }

    public static function paginatedData($items, $perPage)
    {
        $currentPage = request('page', 1);
        // $perPage = 10;
        $paginatedData = array_slice($items, ($currentPage - 1) * $perPage, $perPage);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($paginatedData, count($items), $perPage);
        // $nextPageUrl = $paginator->nextPageUrl();
        // return [$paginator, $nextPageUrl];

        return [
            'count' => $paginator->count(),
            'current_page' => $paginator->currentPage(),
            'hasMorePages' => $paginator->hasMorePages(),
            'items' => $paginator->items(),
            'lastPage' => $paginator->lastPage(),
            'nextPage' => $paginator->nextPageUrl(),
            'previousPageUrl' => $paginator->previousPageUrl(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    public static function removeSessionFromDB($id){
        $sessions = UserSession::where('user_id', $id)->delete();
        // dd($sessions);
    }
}
