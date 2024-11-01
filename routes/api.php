<?php
require __DIR__.'/shopify.php';
use App\Events\LiveLocation;
use App\Http\Controllers\ReturnController;
use App\Models\Rider;
use App\Models\RidersLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\EndUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\EndUserController;
use App\Http\Controllers\CollectorController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\GroceryController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\Shopify\ShopifyController;
use App\Models\EndupCommission;
use App\Models\EndUsers;
use App\Models\PostalCode;
use App\Models\ShopifyPackage;
use App\Models\Zone;
use Carbon\CarbonTimeZone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('test',[EndUserController::class,'test']);
Route::post('/order/delivery_time', [RiderController::class, 'deliveryTime']);
Route::post('/retailers/add', [\App\Http\Controllers\CollectorController::class, 'insertRetailer']);
Route::post('/rider/login', [RiderController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    // rider
    Route::post('/rider/edit', [RiderController::class, 'editRider']);
    Route::get('/rider/assigned-orders', [RiderController::class, 'assignOrders']);
    Route::post('/rider/online/update', [RiderController::class, 'changeOnlineStatus']);
    Route::post('/rider/update/order/status', [RiderController::class, 'updateOrderStatus']);
    Route::post('/rider/verify/block', [RiderController::class, 'qrScan']);
    // Route::post('/rider/verify/block', [RiderController::class, 'verifyBlock']);
    Route::post('/rider/update/delivery_information', [RiderController::class, 'updateDeliveryInformation']);
    Route::get('/rider/calculate_earnings', [RiderController::class, 'calculateEarnings']);
    Route::get('/rider/earnings', [RiderController::class, 'earnings']);
    Route::get('rider/get-all-earnings', [RiderController::class, 'getAllEarnings']);
    Route::post('rider/verify-order-status-change', [RiderController::class, 'changeOrderVerificationStatus']);
    // Route::post('rider/verify', [RiderController::class, 'qrScan']);



    // collector
    Route::post('/collector/edit', [RiderController::class, 'editRider']);
    // Route::get('/collector/assigned-orders', [CollectorController::class, 'assignOrders']);
    Route::post('/collector/online/update', [RiderController::class, 'changeOnlineStatus']);
    Route::post('/collector/update/order/status', [CollectorController::class, 'updateOrderStatus']);
    Route::get('collector/assigned-orders', [CollectorController::class, 'assignOrders']);
    Route::get('collector/assigned-return-blocks', [ReturnController::class, 'assignOrders']);
    Route::post('collector/verify/items', [CollectorController::class, 'verifyItems']);
    Route::get('collector/caculate_earnings', [CollectorController::class, 'caculateEarnings']);
    Route::get('collector/caculate_earnings_return_retailer', [CollectorController::class, 'caculateEarningsReturn']);
    Route::get('collector/caculate_earnings_return_warehouse', [ReturnController::class, 'caculateEarnings']);
    Route::post('collector/update/multiple', [CollectorController::class, 'updateMultiple']);
    // Route::post('collector/verify/collection', [CollectorController::class, 'verifyCollection']);
    Route::post('collector/verify/collection', [CollectorController::class, 'qrScan']);
    Route::get('collector/get-all-earnings', [CollectorController::class, 'getAllEarnings']);
    Route::get('collector/earnings', [CollectorController::class, 'earnings']);
    Route::get('auth', [EndUserController::class, 'checkAuth']);
    Route::get('allpackages', [PackageController::class, 'viewPackages']);

    Route::post('collector/return-verify-order-status-change', [CollectorController::class, 'changeOrderVerificationStatusReturnCollector']);
    Route::post('collector/return/delivered', [CollectorController::class, 'updateDeliveryInformation']);

    Route::get('collector/return-to-retailer/collections', [ReturnController::class, 'returnToRetailerCollections']);
});

/* ****************** */
/* **End User Routes **/
/* ****************** */

Route::post('enduser/register', [EndUserController::class, 'register']);
Route::post('enduser/login', [EndUserController::class, 'login']);

/* End User - SOCIAL LOGINS */

Route::post('loginwithfacebook', [EndUserController::class, 'loginWithFacebook']);
Route::post('loginwithgoogle', [EndUserController::class, 'loginWithGoogle']);
Route::post('loginwithapplepay', [EndUserController::class, 'loginWithApplePay']);
Route::get('numberverification', [EndUserController::class, 'numberVerification']);


Route::post('validateNumberForForgotPassword', [EndUserController::class, 'validateNumberForForgotPassword']);

Route::post('changePassword', [EndUserController::class, 'changePassword']);

