<?php

use App\Events\TestEvent;
use App\Http\Controllers\TicketController;
use App\Models\Orders;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTypeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\EndUserController;
use App\Http\Controllers\RetailerDocumentController;
use App\Http\Controllers\MessageController;
use BeyondCode\LaravelWebSockets\Facades\WebSockets;
use Pusher\Pusher;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('index');

// \Illuminate\Support\Facades\Auth::routes();
Auth::routes([
    'register' => false,
    // Registration Routes...
    'reset' => false,
    // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['auth'])->name('home');

Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::get('/riders', [App\Http\Controllers\HomeController::class, 'riders'])->name('riders');
    Route::get('/riders/add', [\App\Http\Controllers\HomeController::class, 'addRider'])->name('add.rider');
    Route::post('/riders/insert', [\App\Http\Controllers\HomeController::class, 'insertRider'])->name('insert.rider');
    Route::get('/riders/edit/{id}', [\App\Http\Controllers\HomeController::class, 'editRider'])->name('edit.rider');
    Route::post('/riders/update/{id}', [\App\Http\Controllers\HomeController::class, 'updateRider'])->name('update.rider');


    Route::get('/retailers', [App\Http\Controllers\HomeController::class, 'retailers'])->name('retailers');
    Route::get('/retailers/add', [\App\Http\Controllers\HomeController::class, 'addRetailer'])->name('add.retailer');
    Route::post('/retailers/insert', [\App\Http\Controllers\HomeController::class, 'insertRetailer'])->name('insert.retailer');
    Route::get('/retailers/edit/{id}', [\App\Http\Controllers\HomeController::class, 'editRetailer'])->name('edit.retailer');
    Route::post('/retailers/update/{id}', [\App\Http\Controllers\HomeController::class, 'updateRetailer'])->name('update.retailer');
    Route::get('/Auth/Logs/{id?}', [\App\Http\Controllers\HomeController::class, 'AuthLogs'])->name('auth.logs');

    Route::get('/retailer-prices', [App\Http\Controllers\HomeController::class, 'retailerPrices'])->name('retailer.prices');
    Route::get('/retailer-prices/add', [\App\Http\Controllers\HomeController::class, 'addRetailerPrice'])->name('add.retailer.prices');
    Route::post('/retailer-prices/insert', [\App\Http\Controllers\HomeController::class, 'insertRetailerPrice'])->name('insert.retailer.price');
    Route::get('/retailer-prices/edit/{id}', [\App\Http\Controllers\HomeController::class, 'editRetailerPrice'])->name('edit.retailer.price');
    Route::post('/retailer-prices/update/{id}', [\App\Http\Controllers\HomeController::class, 'updateRetailerPrice'])->name('update.retailer.price');
    Route::get('/retailer/promotion/{id}', [\App\Http\Controllers\HomeController::class, 'retailerPromotion'])->name('retailer.promotion');

    Route::get('/currencies', [App\Http\Controllers\HomeController::class, 'currencies'])->name('currencies');
    Route::get('/currencies/add', [\App\Http\Controllers\HomeController::class, 'addCurrency'])->name('add.currency');
    Route::post('/currencies/insert', [\App\Http\Controllers\HomeController::class, 'insertCurrency'])->name('insert.currency');
    Route::get('/currencies/edit/{id}', [\App\Http\Controllers\HomeController::class, 'editCurrency'])->name('edit.currency');
    Route::post('/currencies/update/{id}', [\App\Http\Controllers\HomeController::class, 'updateCurrency'])->name('update.currency');

    Route::get('/discounts', [App\Http\Controllers\HomeController::class, 'discounts'])->name('discounts');
    Route::get('/discounts/add', [\App\Http\Controllers\HomeController::class, 'addDiscount'])->name('add.discount');
    Route::post('/discounts/insert', [\App\Http\Controllers\HomeController::class, 'insertDiscount'])->name('insert.discount');
    Route::get('/discount/edit/{id}', [\App\Http\Controllers\HomeController::class, 'editDiscount'])->name('edit.discount');
    Route::post('/discount/update/{id}', [\App\Http\Controllers\HomeController::class, 'updateDiscount'])->name('update.discount');


    Route::get('/prices', [App\Http\Controllers\HomeController::class, 'indexPrices'])->name('prices');
    Route::get('/prices/add', [\App\Http\Controllers\HomeController::class, 'addPrice'])->name('add.price');
    Route::post('/prices/insert', [\App\Http\Controllers\HomeController::class, 'insertPrice'])->name('insert.price');
    Route::get('/discount/edit/{id}', [\App\Http\Controllers\HomeController::class, 'editDiscount'])->name('edit.discount');
    Route::post('/discount/update/{id}', [\App\Http\Controllers\HomeController::class, 'updateDiscount'])->name('update.discount');


    Route::get('/blocks', [App\Http\Controllers\HomeController::class, 'indexBlocks'])->name('blocks');
    Route::get('/collections', [App\Http\Controllers\HomeController::class, 'indexCollections'])->name('collections');
    Route::get('/return-blocks', [App\Http\Controllers\HomeController::class, 'indexReturnBlocks'])->name('return-blocks');

    Route::get('charges', [HomeController::class, 'chargesList'])->name('charges');
    Route::get('charges/add', [HomeController::class, 'addCharges'])->name('add.charges');
    Route::post('charges/insert', [HomeController::class, 'insertCharges'])->name('insert.charges');


    Route::get('charges/items/{id}', [HomeController::class, 'chargesItems'])->name('charges.items');
    Route::get('charges/items/add/{id}', [HomeController::class, 'addChargesItem'])->name('add.charges.items');
    Route::post('charges/items/insert/{id}', [HomeController::class, 'insertChargesItem'])->name('insert.charges.items');
    Route::get('/charges/edit/items/{id}', [HomeController::class, 'editChargesItem'])->name('edit.charges.items');
    Route::post('/charges/update/items/{id}', [HomeController::class, 'updateChargesItem'])->name('update.charges.items');
    // Route::get('create/order', [RetailerController::class, 'manualOrder'])->name('manual.order');


    Route::get('label/print/{id}', [HomeController::class, 'generateLabelPrint'])->name('generate.label');


    Route::get('postal-codes', [HomeController::class, 'postalCodes'])->name('postal-codes');
    Route::get('postal-codes/add', [HomeController::class, 'addPostalCode'])->name('postal-codes.add');
    Route::post('postal-codes/insert', [HomeController::class, 'insertPostalCode'])->name('postal-codes.insert');
    Route::get('postal-codes/edit/{id}', [HomeController::class, 'editPostalCode'])->name('postal-codes.edit');
    Route::post('postal-codes/update/{id}', [HomeController::class, 'updatePostalCode'])->name('postal-codes.update');
    Route::post('import/postals/{zone_id}', [HomeController::class, 'importPostalToZones'])->name('import-postal-code');

    Route::get('zones', [HomeController::class, 'zones'])->name('zones');
    Route::get('zones/add', [HomeController::class, 'addZone'])->name('zones.add');
    Route::post('zones/insert', [HomeController::class, 'insertZone'])->name('zones.insert');
    Route::get('zones/edit/{id}', [HomeController::class, 'editZone'])->name('zones.edit');
    Route::post('zones/update/{id}', [HomeController::class, 'updateZone'])->name('zones.update');
    Route::get('zones/delete/{id}', [HomeController::class, 'deleteZone'])->name('zones.delete');


    Route::get('warehouse', [HomeController::class, 'warehouses'])->name('warehouse');
    Route::get('warehouse/add', [HomeController::class, 'addWarehouse'])->name('warehouse.add');
    Route::post('warehouse/insert', [HomeController::class, 'insertWarehouse'])->name('warehouse.insert');
    Route::get('warehouse/edit/{id}', [HomeController::class, 'editWarehouse'])->name('warehouse.edit');
    Route::post('warehouse/update/{id}', [HomeController::class, 'updateWarehouse'])->name('warehouse.update');

    Route::get('create/collection', [HomeController::class, 'createCollection'])->name('create.collection');
    Route::get('create/collection/return', [HomeController::class, 'createCollectionReturn'])->name('create.collection.return');
    Route::get('create/return-block', [HomeController::class, 'createReturnBlock'])->name('create.return.block');
    Route::post('return/verify_distance', [HomeController::class, 'returnBlockVerifyDistance']);
    Route::post('collector/verify_distance', [HomeController::class, 'collectorVerifyDistance']);
    Route::post('post/collector', [HomeController::class, 'postCollector'])->name('post.collector');
    Route::post('post/collector/return', [HomeController::class, 'postCollector'])->name('post.collector.return');
    Route::post('post/return-block', [HomeController::class, 'postReturnBlock'])->name('post.return.block');

    Route::post('change/rider', [HomeController::class, 'changeRider']);
    Route::post('change/collector', [HomeController::class, 'changeCollector']);


    Route::get('check/location', [HomeController::class, 'viewOnMap'])->name('viewOnMap');



    Route::get('type', [OrderTypeController::class, 'index'])->name('order_types');
    Route::get("ordertype/add", [OrderTypeController::class, 'add'])->name('ordertype.add');
    Route::get('ordertype/edit/{id}', [OrderTypeController::class, 'edit'])->name("ordertype.edit");
    Route::post('ordertype/update/{id}', [OrderTypeController::class, 'update'])->name("ordertype.update");
    Route::post('addordertype', [OrderTypeController::class, 'storePkgInfo'])->name("add.new.ordertype");

    // Business Hours For the platform
    Route::get('business/hours', [HomeController::class, 'businessHoursView'])->name('index.businessHours');
    Route::post('business/hours', [HomeController::class, 'addBusinessHours'])->name('add.businessHours');
    // Business Hours For the platform

    // Shopify Packages
    Route::get('shopify/packages', [HomeController::class, 'shopifyPackage'])->name('index.shopify.package');
    Route::get('add/shopify/package', [HomeController::class, 'formShopifyPackage'])->name('form.shopify.package');
    Route::post('add/shopify/package', [HomeController::class, 'addShopifyPackage'])->name('add.shopify.package');

    Route::get('edit/shopify/package/{id}', [HomeController::class, 'editShopifyPackage'])->name('edit.shopify.package');
    Route::post('update/shopify/package/{id}', [HomeController::class, 'updateShopifyPackage'])->name('update.shopify.package');
    // Shopify Packages

    Route::get('retailer/documents/{id}', [RetailerDocumentController::class, 'index'])->name('retailer.documents');
    Route::get('retailer/delete/documents/{id}', [RetailerDocumentController::class, 'delete'])->name('retailer.delete.document');
    Route::post('retailer/documents/add/{id}', [RetailerDocumentController::class, 'uploadDoc'])->name('retailer.documents.add');
});
Route::get('check/location', [HomeController::class, 'viewOnMap'])->name('viewOnMap');
Route::middleware(['auth'])->group(function () {
    Route::get('finances', [HomeController::class, 'finances'])->name('finances');
    Route::get('create/order', [RetailerController::class, 'manualOrder'])->name('manual.order');
    Route::post('manual/order/post', [RetailerController::class, 'orderAPI'])->name('manual.order.post');
    Route::get('edit/order/{id}', [RetailerController::class, 'editOrder'])->name('edit.manual.order');
    Route::post('update/order/{id}', [RetailerController::class, 'updateOrder'])->name('update.manual.order');

    Route::get('orders', [\App\Http\Controllers\HomeController::class, 'orders'])->name('orders');
    Route::get('assign/rider/', [\App\Http\Controllers\HomeController::class, 'assignToRider'])->name('assign.rider');
    Route::get('/orders/items/{id}', [\App\Http\Controllers\HomeController::class, 'orderItems'])->name('order.items');
    Route::get('list/riders', [\App\Http\Controllers\HomeController::class, 'riderList'])->name('rider.list');

    Route::post('assign/order/rider', [\App\Http\Controllers\HomeController::class, 'assignOrder'])->name('assign.order');

    Route::get('rider/orders/{id}/{date}', [\App\Http\Controllers\HomeController::class, 'riderOrders'])->name('single.rider');


    Route::get('promotion', [\App\Http\Controllers\RetailerController::class, 'promotionsIndex'])->name('promotion.index');
    Route::post('promotion/update/{id}', [\App\Http\Controllers\RetailerController::class, 'promotionUpdate'])->name('promotion.update');

    Route::get('order/json/{id}', [\App\Http\Controllers\HomeController::class, 'jsonOrder']);

    Route::post('verify/distance', [HomeController::class, 'verifyDistance']);
    Route::get('collection/ready/{id}', [HomeController::class, 'collectionReady'])->name('ready.collection');


    Route::get('type', [OrderTypeController::class, 'index'])->name('order_types');
    Route::get("ordertype/add", [OrderTypeController::class, 'add'])->name('ordertype.add');
    Route::get('ordertype/edit/{id}', [OrderTypeController::class, 'edit'])->name("ordertype.edit");
    Route::post('ordertype/update/{id}', [OrderTypeController::class, 'update'])->name("ordertype.update");
    Route::post('addordertype', [OrderTypeController::class, 'storePkgInfo'])->name("add.new.ordertype");

    Route::get('type', [OrderTypeController::class, 'index'])->name('order_types');
    Route::get("ordertype/add", [OrderTypeController::class, 'add'])->name('ordertype.add');
    Route::get('ordertype/edit/{id}', [OrderTypeController::class, 'edit'])->name("ordertype.edit");
    Route::post('ordertype/update/{id}', [OrderTypeController::class, 'update'])->name("ordertype.update");
    Route::post('addordertype', [OrderTypeController::class, 'storePkgInfo'])->name("add.new.ordertype");
    Route::get('print/item/label/{code}', [HomeController::class, 'generateItemsLabel'])->name('print.item.label');
    Route::get("/package", [PackageController::class, 'index'])->name("packages");
    Route::get("package/add", [PackageController::class, 'add'])->name('package.add');
    Route::get('package/edit/{id}', [PackageController::class, 'edit'])->name("package.edit");
    Route::post('package/update/{id}', [PackageController::class, 'update'])->name("package.update");
    Route::post('addpackage', [PackageController::class, 'storePkgInfo'])->name("add.new.package");

    Route::post('generate/item/label', [RetailerController::class, 'generateItemLabel'])->name('generate.item.label');

    Route::get('accept-return/{id}', [RetailerController::class, 'acceptReturn'])->name('accept-return');

    Route::get('settings',[RetailerController::class, 'settings'])->name('retailer.settings');
    Route::post('settings/{id}',[RetailerController::class, 'saveSettings'])->name('post.retailer.settings');



    // Route::get('tickets', [TicketController::class, 'index'])->name('tickets');
    // Route::get('/tickets/add-ticket', [TicketController::class, 'add'])->name('tickets.add');
    // Route::post('generate_ticket', [TicketController::class, 'generateTicket'])->name('tickets.post-ticket');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/send', [MessageController::class, 'sendMessage'])->name('messages.send');

    Route::get('new/message', [MessageController::class, 'newMessage'])->name('form.message');
    Route::post('/messages/new', [MessageController::class, 'store'])->name('messages.create');
    Route::post('conversations/{conversation}/end', [MessageController::class, 'endConversation'])->name('conversation.end');
});

