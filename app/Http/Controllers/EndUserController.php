<?php

namespace App\Http\Controllers;

use App\Models\ReturnOrder;
use Throwable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Zone;
use App\Models\Items;
use App\Models\Orders;
use App\Models\Discount;
use App\Models\EndUsers;
use App\Models\Retailer;
use App\Models\OrderType;
use Illuminate\Support\Str;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Helpers\OptimizeTrait;
use App\Mail\GenerateTicket;
use App\Mail\OrderConfirmation;
use App\Models\Ticket;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Predis\Command\Redis\AUTH as RedisAUTH;
use App\Helpers\Enduser\PushNotification;
use App\Models\Notification;
use App\Models\OrderPayment;

class EndUserController extends Controller
{
    use OptimizeTrait;
    /* *************************** */
    /* *************************** */
    /* Registering the End User */
    /* *************************** */
    /* *************************** */
    public function register(Request $request)
    { /* Request Validation */
        $validator = Validator::make($request->toArray(), [
            "firstname" => "required",
            "lastname" => "required",
            "number" => "required|unique:end_users,number",
            "password" => "required|min:6",
            "email" => "required|unique:end_users",
            "location_str" => 'required',
            "location_cod" => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()
            ], 422);
        }
        $end_user = EndUsers::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request['email'],
            'number' => $request['number'],
            'password' => Hash::make($request['password']),
            'location_str' => $request['location_str'],
            'location_cod' => $request['location_cod'],
            "code" => $request['code'],
            'login_with' => 'phonenumber'
        ]);
        $token = $end_user->createToken($end_user->number);
        $token = $token->plainTextToken; // Extracting Token
        return response()->json(["status" => 200, "message" => "Registration completed Successfully.", "user" => $end_user, "token" => $token,], 200);
    }

    /* *************************** */
    /* *************************** */
    /* **** End User Login Api**** */
    /* *************************** */
    /* *************************** */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422], 422);
        }
        $user = EndUsers::where('number', $request->number)->first();
        if (auth()->guard('end_user')->attempt(['number' => $request->number, 'password' => $request->password])) {
            $user->device_token = $request->device_token;
            $user->save();
            $token = $user->createToken($user->number);
            $token = $token->plainTextToken; // Extracting Token
            Auth::guard('end_user')->login($user);
            return response()->json(["status" => 200, "message" => "Successfully Logged in.", "user" => $user, "token" => $token], 200);
        } else {
            return response()->json(['status' => 403, 'message' => "Wrong credentails. Please try again."], 403);
        }
        return response()->json(['message' => "You don't have any account registered with this phone number. Please signup to continue", 'status' => 404], 404);
    }

    public function checkAuth()
    {
        return response()->json(['status' => 200, 'user' => Auth::user()]);
    }

    /* ****************** */
    /* Check for Phone Number*/
    /* ****************** */
    public function numberVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "number" => "required|unique:end_users,number|numeric",
            "email" => "required|unique:end_users",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()
            ], 422);
        } else {
            return response()->json(['type' => 'New EndUser', 'status' => 200], 200);
        }
    }
    /* ************************ */
    /* End User- Login with FB */
    /* ************************ */
    public function loginWithFacebook(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            "location_str" => 'required',
            "location_cod" => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()
            ], 422);
        }
        $user = EndUsers::where('number', $request->number)->first();
        $email = EndUsers::where('email', $request->email)->first();
        if (isset($user) || isset($email)) {
            $token = $user->createToken($user->number ?? $user->email);
            $token = $token->plainTextToken;
            Auth::guard('end_user')->login($user);
            return response()->json(["status" => 200, "message" => "Successfully Logged in.", "token" => $token, "user" => $user], 200);
        } else {
            EndUsers::create([
                'firstname' => $request->firstname ?? null,
                'lastname' => $request->lastname ?? null,
                'number' => $request->number ?? null,
                'email' => $request->email ?? $request->firstname . '@gmail.com',
                'password' => Hash::make($request->number ?? $request->email),
                "login_with" => "facebook"
            ]);
            $user = EndUsers::where('number', $request->number)->first();
            $token = $user->createToken($user->number ?? $user->email)->plainTextToken;
            Auth::guard('end_user')->login($user);
            return response()->json(["status" => 200, "message" => "Registered & Sign In", "token" => $token, "user" => $user], 200);
        }
    }

    /* ************************* */
    /*End User Login with Google */
    /* ************************* */
    public function loginWithGoogle(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            "location_str" => 'required',
            'email' => 'required',
            "location_cod" => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()
            ], 422);
        }

        $user = EndUsers::where('email', $request->email)->first();

        if (isset($user)) {
            $token = $user->createToken($user->number ?? $user->email);
            $token = $token->plainTextToken;
            Auth::guard('end_user')->login($user);
            return response()->json(["status" => 200, "message" => "Successfully Logged in.", "token" => $token, "user" => $user], 200);
        } else {
            EndUsers::create([
                'firstname' => $request->firstname ?? null,
                'lastname' => $request->lastname ?? null,
                'number' => $request->number ?? null,
                'email' => $request->email ?? $request->firstname . '@gmail.com',
                'password' => Hash::make($request->number ?? $request->email),
                "login_with" => "google"
            ]);
            $user = EndUsers::where('email', $request->email)->first();
            $token = $user->createToken($user->number ?? $request->email);
            $token = $token->plainTextToken;
            Auth::guard('end_user')->login($user);
            return response()->json(["status" => 200, "message" => "Registered & Sign In", "token" => $token, "user" => $user], 200);
        }
    }


    public function loginWithApplePay(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            "location_str" => 'required',
            "location_cod" => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()
            ], 422);
        }

        $user = EndUsers::where('number', $request->number)->first();
        if (isset($user)) {
            $token = $user->createToken($user->number ?? $user->email);
            $token = $token->plainTextToken;
            Auth::guard('end_user')->login($user);
            return response()->json(["status" => 200, "message" => "Successfully Logged in.", "token" => $token, "user" => $user], 200);
        } else {
            EndUsers::create([
                'firstname' => $request->firstname ?? null,
                'lastname' => $request->lastname ?? null,
                'number' => $request->number ?? null,
                'email' => $request->email ?? $request->firstname . '@gmail.com',
                'password' => Hash::make($request->number),
                "login_with" => "applepay"
            ]);
            $user = EndUsers::where('number', $request->number)->first();
            $token = $user->createToken($user->number);
            $token = $token->plainTextToken;
            Auth::guard('end_user')->login($user);
            return response()->json(["status" => 200, "message" => "Registered & Sign In", "token" => $token, "user" => $user], 200);
        }
    }



    public function placeOrder(Request $request)
    {
        info('request srar');
        info($request);
        /* Request Validation  */
        $validator = Validator::make($request->all(), [
            'enduser_address' => 'required',
            'order_type' => 'required',
            'pickupdate' => 'required',
            'pickuptime' => 'required',
            'deliverydate' => 'required',
            'deliverytime' => 'required',
            'item_weight' => ['required', 'numeric'],
            // 'item_length' => ['required', 'numeric'],
            // 'item_width' => ['required', 'numeric'],
            // 'item_height' => ['required', 'numeric'],
            'dropoff_postal' => ['required'],
            'dropoff_city' => ['required'],
            'dropoff_country' => ['required'],
            'pickup_postal_code' => ['required'],
            'pickup_city' => ['required'],
            'pickup_country' => ['required'],
            'dropoff_address' => ['required'],
            'number_of_items' => ['required'],
            'delivery_type' => ['required']

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $retailer = User::with('retailer')->where('email', env('ENDUP_RETAILER'))->first();
        if (is_null($retailer)) {
            return response()->json(['message' => 'Retailer not found'], 400);
        }

        $itemsInputJSON = $request->input('items', '[]');
        $itemsInput = json_decode($itemsInputJSON);
        info('Req');
        info($itemsInput);
        $items = [];
        $totalVolumetricWeight = 0;
        if (is_array($itemsInput)) {
        foreach ($itemsInput as $itemInput) {
            if (isset($itemInput->length) && isset($itemInput->width) && isset($itemInput->height)) {
                $length = $itemInput->length;
                $width = $itemInput->width;
                $height = $itemInput->height;
                $item = [
                    'length' => $length,
                    'width' => $width,
                    'height' => $height,
                    'measuring_unit' => $request['measuring_unit'], 
                ];
                $item['volumetric_weight'] = $length * $width * $height / 5000;
                $item['dimension'] = $width . " x " . $height;
                $item['weight'] = $request['item_weight']; 
                $items[] = $item;
                $totalVolumetricWeight += $item['volumetric_weight'];
            }
        }
    }
        info('the converted');
        info($items);

        $retailer = $retailer->retailer;
        // $itemsinput = [];

        // $itemsinput['weight'] = $request['item_weight'];
        // $itemsinput['length'] = $request['item_length'];
        // $itemsinput['width'] = $request['item_width'];
        // $itemsinput['height'] = $request['item_height'];
        // $itemsinput['measuring_unit'] = $request['measuring_unit'];
        // $itemsinput['number_of_items'] = $request['number_of_items'];

        // $request['item'] = $itemsinput;
        $request['order_key'] = str_replace('-', '', uuid_create());
        $request['retailer_id'] = $retailer->id;

        $zone = Zone::whereHas('postalcodes', function ($q) use ($request) {
            $q->where('postal', $request->dropoff_postal);
        })->first();
        $request['zone_id'] = (!is_null($zone)) ? $zone->id : null;
        // return $zone;

        $order_number = 'EU-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $enduserDetails = EndUsers::where('id', Auth::user()->id)->first(); // get authenticated user record
        $order = Orders::create([
            'retailer_id' => $retailer->id,
            'order_number' => $order_number,
            'payment_type' => 'card',
            'order_type' => $request['order_type'],
            'enduser_address' => $request['enduser_address'],
            'order_key' => $request['order_key'],
            'shipping_notes' => $request['shipping_notes'] ?? "",
            'enduser_ordernotes' => $request['shipping_notes'] ?? '',
            'enduser_id' => Auth::user()->id,
            'enduser_name' => $enduserDetails['firstname'] . " " . $enduserDetails['lastname'],
            'enduser_email' => $enduserDetails['email'] ?? "",
            'enduser_mobile' => $enduserDetails->number,
            'order_type_id' => $request['order_type_id'],
            'pickuptime' => $request['pickuptime'],
            'pickupdate' => $request['pickupdate'],
            'deliverytime' => $request['deliverytime'],
            'deliverydate' => $request['deliverydate'],
            'dropoff_country' => $request->dropoff_country,
            'dropoff_city' => $request->dropoff_city,
            'dropoff_postal' => $request->dropoff_postal,
            'pickup_coordinates' => $request['pickup_coordinates'],
            'dropoff_coordinates' => $request['dropoff_coordinates'],
            'pickup_postal_code' => $request->pickup_postal_code,
            'pickup_city' => $request->pickup_city,
            'pickup_country' => $request->pickup_country,
            'dropoff_address' => $request->dropoff_address,
            'number_of_items' => $request['number_of_items'],
            'is_accepted' => 0,
            'zone_id' =>  $request['zone_id'],
            'pickup_house_number' => $request->pickup_house_number,
            'pickup_street_address' => $request->pickup_street_address,
            'dropoff_street_address' => $request->dropoff_street_address,
            'dropoff_house_number' => $request->dropoff_house_number,
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

        // $items = [$request['item']];
        // foreach ($items as $key => $i) {
        //     $items[$key]['volumetric_weight'] = $i['length'] * $i['width'] * $i['height'] / 5000;
        //     $items[$key]['order_id'] = $order->id;
        //     $items[$key]['dimension'] = $i['width'] . " x " . $i['height'];
        //     $items[$key]['height'] = $i['height'];
        //     $items[$key]['width'] = $i['width'];
        //     $items[$key]['length'] = $i['length'];
        //     $items[$key]['measuring_unit'] = $i['measuring_unit'];
        //     // $items[$key]['number_of_items'] = $i['number_of_items'];
        //     $items[$key]['created_at'] = now();
        //     $items[$key]['updated_at'] = now();
        // }
        $order->items()->createMany($items);
        $checkRate = [
            'volumetric_weight' => number_format($totalVolumetricWeight, 2),
        ];
        // $checkRate = array(
        //     'volumetric_weight' => number_format(array_sum(array_column($items, 'length')) * array_sum(array_column($items, 'width')) * array_sum(array_column($items, 'height')) / 5000, 2),
        // );
        $rates = $this->calculateShipping($checkRate, $retailer);
        $order->shipping_charges = $rates->price;
        if ($request->has('coupon')) {
            $discount = Discount::where('code', $request->coupon)->where('status', 1)
                ->first();
            if (isset($discount->id) && (!is_null($discount->id))) {
                $dis_count = Orders::where('enduser_id', Auth::user()->id)->where('discount_id', $discount->id)->latest()->first();
                if ($discount->single_time == 1) {
                    if ((!is_null($discount)) && isset($discount) && is_null($dis_count)) {
                        $discounted_price = $rates->price / $discount->value;
                        $discount_price = $rates->price - $discounted_price;
                        Orders::where('order_number', $order_number)
                            ->where('enduser_id', Auth::user()->id)
                            ->update([
                                'discounted_price' => round($discount_price),
                                'discount_id' => $discount->id
                            ]);
                    }
                } else {
                    if ((!is_null($discount)) && isset($discount)) {
                        $discounted_price = $rates->price / $discount->value;
                        $discount_price = $rates->price - $discounted_price;
                        Orders::where('order_number', $order_number)
                            ->where('enduser_id', Auth::user()->id)
                            ->update([
                                'discounted_price' => round($discount_price),
                                'discount_id' => $discount->id
                            ]);
                    }
                }
            }
        }
        $endusername = $enduserDetails['firstname'] . " " . $enduserDetails['lastname'];
        $enduseremail = $enduserDetails['email'];
        $endusernumber = $enduserDetails['number'] ?? 'Phone number not provided';
        $pickuplocation = $request->pickup_city . '' . $request->pickup_postal_code . '' . $request->pickup_country;
        $pickupdatetime = $request['pickupdate'] . ' ' . $request['pickuptime'];
        $dropofflocation = $request->dropoff_city . " " . $request->dropoff_postal . ' ' . $request->dropoff_country;
        $dropoffdatetime = $request['deliverydate'] . ' ' . $request['deliverytime'];
        $shippingnotes = $request['shipping_notes'] ?? 'NO SHIPPING NOTES FROM ENDUSER';
        $orderweight = $request['item_weight'];
        $shippingcharges = $rates->price;
        $discountprice = round($discounted_price ?? 0) ?? 0;
        $totalprice = isset($discount_price) ? round($discount_price) : $discount_price ?? round($rates->price);
        Mail::to($enduseremail)->send(
            new OrderConfirmation(
                $endusername,
                $enduseremail,
                $endusernumber,
                $pickuplocation,
                $pickupdatetime,
                $dropofflocation,
                $dropoffdatetime,
                $shippingnotes,
                $orderweight,
                $shippingcharges,
                $discountprice,
                $totalprice,
                $order_number
            )
        );

        $order->save();
        $updatechargersorder = Orders::with('items', 'orderType')->find($order->id);
        $updatechargersorder->save();
        if ($request->bearerToken()) {
            return response()->json(['order' => $updatechargersorder, 'Discount (%)' => $discount->value ?? 0, 'discounted_price' => $discounted_price ?? 0, 'message' => 'Order has been placed', 'status' => 200], 200);
        }
        return response()->json(
            [
                'message' => 'something went wrong',
                'status' => 201
            ],
            201
        );
    }



    public function checkDiscountCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }
        $now = now();
        $discount = Discount::where('code', $request->coupon)->where('status', 1)
            ->first();
            if (isset($discount) && (!is_null($discount))) {

                if($discount->single_time == 1)
                {
                $couponAlreadyRedeemed = Orders::where('enduser_id', Auth::id())
                ->where('discount_id', $discount->id)
                ->exists();

                if ($couponAlreadyRedeemed) {
                return response()->json([
                'message' => 'Invalid discount code',
                'status' => 201
                ], 201);
                }

                }

            if ($discount->date_start_expiry && $discount->date_start_expiry > $now) {
                return response()->json([
                    'message' => 'Coupon not yet valid',
                    'status' => 201
                ], 201);
            }

            if ($discount->date_end_expiry && $discount->date_end_expiry < $now) {
                return response()->json([
                    'message' => 'Coupon has expired',
                    'status' => 201
                ], 201);
            }

            return response()->json(
                [
                    'message' => 'Verified',
                    'status' => 200
                ],
                200
            );
        }
        return response()->json(
            [
                'message' => 'Invalid discount code',
                'status' => 201
            ],
            201
        );
    }
    public function editProfile(Request $request)
    {


        $user = EndUsers::where('id', Auth::user()->id)->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request['email']
        ]);

        if (!empty($request->profile_picture)) {
            $file = $request->file('profile_picture');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('uploads/'), $filename);
            EndUsers::where('id', Auth::user()->id)->update([
                'profile_picture' => url('uploads/' . $filename)
            ]);
        }
        $user_data = EndUsers::where('id', Auth::user()->id)->first();
        if (isset($user) && (!is_null($user))) {
            return response()->json(['message' => 'profile updated successfully', 'user' => $user_data, 'status' => 200], 200);
        }
        return response()->json(['message' => 'please try again', 'status' => 201], 201);
    }

    public function orderHistory()
    {
        $orders = Orders::with('items', 'orderType')->where([['enduser_id', Auth::user()->id], ['is_accepted', 1]])->latest()->first();
        $monthly_orders = Orders::with('items', 'orderType')->where([['enduser_id', Auth::user()->id], ['is_accepted', 1]])->whereMonth('created_at', date('m'))->get();
        $yearly_orders = Orders::with('items', 'orderType')->where([['enduser_id', Auth::user()->id], ['is_accepted', 1]])->whereYear('created_at', date('Y'))->get();
        $today_orders = Orders::with('items', 'orderType')->where([['enduser_id', Auth::user()->id], ['is_accepted', 1]])->whereDate('created_at', Carbon::today())->get();
        $Orders = [
            'daily_orders' => $today_orders,
            'monthly_orders' => $monthly_orders,
            'yearly_orders' => $yearly_orders
        ];
        if (isset($orders) && (!is_null($orders))) {
            return response()->json(['Order Details' => $Orders, 'status' => 200], 200);
        }
        return response()->json(['message' => 'No orders found', 'status' => 201], 201);
    }


    public function getEndUserInfo(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'user' => Auth::user(),
                'status' => 200
            ], 200);
        }
    }


    public function validateNumberForForgotPassword(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            "number" => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()
            ], 422);
        }

        /* Check user Exist via this email */
        $user = EndUsers::where('number', $request['number'])->first();
        if (isset($user) && (!is_null($user))) {
            // $email_verification_code =    str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            // Mail::to('saadabdullah314405@gmail.com')->send(new ForgotPassword($email_verification_code));
            // EndUsers::where('number', $request->number)->update([
            //     'email_verification_code' => $email_verification_code
            // ]);
            return response()->json([
                'message' => 'Otp sent successfully.',
                'status' => 200
            ], 200);
        } else {
            return response()->json(['message' => 'User not found', 'status' => 409], 409);
        }
    }

    public function changePassword(Request $request)
    {
        /* Request Validation */
        $validator = Validator::make($request->toArray(), [
            "password" => "required|min:6",
            "number" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()
            ], 422);
        }
        $end_user = EndUsers::where('number', $request->number)->first();

        if (isset($end_user) && (!is_null($end_user))) {
            $end_user->update([
                'password' => Hash::make($request->password)
            ]);
            return response([
                'message' => 'Password changed successfully.',
                'status' => 200
            ], 200);
        }
        return response()->json([
            'message' => 'Please try again',
            'status' => 201
        ], 201);
    }

    public function initiateReturn(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            "order_id" => "required",
            "reason" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()
            ], 422);
        }
        $return = new ReturnOrder();
        $return->order_id = $request->order_id;
        $return->reason = $request->reason;
        $return->save();

        $order = Orders::find($request->order_id);
        $order->return = 1;
        $order->save();

        return response()->json(['message' => 'Return initiated', 'status' => 200], 200);
    }


    public function resetPassword(Request $request)
    {

        $validator = Validator::make($request->toArray(), [
            "password" => "required|min:6",
            "new_password" => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()
            ], 422);
        }


        $user = EndUsers::where('id', Auth::user()->id)->first();
        if (isset($user) && (!is_null($user))) {
            if (Hash::check(request('password'), $user->password)) {
                EndUsers::where('id', Auth::user()->id)->update([
                    'password' => Hash::make($request->new_password)
                ]);
                return response()->json([
                    'message' => 'Password changed successfully',
                    'status' => 200
                ], 200);
            }
            return response()->json([
                'message' => 'Incorrect password',
                'status' => 401
            ], 401);
        }
        return response()->json([
            'message' => 'User not found',
            'status' => 404
        ], 404);
    }

    public function searchItems(Request $request)
    {
        if ($request->search_request == '') {
            return response()->json(
                ['data' => [], 'status' => 200],
                200
            );
        }
        $order_number = Orders::with('orderType')->whereHas('orderType', function (Builder $query) use ($request) {
            $query->where('name', 'like', "%{$request->search_request}%")->where('enduser_id', Auth::user()->id);
        })
            ->Orwhere('order_number', 'LIKE', "%{$request->search_request}%")
            ->where('enduser_id', Auth::user()->id)->get();




        if (isset($order_number) && (!is_null(($order_number)))) {

            return response()->json([
                'message' => 'Order details',
                'data' => $order_number,
                'status' => 200
            ], 200);
        }
        return response()->json([
            'message' => 'No orders found',
            'status' => 404,
            'data' => []
        ], 404);
    }
    public function generateTicket(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            'subject' => 'required',
            'message' => 'required',
            'category' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => array_values($validator->errors()->toArray())[0][0]
            ], 422);
        }

        $user = Auth::user();

        $msg = $request->message;
        $user_name = $user->firstname . " " . $user->lastname;
        $user_phone_number = $user->number;
        $mail_subject = $request->subject;
        Mail::to("saadabdullah314405@gmail.com")->send(new GenerateTicket($msg, $mail_subject, $user_name, $user_phone_number));
        $user->tickets()->create(
            $request->all()
        );
        return response()->json(['message' => 'Ticket generated', 'status' => 200], 200);
    }

    public function confirmOrder(Request $request)
    {

        $order_id = $request->order_id;
        $order_exist =     Orders::where('id', $order_id)->first();
        if (isset($order_exist)  && (!is_null($order_exist) && ($order_exist->is_accepted  == 0))) {
            Orders::where('enduser_id', Auth::user()->id)->where('id', $order_id)
                ->update([
                    'is_accepted' => 1
                ]);
                // Save Payment Information
                OrderPayment::create([ 
                    'end_user_id' => Auth::id(),
                    'order_id' => $order_id,
                    'stripe_session_id' => $request->session_id,
                    'order_amount' => $request->sub_total,
                ]);
                // Save Payment Information
        
            return response()->json([
                'message' => 'Your order has been confirmed successfully.', 'status' => 200
            ], 200);
        }
        return response()->json(['message' => 'No order found', 'status' => 404], 404);
    }

    public function test(){

        $userId = 2;

        $title = 'New Order Placed';
        $message = 'Order has been placed'; 
        $url = 'https://www.enduptech.com';

        //  Create Notification
        $notification =  Notification::create([
            'end_user_id' => $userId,
            'type' => 'Test',
            'data' => 'order=placed',
            'created_at' => now(),
            'title' => $title,
            'message' => $message,
            'url'=> $url,
            'updated_at' => now(),
        ]);
        //  Create Notification

        // Send FCM Notification to user
        $test  = $this->sendPushNotification($userId,$title,$message,$url);
        // Send FCM Notification to user

        return $test;
    }
}