Route::get('update/location', function (Request $request) {
    $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), array('cluster' => env('PUSHER_APP_CLUSTER')));
    $rider = Rider::with('location')->find($request->rider);
    if (!is_null($rider)) {
        if (is_null($rider->location)) {
            $rider->location()->create($request->except(['rider']));
            $location = $request->all();
            // event(new LiveLocation($rider->id, $request->coordinates));
            // $pusher->trigger('endup_channel', 'live_location_' . $rider->id, ['rider' => $rider->id, 'coordinates' => $request->coordinates]);
            return response()->json(['message' => 'Live Location created', 'status' => 200], 200);
        } else {
            $rider->location()->update($request->except(['rider']));
            $location = $request->all();
            // $pusher->trigger('endup_channel', 'live_location_' . $rider->id, ['rider' => $rider->id, 'coordinates' => $request->coordinates]);
            return response()->json(['message' => 'Live Location updated', 'status' => 200], 200);
        }
    } else {
        return response()->json(['message' => 'Carrier not found', 'status' => 404], 404);
    }
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('getenduserinfo', [EndUserController::class, 'getEndUserInfo']);
    Route::get('document_type', [HomeController::class, 'getallDocumentTypes']);
    Route::post('enduser/order/placed', [EndUserController::class, 'placeOrder']);
    Route::get('checkdiscountcode', [EndUserController::class, 'checkDiscountCode']);
    Route::post('editProfile', [EndUserController::class, 'editProfile']);
    Route::get('orderHistory', [EndUserController::class, 'orderHistory']);
    Route::post('initiate-return', [EndUserController::class, 'initiateReturn']);
    Route::post('resetpassword', [EndUserController::class, 'resetPassword']);
    Route::get('searchItems', [EndUserController::class, 'searchItems']);
    Route::post('confirm_order', [EndUserController::class, 'confirmOrder']);
    Route::post('generate/ticket', [EndUserController::class, 'generateTicket']);
});


// Grocery APIs

Route::group(['prefix' => 'grocery'], function () {
    Route::middleware(['check.api.key'])->group(function () {
        Route::post('/order-post', [GroceryController::class, 'placeOrder']);
        Route::get('/ready-for-collection/{order_key}', [GroceryController::class, 'readyForCollection']);
        Route::post('/label-generate', [GroceryController::class, 'labelGenerate']);
        Route::get('/get-label', [GroceryController::class, 'getLabel']);
    });
});

Route::post('/import-file', function (Request $request){
    $file = $request->file;
    set_time_limit(1200);
    ini_set('memory_limit', '256M');
    ini_set('max_execution_time', 180);
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ZoneImport(), $file);

    return 'Done';
});

Route::post('merchant/return/order',[RetailerController::class,'returnAcceptApi']);
Route::post('add/notes/order',[RetailerController::class,'addNotesToOrder']);


// Postal Code Suggesstions
Route::get('postal_suggesstion',function(Request $request){
    $validator = Validator::make($request->all(), [
        'postal_code' => ['required'],
        'order_type' => ['required', 'exists:shopify_packages,id'],
    ],
        [
            'postal_code.required' => 'The postal code field is required.',
            'order_type.required' => 'The order type field is required.',
            'order_type.exists' => 'The selected order type is invalid.',
        ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
    }

    $ukTimeZone = new CarbonTimeZone('Europe/London');
    $currentDateTime = Carbon::now($ukTimeZone);
    $currentDay = strtolower($currentDateTime->englishDayOfWeek);

    $query = $request->postal_code;
    $normalizedQuery = strtoupper(str_replace(' ', '', $query));

    $zones = PostalCode::whereRaw("REPLACE(postal, ' ', '') LIKE ?", ['%' . $normalizedQuery . '%'])->pluck('postal');
    
    if (!empty($zones) && count($zones) > 0) {
        $shippingPackage = ShopifyPackage::with('businessHours')->where('id', $request->order_type)->where('status', 1)->first();
        $packageBusinessHours = collect($shippingPackage->businessHours);
        $businessHours = $packageBusinessHours->firstWhere('day', $currentDay);

        if (!empty($businessHours) && $businessHours['open_time'] && $businessHours['close_time']) {
            $openTime = Carbon::parse($businessHours['open_time'], $ukTimeZone);
            $closeTime = Carbon::parse($businessHours['close_time'], $ukTimeZone);

            if ($currentDateTime->between($openTime, $closeTime)) {
                return response()->json([
                    'message' => 'Success',
                    'postal_codes' => $zones,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'We do not operate at this time. Our available time is ' . $businessHours['open_time'] . ' - ' . $businessHours['close_time'],
                    'postal_codes' => $zones,
                    'status' => 403,
                ], 403);
            }
        }else{
            return response()->json([
                'message' => 'We do not operate at this time.',
                'postal_codes' => $zones,
                'status' => 403,
            ], 403);
        }
    }

    return response()->json([
        'message' => 'Currently, we do not serve in this area',
        'postal_codes' => [],
        'status' => 403,
    ], 403);

});
// Postal Code Suggesstions
