<?php

namespace App\Http\Controllers;

use App\Helpers\OptimizeTrait;
use App\Models\ItemLabel;
use App\Models\Items;
use App\Models\Orders;
use App\Models\Price;
use App\Models\Retailer;
use App\Models\RetailerCharges;
use App\Models\RetailerChargesListItem;
use App\Models\RetailerPromotion;
use App\Models\ReturnOrder;
use App\Models\ShopifyPackage;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RetailerController extends Controller
{
    use OptimizeTrait;

    public function orderAPI(Request $request)
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

        // dd($request->all());
        if ($request->bearerToken()) {
            info('in bearee');
            $retailer = Retailer::with('charges')->where('secret_key', $request->bearerToken())->first();
        } else {

            $retailer = Retailer::with('charges')->find($request->retailer);


        }
        if (is_null($retailer)) {
            return response()->json(['message' => 'Invalid Secret Key'], 403);
        }

        $retailerCharges = RetailerCharges::where('retailer_id', $retailer->id)->value('retailer_charges_list_id');

        $type = RetailerChargesListItem::where('shopify_package_id','<>',null)->whereHas('retailerCharges', function ($query) use($retailerCharges)  {
            $query->where('active', 1)->where('id', $retailerCharges);
        })->whereHas('shopifyPackage', function ($query) use($request) {
            $query->where('id',$request->order_type);
        })
        ->with(['shopifyPackage' => function ($query) use ($request) {
            $query->where('id', $request->order_type);
        }])->first();

        if(empty($type)){
            return response()->json(['message' => 'Invalid Order Type.'], 403);
        }

        $request['order_type'] = $type->shopifyPackage->name;
        $request['shopify_package_id'] = $type->shopifyPackage->id;
        $shippingCharges = $type->price;

        $itemsinput = [];
        if (!$request->bearerToken()) {
            foreach ($request->sku as $key => $sku) {
                $itemsinput[$key]['sku'] = $sku;
                $itemsinput[$key]['name'] = $request['name'][$key];
                // $itemsinput[$key]['barcode'] = $request['barcode'][$key];
                $itemsinput[$key]['price'] = $request['price'][$key];
                $itemsinput[$key]['quantity'] = $request['quantity'][$key];
                $itemsinput[$key]['weight'] = $request['weight'][$key];
                $itemsinput[$key]['length'] = $request['length'][$key];
                $itemsinput[$key]['width'] = $request['width'][$key];
                $itemsinput[$key]['height'] = $request['height'][$key];
            }
            $request['item'] = $itemsinput;
        }
        $itemsPriceArr = [];
        foreach ($request->item as $ikey => $i) {
            $itemsPriceArr[$ikey] = $i['price'];
        }
        $retailerPromotionDiscountValue = 0;
        if (!is_null($retailer->activePromotion) && array_sum($itemsPriceArr) >= $retailer->activePromotion->min_order_value) {
            $retailerPromotionDiscountValue = $retailer->activePromotion->percentage;
        }
        // dd(array_sum($itemsPriceArr));

        if (array_sum($itemsPriceArr) >= 500) {
            $random = Str::random(6);

            $request['premium_code'] = $random;
            $request['is_premium'] = 1;
        }

        $zone = Zone::whereHas('postalcodes', function ($q) use ($request){
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
        // DB::statement('SET FOREIGN_KEY_CHECKS=0');
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

        $arr = $request->item;

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
        $order->items()->insert($arr);

        // $shippingRates[0] = $this->pricesResource($this->getShippingRate($items, $order, $retailer));
        // foreach ($shippingRates as $key => $sr) {
        //     $retailerDiscount[$key] = (isset($retailer->price->extra_discount_percentage)) ? $retailer->price->extra_discount_percentage : 0;
        //     $retailerSurcharger[$key] = (isset($retailer->price->extra_surcharge)) ? $retailer->price->extra_surcharge : 0;

        //     $shippingRates[$key]['retailer_discount'] = $retailerDiscount[$key] . '%';
        //     $adminRetailerDiscount[$key] = $sr['price'] + $retailerSurcharger[$key] - ($retailerDiscount[$key] / 100) * $sr['price'];
        //     $shippingRates[$key]['price'] = $adminRetailerDiscount[$key] + $retailerSurcharger[$key] - ($retailerPromotionDiscountValue / 100) * $adminRetailerDiscount[$key];
        // }
        // dd();
        $updatechargersorder = Orders::with('items')->find($order->id);
        $updatechargersorder->shipping_charges = $shippingCharges;
        $updatechargersorder->save();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1');

        if ($request->bearerToken()) {
            // return response()->json(['order' => $updatechargersorder, 'prices' => $shippingRates, 'message' => 'Prices Fetched', 'status' => 200], 200);
            return response()->json(['order' => $updatechargersorder, 'message' => 'Order has been placed', 'status' => 200], 200);
        } else {
            return redirect()->route('orders')->with('success', 'Order Placed Successfully');
        }
    }

    public function placeOrder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_key' => ['required'],
            'price_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $price = Price::find($request->price_id);
        $order = Orders::with('items')->where('order_key', $request->order_key)->first();
        $order->shipping_charges = $price->price;
        $order->save();

        $order->delivery_information()->insert([
            'order_id' => $order->id,
            'order_qr_code' => 'eu_qr_' . $request->order_key,
        ]);

        return response()->json(['order' => $order, 'message' => 'Order Posted', 'status' => 200], 200);
    }


    public function promotionsIndex()
    {
        $retailer = Auth::user()->retailer;
        //        dd($retailer);
        return view('promotions.index', compact('retailer'));
    }

    public function promotionUpdate($id, Request $request)
    {
        //        dd($request->all());
        $promotion = RetailerPromotion::where('retailer_id', $id)->first();
        if (is_null($promotion)) {
            $promotion = new RetailerPromotion();
        }
        $promotion->start_date = $request->start_date;
        $promotion->end_date = $request->last_date;
        $promotion->percentage = $request->percentage;
        $promotion->min_order_value = $request->min_order_value;
        $promotion->retailer_id = $id;
        $promotion->save();

        return back()->with('success', 'Promotion Updated');
    }

    public function manualOrder()
    {

        $retailers = Retailer::with('user')->get();
        $packages = RetailerChargesListItem::where('shopify_package_id','<>',null)->whereHas('retailerCharges', function ($query) {
            $query->where('active', 1);
        })
        ->with(['shopifyPackage' => function ($query) {
            $query->where('status', 1);
        }])->distinct('shopify_package_id')->get(['shopify_package_id']);

        $uniqueShopifyPackageIds = $packages->pluck('shopify_package_id')->toArray();
        $packages = ShopifyPackage::whereIn('id', $uniqueShopifyPackageIds)->get();

        return view('orders.create', compact('retailers','packages'));
    }

    public function editOrder($id)
    {
        $retailers = Retailer::with('user')->get();
        $order = Orders::find($id);
        $packages = RetailerChargesListItem::where('shopify_package_id','<>',null)->whereHas('retailerCharges', function ($query) {
            $query->where('active', 1);
        })
        ->with(['shopifyPackage' => function ($query) {
            $query->where('status', 1);
        }])->distinct('shopify_package_id')->get(['shopify_package_id']);

        $uniqueShopifyPackageIds = $packages->pluck('shopify_package_id')->toArray();
        $packages = ShopifyPackage::whereIn('id', $uniqueShopifyPackageIds)->get();

        return view('orders.create', compact('retailers', 'order','packages'));
    }

    public function updateOrder($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'retailer' => ['required'],
            'dropoff_country' => ['required'],
            // 'dropoff_city' => ['required'],
            'dropoff_postal' => ['required'],
            'enduser_name' => ['required'],
            'enduser_email' => ['required'],
            'enduser_address' => ['required'],
            'enduser_mobile' => ['required'],
            // 'payment_type' => ['required'],
            'order_number' => ['required'],
            'order_type' => ['required'],
            // 'item.*.sku' => ['required'],
            'item.*.name' => ['required'],
            // 'item.*.barcode' => ['required'],
            'item.*.price' => ['required', 'numeric'],
            'item.*.quantity' => ['required', 'numeric'],
            'item.*.weight' => ['required', 'numeric'],
            'item.*.length' => ['required', 'numeric'],
            'item.*.width' => ['required', 'numeric'],
            'item.*.height' => ['required', 'numeric'],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }



        $retailer = Retailer::find($request->retailer);

        $retailerCharges = RetailerCharges::where('retailer_id', $retailer->id)->value('retailer_charges_list_id');

        $type = RetailerChargesListItem::where('shopify_package_id','<>',null)->whereHas('retailerCharges', function ($query) use($retailerCharges) {
            $query->where('active', 1)->where('id', $retailerCharges);
        })->whereHas('shopifyPackage', function ($query) use($request) {
            $query->where('id',$request->order_type);
        })
        ->with(['shopifyPackage' => function ($query) use ($request) {
            $query->where('id', $request->order_type);
        }])->first();

        $request['order_type'] = $type->shopifyPackage->name;
        $request['shopify_package_id'] = $type->shopifyPackage->id;
        $shippingCharges = $type->price;

        $itemsinput = [];
        if (!$request->bearerToken()) {
            foreach ($request->sku as $key => $sku) {

                $itemsinput[$key]['sku'] = $sku;
                $itemsinput[$key]['name'] = $request['name'][$key];
                // $itemsinput[$key]['barcode'] = $request['barcode'][$key];
                $itemsinput[$key]['price'] = $request['price'][$key];
                $itemsinput[$key]['quantity'] = $request['quantity'][$key];
                $itemsinput[$key]['weight'] = $request['weight'][$key];
                $itemsinput[$key]['length'] = $request['length'][$key];
                $itemsinput[$key]['width'] = $request['width'][$key];
                $itemsinput[$key]['height'] = $request['height'][$key];
            }
            $request['item'] = $itemsinput;
        }

        $itemsPriceArr = [];
        foreach ($request->item as $ikey => $i) {
            $itemsPriceArr[$ikey] = $i['price'];
        }

        $retailerPromotionDiscountValue = 0;

        if (!is_null($retailer->activePromotion) && array_sum($itemsPriceArr) >= $retailer->activePromotion->min_order_value) {
            $retailerPromotionDiscountValue = $retailer->activePromotion->percentage;
        }
        // DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $order = Orders::find($id);
        $zone = Zone::whereHas('postalcodes', function ($q) use ($request){
            $q->where('postal', $request->dropoff_postal);
        })->first();
        $request['retailer_id'] = $retailer->id;
        $request['zone_id'] = (!is_null($zone)) ? $zone->id : null;
        $order->update($request->all());

        // dd($order);
        $drop = array(
            'country' => $request->dropoff_country,
            'city' => $request->dropoff_city,
            'postal' => $request->dropoff_postal,
        );
        $order->drop_info = collect($drop);

        $arr = $request->item;

        // $order->items()->delete();
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

        $order->items()->delete();
        $order->items()->insert($arr);

        // $shippingRates[0] = $this->pricesResource($this->getShippingRateUpdate($items, $order, $retailer));
        // foreach ($shippingRates as $key => $sr) {
        //     $retailerDiscount[$key] = (isset($retailer->price->extra_discount_percentage)) ? $retailer->price->extra_discount_percentage : 0;
        //     $retailerSurcharger[$key] = (isset($retailer->price->extra_surcharge)) ? $retailer->price->extra_surcharge : 0;

        //     $shippingRates[$key]['retailer_discount'] = $retailerDiscount[$key] . '%';
        //     $adminRetailerDiscount[$key] = $sr['price'] + $retailerSurcharger[$key] - ($retailerDiscount[$key] / 100) * $sr['price'];
        //     $shippingRates[$key]['price'] = $adminRetailerDiscount[$key] + $retailerSurcharger[$key] - ($retailerPromotionDiscountValue / 100) * $adminRetailerDiscount[$key];
        // }
        // dd();
        $updatechargersorder = Orders::with('items')->find($order->id);
        $updatechargersorder->shipping_charges = $shippingCharges;
        $updatechargersorder->save();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1');


        return redirect()->route('orders')->with('success', 'Order Updated Successfully');
        // dd($id, $request->all(), $order);
    }

    public function generateItemLabel(Request $request)
    {

        $messages = [
            'itemid.required' => 'Please select atleast 1 Item',
        ];

        $validator = Validator::make($request->all(), [
            'itemid' => ['required'],
        ], $messages);



        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }


        $item_ids = array_unique($request->itemid);

        $order = Orders::find($request->order_id);

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

        return redirect()->back()->with('success', 'Label Generated');

    }


    public function acceptReturn($id){
        $order = Orders::find($id);
        $order->return = 2;
        $order->save();

        return redirect()->back()->with('success', 'Return Accepted');
    }

    public function settings()
    {
        $user = User::where('id',Auth::id())->with('retailer')->first();
        return view('retailers.settings',compact('user'));
    }

    public function saveSettings(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'mobile' => ['required', Rule::unique('users')->ignore($id)],
            'password' => ['nullable'],
            'website' => ['required'],
            'licensefile' => ['nullable', 'mimes:pdf,docx', 'max:2048'],
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::find($id);
        $user->update([
            "name" => $request->name,
            "address" => $request->address,
            "mobile" => $request->mobile,
            "phone" => $request->phone,
        ]);

        if ($request->has('password') && !is_null($request->password)) {
            $request['password'] = Hash::make($request->password);
            $updatePass = $user->password()->update([
                'password' => $request->password
            ]);
        }

        $licenseFile = null;

        if ($request->hasFile('licensefile')) {
            $fileName = time() . '_' . $request->licensefile->getClientOriginalName();
            $filePath = $request->file('licensefile')->storeAs('licenseFiles', $fileName, 'public');
            $name = time() . '_' . $request->licensefile->getClientOriginalName();
            $file_path = url('/storage/' . $filePath);
            $licenseFile = $file_path;
        }

        $retailer = Retailer::where('user_id', $user->id)->first();
        $retailer->user_id = $user->id;
        $retailer->website = $request->website;
        $retailer->support_email = $request->support_email;
        $retailer->support_mobile = $request->support_mobile;
        $retailer->address = $request->address;
        if (!is_null($licenseFile)) {
            $retailer->licensefile = $licenseFile;
        }
        $retailer->save();

        return redirect()->back()->with('success','Settings updated successfully');

    }

    public function returnAcceptApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => ['required'],
            'reason' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $retailer = Retailer::where('secret_key', $request->bearerToken())->first();

        if (empty($retailer)) {
            return response()->json(['message' => 'Invalid Secret Key'], 403);
        }

        $order = Orders::with('items', 'retailer')->where('retailer_id', $retailer->id)
        ->where('order_number', $request->order_number)->first();

        if (empty($order)) {
            return response()->json(['message' => 'Order not found'], 404);
            }

        if($order->delivery_status != 'Delivered')
        {
        return response()->json(['message' => 'You are not allowed to request return on undelivered orders.'], 403);
        }
        
        $order->return = 2;
        $order->save();
        
        $return = new ReturnOrder();
        $return->order_id = $order->id;
        $return->reason = $request->reason;
        $return->save();


        return response()->json(['message' => 'Return Initiated'], 200);
    }

    public function addNotesToOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => ['required'],
            'notes' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9 .,!?&\-\'"()]+$/', // Allow letters, numbers, spaces, and selected punctuation.
                'max:255', // Adjust the max length based on your column's varchar limit.
            ],
        ], [
            'notes.regex' => 'The notes field may only contain letters, numbers, spaces, and selected punctuation.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $retailer = Retailer::where('secret_key', $request->bearerToken())->first();

        if (empty($retailer)) {
            return response()->json(['message' => 'Invalid Secret Key'], 403);
        }

        $order = Orders::where('retailer_id', $retailer->id)->where('order_number', $request->order_number)->first();

        if (empty($order)) {
            return response()->json(['message' => 'Order not found.'], 403);
            }

            $order->shipping_notes = $request->notes;
            $order->save();
            return response()->json(['message' => 'Notes added succssfully'], 200);
    }

}
