<?php

namespace App\Http\Controllers;

use App\Helpers\OptimizeTrait;
use App\Models\ItemLabel;
use App\Models\Items;
use App\Models\Orders;
use App\Models\Retailer;
use App\Models\Zone;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;

class GroceryController extends Controller
{
    use OptimizeTrait;

    public function placeOrder(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'enduser_name' => ['required'],
            'enduser_email' => ['required'],
            'enduser_address' => ['required'],
            'enduser_mobile' => ['required'],
            // 'payment_type' => ['required'],
            'order_number' => ['required'],
            'order_type' => ['required'],
            'item.*.sku' => ['required'],
            'item.*.name' => ['required'],
            // 'item.*.barcode' => ['required'],
            'item.*.price' => ['required', 'numeric'],
            'item.*.quantity' => ['required', 'numeric'],
            'item.*.weight' => ['required', 'numeric'],
            'item.*.length' => ['required', 'numeric'],
            'item.*.width' => ['required', 'numeric'],
            'item.*.height' => ['required', 'numeric']
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }
        $request['payment_type'] = 'card';


        $retailer = Retailer::with('charges')->where('secret_key', $request->bearerToken())->first();

        if (is_null($retailer)) {
            return response()->json(['message' => 'Invalid Secret Key'], 403);
        }

        $itemsPriceArr = [];
        foreach ($request->item as $ikey => $i) {
            $itemsPriceArr[$ikey] = $i['price'];
        }
        $retailerPromotionDiscountValue = 0;
        if (!is_null($retailer->activePromotion) && array_sum($itemsPriceArr) >= $retailer->activePromotion->min_order_value) {
            $retailerPromotionDiscountValue = $retailer->activePromotion->percentage;
        }


        if (array_sum($itemsPriceArr) >= 500) {
            $random = Str::random(6);

            $request['premium_code'] = $random;
            $request['is_premium'] = 1;
        }

        $zone = Zone::whereHas('postalcodes', function ($q) use ($request) {
            $q->where('postal', $request->dropoff_postal);
        })->first();
        // dd($zone);
        $request['order_key'] = str_replace('-', '', uuid_create());
        $request['retailer_id'] = $retailer->id;
        $request['zone_id'] = (!is_null($zone)) ? $zone->id : null;
        $drop = array(
            'country' => $request->dropoff_country,
            'city' => $request->dropoff_city,
            'postal' => $request->dropoff_postal,
        );

        $order = Orders::create($request->all());

        $order->delivery_information()->insert([
            'order_id' => $order->id,
            'order_qr_code' => 'eu_qr_' . $request->order_key,
        ]);

        $order->collector_delivery_information()->insert([
            'order_id' => $order->id,
            'order_qr_code' => 'eu_qr_' . $request->order_key,
        ]);
        $order->return_delivery_information()->insert([
            'order_id' => $order->id,
            'order_qr_code' => 'eu_qr_' . $request->order_key,
        ]);

        $order->drop_info = collect($drop);

        $items = $request->item;

        foreach ($items as $key => $i) {
            $items[$key]['volumetric_weight'] = $i['length'] * $i['width'] * $i['height'] / 5000;
            $items[$key]['order_id'] = $order->id;
            $items[$key]['dimension'] = $i['width'] . " x " . $i['height'];
            $items[$key]['height'] = $i['height'];
            $items[$key]['width'] = $i['width'];
            $items[$key]['length'] = $i['length'];
            $items[$key]['created_at'] = now();
            $items[$key]['updated_at'] = now();
        }
        // dd($items);
        //        dd($items);
        $order->items()->insert($items);

        // $shippingRates[0] = $this->pricesResource($this->getShippingRate($items, $order, $retailer));
        // foreach ($shippingRates as $key => $sr) {
        //     $retailerDiscount[$key] = (isset($retailer->price->extra_discount_percentage)) ? $retailer->price->extra_discount_percentage : 0;
        //     $retailerSurcharger[$key] = (isset($retailer->price->extra_surcharge)) ? $retailer->price->extra_surcharge : 0;