Route::get('test/socket', function () {
    event(new TestEvent());
    $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), array('cluster' => env('PUSHER_APP_CLUSTER')));
    // dd($pusher);
    // $pusher->trigger('my-channel', 'my-event', array('message' => 'hello world'));
    // WebSockets::server()->emit('event-name', ['data' => 'value']);

});

Route::post('endup/merchant/order', [\App\Http\Controllers\RetailerController::class, 'orderAPI']);
Route::post('endup/merchant/order/placed', [\App\Http\Controllers\RetailerController::class, 'placeOrder']);

Route::get('track/email/{id}', [EndUserController::class, 'sendNavigateLink']);


Route::get('search/auto-compelete/{query}', function ($q) {

    $data = Orders::select("order_number", "id", 'enduser_name', 'enduser_email')
        ->where('order_number', 'LIKE', '%' . $q . '%')->latest()->limit(5)
        ->get();

    return response()->json($data);
});
Route::get('/search/postal/{query}', function ($q) {

    $data = Zone::select("name", "id")->with(['postalcodes' => function ($s) use ($q) {
        $s->where('postal', 'LIKE', '%' . $q . '%');
    }])->whereHas('postalcodes', function ($s) use ($q) {
        $s->where('postal', 'LIKE', '%' . $q . '%');
    })->latest()->limit(5)->get();

    return response()->json($data);
});

Route::get('export-orders',[HomeController::class, 'exportOrders'])->name('export.orders');

// Route::get('import/postal', function(){
//     return view('import');
// });
// Route::post('/import-file', [HomeController::class, 'importZones'])->name('import.zones.sheet');
