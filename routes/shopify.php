<?php

use App\Http\Controllers\Shopify\ShopifyController;
use App\Mail\ShopifyOrderPlaced;
use App\Models\BusinessHour;
use App\Models\Orders;
use App\Models\ShopifyPackage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


Route::get('/orders/{orderId}', [ShopifyController::class,'singleOrder']);

Route::get('/oauth/shopify', [ShopifyController::class,'redirectToShopify'])->name('shopify.auth.redirect');
Route::get('/oauth/shopify/callback',[ShopifyController::class,'handleCallback'])->name('shopify.auth.callback');

Route::post('/fulfillment-callback', [ShopifyController::class,'handleFulfillmentCallback']);

Route::get('/add-script-tag', [ShopifyController::class,'scriptTag']);


// Route::get('bh',function(){
//     $order_details = Orders::where('order_number','5490735939901')->with('items')->first();
//     Mail::to($order_details->enduser_email)->send(new ShopifyOrderPlaced($order_details)); 
// });

Route::post('/check-business-hours',[ShopifyController::class,'checkBusinessHours']);

Route::post('/webhook/shopify', [ShopifyController::class,'handleShopifyWebhook']);

Route::post('/order/cancel', [ShopifyController::class,'orderCancel']);
Route::get('/update/order/{orderId}', [ShopifyController::class,'updateOrderStatus'])->name('update.shopify.order');

Route::get('/check-postal-code',[ShopifyController::class,'checkPostalCode']);

Route::post('/shopify/shipping/callback', [ShopifyController::class,'handleShippingCallback']);

Route::post('/shopify/shipping/services', [ShopifyController::class,'getServices'])
    ->name('shopify.shipping.services');
    
Route::post('/shopify/shipping/rates', [ShopifyController::class,'getRates'])
->name('shopify.shipping.rates');

Route::get('/shopify/orders/{id}',[ShopifyController::class,'shopifyStoreOrders'])->name('shopify.store.orders');
Route::get('/orders/{orderId}', [ShopifyController::class,'singleOrder']);
































?>