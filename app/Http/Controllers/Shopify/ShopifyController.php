<?php

namespace App\Http\Controllers\Shopify;

use App\Helpers\SecretKey;
use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Mail\ShopifyOrderPlaced;
use App\Models\BusinessHour;
use App\Models\Orders;
use App\Models\Password;
use App\Models\Retailer;
use App\Models\RetailerCharges;
use App\Models\RetailerChargesListItem;
use App\Models\Roles;
use App\Models\ShopifyPackage;
use App\Models\ShopifyToken;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonTimeZone;
use Illuminate\Support\Facades\Mail;

class ShopifyController extends Controller
{

    public function handleShopifyWebhook(Request $request)
    {    
        $data = $request->getContent();

        // Access the Shopify store URL and name from the request headers
        $shopifyStoreUrl = $request->header('X-Shopify-Shop-Domain');
        $shopifyStoreName = $this->extractStoreNameFromUrl($shopifyStoreUrl);

        Log::info('Store name: ' . $shopifyStoreName);
        Log::info('Store url: ' . $shopifyStoreUrl);

        // Process the order data here
        $orderData = json_decode($data, true);
        //     info($orderData);

        // Extract specific fields from the order data
        $orderNumber = $orderData['order_number'] ?? null;
        $totalPrice = $orderData['total_price'] ?? null;
        $email = $orderData['email'] ?? null;
        $orderId = $orderData['id'] ?? null;

    // Prepare the shopify order items for further processing
        $items = [];
        if (isset($orderData['line_items']) && is_array($orderData['line_items'])) {
            foreach ($orderData['line_items'] as $item) {
                for ($i = 1; $i <= $item['quantity']; $i++) {
                    $items[] = [
                        'name' => $item['name'],
                        'sku' => $item['sku'] ?? 'sku-1',
                        'quantity' => 1, 
                        'price' => $item['price'],
                        'weight' => '10',
                        'length' => '10',
                        'width' => '10',
                        'height' => '10',
                    ];
                }
            }
        }

    $userName = explode('.', $shopifyStoreUrl)[0];
    $store = User::with('retailer')->where('username', $userName)->first();

    if($store){
    $webhookPostalCode = strtoupper(str_replace(' ', '', $orderData['billing_address']['zip']));

    $zone = Zone::whereHas('postalcodes', function ($q) use ($webhookPostalCode) {
        $q->whereRaw("REPLACE(postal, ' ', '') LIKE ?", ['%' . $webhookPostalCode . '%']);
    })->first();

    info('webhook postal code');
    info($webhookPostalCode);

    if(!empty($zone)){
        $shippingLines = $orderData['shipping_lines'];
        foreach ($shippingLines as $shippingLine) {
            $shippingSource = $shippingLine['source'];
            $shippingTitle = $shippingLine['title'];
            $code = $shippingLine['code'];

        if ($shippingSource === 'Endup Logistics') {

        $request['webhookPostalCodeorder_key'] = str_replace('-', '', uuid_create());
        $request['zone_id'] = (!is_null($zone)) ? $zone->id : null;
        $request['order_key'] = str_replace('-', '', uuid_create());
        
        $shippingCharges = RetailerChargesListItem::where('shopify_package_id',$code)->value('price');

        $check = Orders::where('order_number',$orderData['id'])->first();
        if(empty($check))
        {
            info('in orders array');
            $order = Orders::create([
            'retailer_id' => $store->retailer->id,
            'order_number' => $orderData['id'],
            'payment_type' => 'card',
            'order_type' => 'standard',
            'enduser_address' => $orderData['billing_address']['address1'],
            'order_key' => $request['order_key'],
            'shipping_notes' => $orderData['note'] ?? $shippingTitle,
            'enduser_ordernotes' => '',
            'enduser_id' => null,
            'enduser_name' => $orderData['customer']['first_name'] . ' ' . $orderData['customer']['last_name'],
            'enduser_email' => $orderData['customer']['email'],
            'enduser_mobile' => $orderData['customer']['phone'] ?? '012345678910',
            'order_type_id' => 2,
            // 'pickuptime' => $request['pickuptime'],
            // 'pickupdate' => $request['pickupdate'],
            // 'deliverytime' => $request['deliverytime'],
            // 'deliverydate' => $request['deliverydate'],
            'dropoff_country' => $orderData['billing_address']['country'] ?? 'United Kingdom',
            'dropoff_city' => $orderData['billing_address']['city'] ?? 'Manchester',
            'dropoff_postal' =>$orderData['billing_address']['zip'] ?? 'Not Found',
            // 'pickup_coordinates' => $request['pickup_coordinates'],
            // 'dropoff_coordinates' => $request['dropoff_coordinates'],
            // 'pickup_postal_code' => $request->pickup_postal_code,
            'pickup_city' => 'Manchester',
            'pickup_country' => 'United Kingdom',
            'dropoff_address' => $orderData['shipping_address']['address1'] ?? 'null',
            // 'number_of_items' => $request['number_of_items'],
            'is_accepted' => 0,
            'zone_id' =>  $request['zone_id'],
            'delivery_type' => $shippingTitle,
            'is_shopify' => 1,
            'shipping_charges' => $shippingCharges ?? 0,
            'shopify_package_id' => $code,
            'order_total' => $totalPrice,
        ]);

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
        $order->items()->createMany($items);

        $order_details = Orders::where('order_number',$order->order_number)->with('items')->first();
        Mail::to($order_details->enduser_email)->send(new ShopifyOrderPlaced($order_details)); 

            }

    }
              }

            }
        }
        
        // Log the incoming webhook data for testing purposes
        // Log::info('Shopify Webhook Data: ' . $data);
        return response()->json(['success' => true], 200);
    } 