        //     $shippingRates[$key]['retailer_discount'] = $retailerDiscount[$key] . '%';
        //     $adminRetailerDiscount[$key] = $sr['price'] + $retailerSurcharger[$key] - ($retailerDiscount[$key] / 100) * $sr['price'];
        //     $shippingRates[$key]['price'] = $adminRetailerDiscount[$key] + $retailerSurcharger[$key] - ($retailerPromotionDiscountValue / 100) * $adminRetailerDiscount[$key];
        // }
        // dd();
        $updatechargersorder = Orders::select('id', 'order_number', 'enduser_name as customer_name', 'enduser_email as customer_email', 'enduser_mobile as customer_mobile', 'enduser_address as customer_address', 'shipping_charges', 'order_key')
            ->with('items:order_id,id,sku,name,price,quantity,weight,length,height,width')->find($order->id);
        // $updatechargersorder->shipping_charges = $shippingRates[0]['price'];
        $updatechargersorder->shipping_charges = 0;
        $updatechargersorder->is_grocery = 1;
        $updatechargersorder->save();

        $updatechargersorder = $updatechargersorder->toArray();
        unset($updatechargersorder['updated_at']);
        unset($updatechargersorder['distance_collector_num']);
        unset($updatechargersorder['retailer_address']);
        unset($updatechargersorder['retailer']);
        unset($updatechargersorder['id']);

        foreach ($updatechargersorder['items'] as $key => $item) {

            $updatechargersorder['items'][$key]['logistics_item_id'] = 'grocrery' . time() . '_' . $item['id'];
            unset($updatechargersorder['items'][$key]['order_id']);
            unset($updatechargersorder['items'][$key]['id']);
        }

        return response()->json(['order' => $updatechargersorder, 'message' => 'Order has been placed', 'status' => 200], 200);

    }

    public function readyForCollection($order_key, Request $request)
    {

        $order = Orders::select('collection_ready', 'order_key', 'id')->whereHas('retailer', function ($q) use ($request) {
            $q->where('secret_key', $request->bearerToken());
        })->where('order_key', $order_key)->first();

        if (is_null($order)) {
            return response()->json(['message' => 'Order not found', 'status' => 404], 404);
        }

        $order->collection_ready = 1;
        $order->save();

        $response['order_key'] = $order->order_key;
        $response['collection_ready'] = $order->collection_ready;
        return response()->json(['order' => $response, 'message' => 'Order has been ready for collection', 'status' => 200], 200);
    }

    public function labelGenerate(Request $request)
    {

        $item_id = [];
        foreach ($request->itemid as $id) {
            $item_id[] = explode('_', $id)[1];
        }
        $item_ids = array_unique($item_id);

        $order = Orders::where('order_key', $request->order_key)->first();

        $items = Items::with('order', 'scan_info')->find($item_ids);
        $label_last = ItemLabel::latest()->first();

        $last_id = '';
        if (is_null($label_last)) {
            $last_id = 1;
        } else {
            $last_id = $label_last->id + 1;
        }

        $qrCode = 'eu_qr_items_' . $order->order_number . time() . $last_id;
        $number = time() . $order->retailer->id;
        foreach ($items as $key => $i) {
            if (is_null($i->scan_info)) {
                $i->scan_info()->create([
                    'order_id' => $order->id,
                    'qr_code' => $qrCode,
                    'number' => $number
                ]);
            }
        }
        return response()->json(['qr_code' => $qrCode, 'items' => $request->itemid, 'message' => 'QR Generated', 'status' => 200], 200);
    }

    public function getLabel(Request $request)
    {

        $items = Items::with('scan_info', 'order.retailer.user', 'order.zone')
            ->whereHas('order', function ($o) use ($request) {
                $o->where('order_key', $request->order_key);
            })
            ->whereHas('scan_info', function ($q) use ($request) {
                $q->where('qr_code', $request->qr_code);
            })->get();
        // dd($items);
        $view = View('pdf.print_label', array('items' => $items->toArray(), 'qr' => $request->qr_code));
        return response()->json(array('items' => $items->toArray(), 'qr' => $request->qr_code));
        $pdf = \App::make('dompdf.wrapper')->setPaper([0, 0, 283.465, 425.197], 'portrait');
        $pdf->loadHTML($view->render());
        // return response()->json($pdf);
    }

}