    private function extractStoreNameFromUrl($storeUrl)
{
    $parsedUrl = $storeUrl;
    // Check if the "host" key exists in the parsed URL array
    if (isset($parsedUrl)) {
        $subdomain = explode('.', $parsedUrl)[0];
        return $subdomain;
    }
    return 'unknown_store';
}


public function redirectToShopify(Request $request)
    {
        $scopes = Config::get('shopify.SHOPIFY_SCOPES');
        $redirectUri = 'https://enduptech.tijarah.ae/api/oauth/shopify/callback';
        $apiKey = Config::get('shopify.SHOPIFY_API_KEY');
        $shopDomain = $request->shop;
        return redirect("https://{$shopDomain}/admin/oauth/authorize?client_id={$apiKey}&scope={$scopes}&redirect_uri={$redirectUri}");
    }

public function handleCallback(Request $request)
{
    info('handle callback data check');
    info($request);
    $code = $request->query('code');
    $shopDomain = $request->shop;
    $shopName = explode('.', $shopDomain)[0];
    // Exchange the authorization code for an access token
    $accessToken = $this->getAccessTokenFromShopify($code,$shopDomain);
    info($accessToken);

    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $accessToken,
    ])->get("https://$shopDomain/admin/api/2023-07/shop.json");

    if ($response->successful()) {
        $storeInfo = $response->json()['shop'];
        info($storeInfo);
        $id = $storeInfo['id'];
        $email = $storeInfo['email'];
        $name = $storeInfo['name'];
        $address = $storeInfo['address1'];  
        $shopOwner = $storeInfo['shop_owner'];
        $phone = $storeInfo['phone'];

    $store = User::where('username', $shopName)->first();
    if($store){      
        $setToken = ShopifyToken::where('user_id',$store->id)->first();
        $setToken->user_id = $store->id;
        $setToken->access_token = $accessToken;
        $setToken->save();
    }else{
         $user = User::create([
           'name'=> $shopOwner,
           'username' => $name,
           'email' => $email,
           'mobile' => $phone ?? $id,
         ]);
         $user->password()->create([
            'password' => 'enduplogistics'
        ]);

         $setPassword = new ShopifyToken();
         $setPassword->user_id = $user->id;
         $setPassword->access_token = $accessToken;
         $setPassword->save();

         $business_type_id = '7';
         $currency_id = '1';
         $charges = '2';

         $role = Roles::where('name', 'Retailer')->first();
         $user->roles()->attach($role->id);
 
         $secret = new SecretKey('eu_sk_');
         $public = new SecretKey('eu_pk_');

         $user->retailer()->create([
            'business_type_id' => $business_type_id,
            'secret_key' => $secret->uuid,
            'public_key' => $public->uuid,
            'website' => $shopDomain,
            'currency_id' => $currency_id,
            'support_email' => $email,
            'support_mobile' => $phone ?? $id,
            'licensefile' => null,
            'address' => $address,
        ]);

        $user->retailer->charges()->attach($charges);
        $response = [
            "secret_key" => $secret->uuid,
            "public_key" => $public->uuid,
        ];
    }

    //  Register the fulfillment service
     $fulfillmentServiceName = 'Endup Logistics';
     $fulfillmentServiceCallbackUrl = 'https://enduptech.tijarah.ae/api/fulfillment-callback';
 
     $response = Http::withHeaders([
         'X-Shopify-Access-Token' => $accessToken,
         'Accept' => 'application/json',
     ])->post("https://{$shopDomain}/admin/api/2023-07/fulfillment_services.json", [
         'fulfillment_service' => [
             'name' => $fulfillmentServiceName,
             'callback_url' => $fulfillmentServiceCallbackUrl,
             'inventory_management' => true,
             'tracking_support' => true,
             'requires_shipping_method' => true,
             'format' => "json",
             'permits_sku_sharing' => true,
             'fulfillment_orders_opt_in' => true,
         ],
     ]);

     info($response);
 
     if ($response->successful()) {
        $shopifyStore = User::where('username', $name)->first();
         // Fulfillment service registered successfully
         $fulfillmentServiceData = $response->json()['fulfillment_service'];
         $fulfillmentServiceId = $fulfillmentServiceData['id'];
         if ($shopifyStore) {
            $token = ShopifyToken::where('user_id',$shopifyStore->id)->first();
            $token->fulfillment_service_id = $fulfillmentServiceId;
            $token->save();
        }
        info($fulfillmentServiceId);
     } else {
        info('error full');
         // Error registering the fulfillment service
         $errorMessage = $response->json()['errors'] ?? 'Unknown error';
         // Handle the error accordingly
     }

}

     $carrier = Http::withHeaders([
        'Content-Type' => 'application/json',
        'X-Shopify-Access-Token' => $accessToken,
    ])->post("https://{$shopDomain}/admin/api/2021-07/carrier_services.json", [
        'carrier_service' => [
            'name' => 'Endup Logistics',
            'callback_url' => 'https://enduptech.tijarah.ae/api/shopify/shipping/callback',
            'service_discovery' => true,
        ],
    ]);
    info($carrier);

    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $accessToken,
    ])->post("https://{$shopDomain}/admin/api/2023-07/webhooks.json", [
        'webhook' => [
            'topic' => 'orders/cancelled',
            'address' => 'https://enduptech.tijarah.ae/api/order/cancel',
            'format' => 'json',
        ],
    ]);

    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $accessToken,
    ])->post("https://{$shopDomain}/admin/api/2023-07/webhooks.json", [
        'webhook' => [
            'topic' => 'orders/create',
            'address' => 'https://enduptech.tijarah.ae/api/webhook/shopify',
            'format' => 'json',
        ],
    ]);

    // Log::info('Webhook Creation Response:', ['response' => $response->json()]);
    $store = User::with('retailer')->where('username',$shopName)->first();
    return view('shopify.welcome',compact('store'));
}

    private function getAccessTokenFromShopify($code, $shopDomain)
    {
        $apiKey = Config::get('shopify.SHOPIFY_API_KEY');
        $apiSecret = Config::get('shopify.SHOPIFY_API_SECRET'); 
        $response = Http::post("https://{$shopDomain}/admin/oauth/access_token", [
            'client_id' => $apiKey,
            'client_secret' => $apiSecret,
            'code' => $code,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['access_token'];
        } else {
            return null;
        }
    }


    public function orderCancel(Request $request)
    {
        $orderData = $request->all();

        $orderId = $orderData['id'];

        DB::table('orders')->where('order_number', $orderId)->update(['delivery_status' => 'cancelled']);
    
        return response()->json(['success' => true], 200);
    }


    public function updateOrderStatus($orderId)
    {
        $order = Orders::where('order_number',$orderId)->first();
        $store = Retailer::find($order->retailer_id);
        $cred = ShopifyToken::where('user_id',$store->user_id)->first();
        if($store){
        $accessToken = $cred->access_token;
        $orderId = $orderId;
        $domain = $store->website;

        $url = "https://$domain/admin/api/2022-10/orders/$orderId/fulfillment_orders.json";
        $fulfill = "https://$domain/admin/api/2023-07/fulfillments.json";
        $locationsUrl = "https://$domain/admin/api/2023-04/locations.json";
        $location = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
        ])->get($locationsUrl);

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
        ])->get($url);

        $fulfillmentOrderId = null;
        foreach ($response['fulfillment_orders'] as $fulfillmentOrder) {
            if ($fulfillmentOrder['status'] === 'open') {
                $fulfillmentOrderId = $fulfillmentOrder['id'];
                break;
            }
        }

        if ($location->successful()) {
            $locationsData = $location->json();
            $locationId = $locationsData['locations'][0]['id'];
        $data = [
            'fulfillment' => [
                'line_items_by_fulfillment_order' =>[
                    [
                    'fulfillment_order_id' =>  $fulfillmentOrderId
                    ],
                ],
                'fulfillment_service_id' => $store->fulfillment_service_id,
                'location_id' => $locationId,
                'tracking_info' => [
                    'number' => '001100',
                    'url' => 'https://enduptech.com',
                ],
            ],
        ];
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
        ])->post($fulfill,$data);

        }

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
        ])->post($url, $data);      
   
        $order->update([
       'delivery_status' => 'delivered'
        ]);
            
        return redirect()->back()->with('success','Order fulfilled successfully'); // Order fulfilled successfully
                
        }
    }

    public function handleFulfillmentCallback(Request $request)
{
    // Retrieve the data from the incoming request
    $shopDomain = $request->header('X-Shopify-Shop-Domain');
    $data = $request->all();

    $orderId = $data['id'];

    $username = explode('.', $shopDomain)[0];

    $order = Orders::where('order_number',$orderId)->first();
        $store = Retailer::where('website',$shopDomain)->first();
        if($store){
        $token = ShopifyToken::where('user_id',$store->user_id)->first();
        $fulfillmentServiceId = $token->fulfillment_service_id;
        $accessToken = $token->access_token;
        }
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
        ])->post("https://{$shopDomain}/admin/api/2023-07/orders/{$orderId}/fulfillments.json", [
            'fulfillment' => [
                'tracking_number' => 'asdmakldlamkfnaodn',
                'tracking_urls' => ['https://enduptech.com/tracking'], // Add actual tracking URL if applicable
                'notify_customer' => true,
                'fulfillment_service_id' => $fulfillmentServiceId, // Add the fulfillment service ID here
            ],
        ]);
        info('callaback');
        info($response);
        return response()->json(['success' => true]);
}


    public function handleShippingCallback(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        info('in callback');
        // if (isset($data['carrier_service']) && $data['carrier_service']['name'] === 'Standard | ENDUP') {
        //     switch ($data['carrier_service']['callback_url']) {
        //         case  $this->servicesCallbackUrl:
                    return $this->getRates($data);
            //     case $this->ratesCallbackUrl:
            //         return $this->getRates($data);
            //     default:
            //         break;
            // }
        // }
        // return response()->json(['error' => 'Invalid CarrierService callback.'], 400);

    }

    public function getServices()
    { 
     info('in services');
        $services = [
            [
                'name' => 'Standard (Same Day) via Endup Logistics',
                'callback_url' => 'https://enduptech.tijarah.ae/api/shopify/shipping/rates',
            ],
        ];
        return response()->json(['carrier_services' => $services]);
    }

    public function getRates($data)
    {
        $postalCode = $data['rate']['destination']['postal_code'];;
        // $destinationPostalCode = $request->input('destination')['postal_code'];
        // $selectedShippingMethod = $request->input('carrier_service')['name'];
        // info($destinationPostalCode);
        // $shippingRate = ShippingRate::where('shipping_method', $selectedShippingMethod)
        // ->where('destination_postal_code', $destinationPostalCode)
        // ->first(); 
        // if (!$shippingRate) {
        //     return response()->json(['error' => 'Shipping rate not available.'], 400);
        // }

    //     $servingAreas = DB::table('serving_areas')->select('codes')->get();
    //     $postalCodes = [];

    //     foreach ($servingAreas as $area) {
    //         // Split the comma-separated postal codes and store them in an array
    //         $codes = explode(',', $area->codes);
    //         // Remove spaces from each postal code and store them in the final array
    //         foreach ($codes as $code) {
    //             $postalCodes[] = str_replace(' ', '', $code);
    //         }
    //     }

        // $shippingLines = $responseData['shipping_lines'];
        // $firstTitle = null;

        // foreach ($shippingLines as $shippingLine) {
        // $title = $shippingLine['title'];
        // if (strpos($title, 'Endup Logistics') !== false) {
        // $firstTitle = $title;
        // break;
        // }
        // }
        $todayDate = Carbon::today()->format('Y-m-d');
        info('in rates');
        $postalCode =  strtoupper(str_replace(' ', '', $postalCode));

        $zone = Zone::whereHas('postalcodes', function ($q) use ($postalCode) {
            $q->whereRaw("REPLACE(postal, ' ', '') = ?", [$postalCode]);
        })->first();

        info($postalCode);
        if(!empty($zone)){
            info('finded');

            $shippingPackages = ShopifyPackage::with('businessHours')->where('status',1)->get();

            $ukTimeZone = new CarbonTimeZone('Europe/London');
            $currentDateTime = Carbon::now($ukTimeZone);
        
            $currentDay = strtolower($currentDateTime->englishDayOfWeek);
        
            foreach ($shippingPackages as $package) {
                $packageBusinessHours = collect($package->businessHours);
                $businessHours = $packageBusinessHours->firstWhere('day', $currentDay);
        
                if ($businessHours && $businessHours['open_time'] && $businessHours['close_time']) {
                    $openTime = Carbon::parse($businessHours['open_time'], $ukTimeZone);
                    $closeTime = Carbon::parse($businessHours['close_time'], $ukTimeZone);
        
                    if ($currentDateTime->between($openTime, $closeTime)) {
                        $rates[] = [
                            'service_name' => $package->name,
                            'service_code' => $package->id, 
                            'total_price' => 0, 
                            'currency' => 'GBP',
                            'min_delivery_date' => $todayDate,
                            'max_delivery_date' => $todayDate,
                        ];
                    }
                }
            }
        }else{
            $rates = [];
        }
        return response()->json(['rates' => $rates]);
    }


        public function singleOrder($orderId)
    {
        $order = Orders::with('items')->find($orderId);
        return response()->json($order);
    }

    public function shopifyStoreOrders($id)
    {
        $orders = Orders::with('items')->where('retailer_id', $id)->latest()->get();
        return view('shopify.orders',compact('orders'));
    }


}

    // $jqueryUrl = 'https://enduptech.tijarah.ae/assets/libs/jquery.min.js';
    // $customScriptUrl = 'https://enduptech.tijarah.ae/custom_checkout.js';

    // $script = Http::withHeaders([
    //     'X-Shopify-Access-Token' => $accessToken,
    // ])->post("https://{$shopDomain}/admin/api/2021-07/script_tags.json", [
    //     'script_tag' => [
    //         'event' => 'onload',
    //         'src' => $jqueryUrl,
    //         'display_scope' => 'all',
    //     ],
    // ]);
    
    // $script = Http::withHeaders([
    //     'X-Shopify-Access-Token' => $accessToken,
    // ])->post("https://{$shopDomain}/admin/api/2021-07/script_tags.json", [
    //     'script_tag' => [
    //         'event' => 'onload',
    //         'src' => $customScriptUrl,
    //         'display_scope' => 'all',
    //     ],
    // ]);
    //     info('script tag');
    //     info($script);

        //     // Match Postal Code with the database
    //     $servingAreas = DB::table('serving_areas')->select('codes')->get();
    //     $postalCodes = [];

    //     foreach ($servingAreas as $area) {
    //         // Split the comma-separated postal codes and store them in an array
    //         $codes = explode(',', $area->codes);
    //         // Remove spaces from each postal code and store them in the final array
    //         foreach ($codes as $code) {
    //             $postalCodes[] = str_replace(' ', '', $code);
    //         }
    //     }

    // $webhookPostalCode = strtoupper(str_replace(' ', '', $orderData['billing_address']['zip']));

    //     if (in_array($webhookPostalCode, $postalCodes)) {
    //                 info('in area');
    //     }else{
    //         info('out area');
    //     }


     // Store the extracted data in the database against the store
            // DB::table('shopify_orders')->insert([
            //     'store_name' => $shopifyStoreName,
            //     'store_id'=> $store->id,
            //     'order_number' => $orderId,
            //     'total_price' => $totalPrice,
            //     'email' => $email,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);


        // $order = new ShopifyOrder();
        // $order->id = $orderData['id'];
        // $order->store_id = $store->id;
        // $order->store_name = $shopifyStoreName;
        // $order->order_number = $orderData['order_number'];
        // $order->total_price = $orderData['total_price'];
        // $order->email = $orderData['email'];
        // $order->order_id = $orderData['id'];
        // $order->name = $orderData['customer']['first_name'] . ' ' . $orderData['customer']['last_name'];
        // $order->type = 'Normal';
        // $order->address = $orderData['billing_address']['address1'];
        // $order->mobile = $orderData['customer']['phone'];
        // $order->postal = $orderData['billing_address']['zip'];
        // $order->country = $orderData['billing_address']['country'];
        // $order->notes = $orderData['note'];
        // $order->house_address = $orderData['shipping_address']['address1'];
        // $order->status = 'in_progress';
        // $order->save();

        // if (isset($orderData['line_items']) && is_array($orderData['line_items'])) {
        //     foreach ($orderData['line_items'] as $item) {
        //         for ($i = 1; $i <= $item['quantity']; $i++) {
        //             $orderItems = new ShopifyOrderItem();
        //                 $orderItems->order_id = $orderData['id'];
        //                 $orderItems->name = $item['name'];
        //                 $orderItems->sku = $item['sku'];
        //                 $orderItems->quantity = 1;
        //                 $orderItems->price = $item['price'];
        //                 $orderItems->save();
        //         }
        //     }
        // }


            // Prepare the shopify order details for further processing
            // $apiData = [
            //     'id' => $orderData['id'],
            //     'order_number' => $orderData['order_number'],
            //     'order_type' => 'normal',
            //     'enduser_name' => $orderData['customer']['first_name'] . ' ' . $orderData['customer']['last_name'],
            //     'enduser_email' => $orderData['customer']['email'],
            //     'enduser_address' => $orderData['billing_address']['address1'] ?? 'no address added',
            //     'enduser_mobile' => $orderData['customer']['phone'] ?? '012345678910',
            //     'dropoff_postal' => $orderData['billing_address']['zip'] ?? 'Not Found',
            //     'dropoff_country' => $orderData['billing_address']['country'] ?? 'United Kingdom',
            //     'dropoff_city' => $orderData['billing_address']['city'] ?? 'Manchester',
            //     'enduser_ordernotes' => $orderData['note'] ?? 'null',
            //     'house_address' => $orderData['shipping_address']['address1'] ?? 'null',
            //     'items' => $items,
            // ]; 



            // public function checkPostalCode(Request $request)
            // {
            //     $postalCode = $request->input('postal_code');
            //     info('done hogya');
            //     if ($postalCode == 'M24WU') {
            //         return response()->json(['available' => true]);
            //     } else {
            //         return response()->json(['available' => false]);
            //     }
    
            // }
    
    
        // public function scriptTag(Request $request){
    
        //      $store = ShopifyStore::where('id',18)->first();
        //      $shopDomain = $store->store_url;
        //     $accessToken = $store->access_token; // Replace with the access token generated earlier
        
        //     $response = Http::withHeaders([
        //     'X-Shopify-Access-Token' => $accessToken,
        //     ])->post("https://{$shopDomain}/admin/api/2023-07/script_tags.json", [
        //     'script_tag' => [
        //     'event' => 'onload',
        //     'src' => 'https://enduptech.tijarah.ae/custom_checkout.js',
        //     'display_scope' => 'checkout',
        //     ],
        //     ]);
        //     if ($response->successful()) {
        //     $scriptTag = $response->json()['script_tag'];
        //     info($scriptTag);
        //     } else {
        //     $errors = $response->json()['errors'];
        //     info($errors);
        //     }
        // }
    
        // public function checkBusinessHours(Request $request)
        // {
        //     $isOpen = $this->isBusinessOpen($request->current_time);
        //     return response()->json(['isOpen' => $isOpen]);
        // }
    
        // private function isBusinessOpen($current_time)
        // {
        //     $ukCurrentTimeCarbon = Carbon::parse($current_time);
        //     $businessHoursOpen = Carbon::parse('08:00:00', 'Europe/London');
        //     $businessHoursClose = Carbon::parse('23:00:00', 'Europe/London');
        //     $isOpen = $ukCurrentTimeCarbon->between($businessHoursOpen, $businessHoursClose);
        //     return $isOpen; 
        // }

          // $response = Http::withHeaders([
        //     'X-Shopify-Access-Token' => $accessToken,
        // ])->put("https://{$domain}/admin/api/2023-04/orders/{$orderId}.json", [
        //     'order' => [
        //         'note' => 'delivered',
        //         'metafields' => [
        //             [
        //             "key" => "order_status",
        //             "value" => "delivered",
        //             "type" => "single_line_text_field",
        //             "namespace" => "global"
        //             ]
        //             ],
        //     ],
        // ]);

        // $shopifyApiBaseUrl = "https://{$domain}/admin/api/2023-07/";
            
        //     // Set up headers with the access token
        //     $headers = [
        //         'X-Shopify-Access-Token' => $accessToken,
        //         'Accept' => 'application/json',
        //         'Content-Type' => 'application/json',
        //     ];
        
        
        //         // Update order status to "delivered" by adding a custom note
        //         $noteData = [
        //             'order' => [
        //                 'id' => $orderId,
        //                 'note' => 'Order delivered.'
        //             ]
        //         ];
        
        //         // Make a PUT request to update the order
        //         $response = Http::withHeaders($headers)->put($shopifyApiBaseUrl . "orders/{$orderId}.json", $noteData);

        
    // function selectAppropriateLocation($locationsData)
    // {
    //     if (!empty($locationsData['locations'])) {
    //         return $locationsData['locations'][0]['id'];
    //     }
    //     // Return a default location ID if no locations are available (you may modify this as needed)
    //     return 'default-location-id';
    // }