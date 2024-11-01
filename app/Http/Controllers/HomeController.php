<?php

namespace App\Http\Controllers;


use App\Helpers\OptimizeTrait;
use App\Helpers\SecretKey;
use App\Imports\PostalImport;
use App\Imports\ZoneImport;
use App\Models\Block;
use App\Models\BusinessHour;
use App\Models\BusinessType;
use App\Models\Collection;
use App\Models\Currency;
use App\Models\Discount;
use App\Models\DocumentType;
use App\Models\Orders;
use App\Models\Items;
use App\Models\OrderType;
use App\Models\PostalCode;
use App\Models\Price;
use App\Models\Retailer;
use App\Models\RetailerChargesList;
use App\Models\RetailerChargesListItem;
use App\Models\RetailerPrice;
use App\Models\ReturnBlock;
use App\Models\Roles;
use App\Models\Rider;
use App\Models\ShippingTerm;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WorkingDay;
use App\Models\Zone;
use App\Models\RidersLocation;
use App\Models\ShopifyBusinessHour;
use App\Models\ShopifyPackage;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Gd\Commands\PickColorCommand;
use Yajra\DataTables\DataTables;

class HomeController extends Controller
{

    use OptimizeTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = [];
        $last7dates = [];
        $last7date = Carbon::now()->subDays(7);
        $dates = DB::table('orders')->where('created_at', '>=', $last7date)->select(DB::raw('DATE(created_at) as date'))->distinct()->get();
        // dd($dates);
        if (Auth::user()->retailer) {
            $orders = Orders::with('retailer')
                ->where('retailer_id', Auth::user()
                    ->retailer->id)
                ->latest()
                ->limit(6)
                ->get();

            $currentMonth = Orders::whereMonth('created_at', date('m'))
                ->where('retailer_id', Auth::user()->retailer->id)
                ->whereYear('created_at', date('Y'))
                ->where('shipping_charges', '!=', '')
                ->get(['shipping_charges'])
                ->pluck('shipping_charges')
                ->toArray();

            $cur = number_format(array_sum($currentMonth), 2);
            $last7dates = $this->get7DaysDates(7, 'd-m-Y');
            $date = Carbon::now()->subDays(7);

            $ordersCountForGraphPending = DB::table('orders')
                ->where('delivery_status', null)
                ->where('retailer_id', Auth::user()->retailer->id)
                ->where('created_at', '>=', $date)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as pending'))
                ->groupBy('date')->get()->toArray();



            $ordersCountForGraphDelivered = DB::table('orders')
                ->where('delivery_status', 'Delivered')
                ->where('retailer_id', Auth::user()->retailer->id)
                ->where('created_at', '>=', $date)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as delivered'))
                ->groupBy('date')->get()->toArray();
            // dd(Auth::user());

            $datesArr = array_merge_recursive($ordersCountForGraphPending, $ordersCountForGraphDelivered);
            sort($datesArr);

            $zoneOrders = Zone::withCount([
                'orders' => function ($q) {
                    $q->where('retailer_id', Auth::user()->retailer->id);
                }
            ])
                ->active()
                ->get()
                ->toArray();

            if (count($zoneOrders) > 0) {
                $zoneNames = array_column($zoneOrders, 'name');
                $ordersCount = array_column($zoneOrders, 'orders_count');
            } else {
                $zoneNames = [];
                $ordersCount = [];
            }
        } else {
            $orders = Orders::with('retailer')->latest()->limit(6)->get();

            $cur = 0;
            $date = Carbon::now()->subDays(7);
            // $ordersCountForGraph = DB::table('orders')->where('created_at', '>=', $date)
            //     ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            //     ->groupBy('date')
            //     ->get()->toArray();
            // dd($date['date']);
            $ordersCountForGraphPending = DB::table('orders')
                ->where('delivery_status', null)
                ->where('created_at', '>=', $date)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as pending'))
                ->groupBy('date')->get()->toArray();


            $ordersCountForGraphDelivered = DB::table('orders')
                ->where('delivery_status', 'Delivered')
                ->where('created_at', '>=', $date)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as delivered'))
                ->groupBy('date')->get()->toArray();
            // dd($ordersCountForGraphPending);
            $datesArr = array_merge_recursive($ordersCountForGraphPending, $ordersCountForGraphDelivered);
            sort($datesArr);
            // dd($datesArr);

            $zoneOrders = Zone::withCount('orders')
                ->active()
                ->get()
                ->toArray();

            if (count($zoneOrders) > 0) {
                $zoneNames = array_column($zoneOrders, 'name');
                $ordersCount = array_column($zoneOrders, 'orders_count');
            } else {
                $zoneNames = [];
                $ordersCount = [];
            }
        }
        return view('home', compact('orders', 'cur', 'ordersCountForGraphPending', 'zoneNames', 'ordersCount', 'last7dates', 'ordersCountForGraphDelivered', 'dates', 'datesArr'));
    }

    public function finances()
    {

        $financials = [];
        $zones = Zone::all();

        $financials = OptimizeTrait::getFinancials();
        return view('finance', compact('financials', 'zones'));
    }
    public function riders(Request $request)
    {
        $riders = User::whereRelation('roles', 'name', '=', 'Rider')->with(['rider'])->get();

        if ($request->ajax()) {
            return DataTables::of($riders)
                ->addColumn('type', function ($riders) {
                    $status = '<span class="badge badge-pill badge-soft-info font-size-11">Rider</span>';
                    if ($riders->rider->is_collector == 1) {
                        $status = '<span class="badge badge-pill badge-soft-secondary font-size-11">Collector</span>';
                    }
                    return $status;
                })
                ->addColumn('status', function ($riders) {
                    //                    dd($riders);
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($riders->active == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })
                ->addColumn('actions', function ($riders) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('edit.rider', $riders->id) . '" class="btn btn-primary">Edit</a>


                                            </div>';
                    return $actions;
                })
                ->rawColumns(['status', 'actions', 'type'])
                ->make(true);
        }
        return view('riders.index');
    }

    public function addRider()
    {
        $workingDays = WorkingDay::all();
        $zones = Zone::all();
        return view('riders.edit', compact('workingDays', 'zones'));
    }

    public function insertRider(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'min:8', 'unique:users'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'unique:users,mobile'],
            'password' => ['required'],
            'working_days' => ['required'],
            'zones' => ['required'],
            'passport_file' => ['required'],
            'license_file' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if ($request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }

        if ($request->collector == 'on') {
            // dd($request->collector);
            $request['is_collector'] = 1;
        } else {
            $request['is_collector'] = 0;
        }


        $request['password'] = Hash::make($request->password);

        $user = User::create($request->toArray());
        $user->password()->create([
            'password' => $request->password
        ]);
        $role = Roles::where('name', 'Rider')->first();
        $user->roles()->attach($role->id);

        $passportFile = null;

        if ($request->hasFile('passport_file')) {
            $fileName = time() . '_' . $request->passport_file->getClientOriginalName();
            $filePath = $request->file('passport_file')->storeAs('riderspassport', $fileName, 'public');
            $name = time() . '_' . $request->passport_file->getClientOriginalName();
            $file_path = url('/storage/' . $filePath);
            $passportFile = $file_path;
        }


        $licenseFile = null;

        if ($request->hasFile('license_file')) {
            $fileName_l = time() . '_' . $request->license_file->getClientOriginalName();
            $filePath_l = $request->file('license_file')->storeAs('riderslicense', $fileName_l, 'public');
            $name = time() . '_' . $request->license_file->getClientOriginalName();
            $file_path_l = url('/storage/' . $filePath_l);
            $licenseFile = $file_path_l;
        }

        $rider = $user->rider()->create([
            'unique_id' => Str::uuid(),
            'license_number' => $request->license_number,
            'passport' => $request->passport,
            'passport_file' => $passportFile,
            'license_file' => $licenseFile,
            'is_collector' => $request->is_collector,
        ]);

        $rider->workingdays()->attach($request->working_days);
        $rider->zones()->attach($request->zones);

        return redirect(route('riders'))->with('success', 'Rider Created');
    }


    public function editRider($id)
    {
        $user = User::with(['roles', 'rider'])->find($id);
        $workingDays = WorkingDay::all();
        $zones = Zone::all();
        return view('riders.edit', compact('workingDays', 'zones', 'user'));
    }

    public function updateRider($id, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => ['required', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'mobile' => ['required', Rule::unique('users')->ignore($id)],
            'password' => ['nullable'],
            'working_days' => ['required'],
            'zones' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->collector == 'on') {
            $request['is_collector'] = 1;
        } else {
            $request['is_collector'] = 0;
        }

        if ($request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }

        $user = User::find($id);
        $userup = User::where("id", $id)->update(request()->except(['_token', 'passport', 'password', 'working_days', 'zones', 'license_number', 'passport_file', 'license_file', 'collector', 'is_collector']));

        if ($request->has('password') && !is_null($request->password)) {
            $request['password'] = Hash::make($request->password);
            $user->password()->create([
                'password' => $request->password
            ]);
        }


        if ($request->hasFile('passport_file')) {
            $fileName = time() . '_' . $request->passport_file->getClientOriginalName();
            $filePath = $request->file('passport_file')->storeAs('riderspassport', $fileName, 'public');
            $name = time() . '_' . $request->passport_file->getClientOriginalName();
            $file_path = url('/storage/' . $filePath);
            $passportFile = $file_path;
            $user->rider()->update([
                'passport_file' => $passportFile,
            ]);
        }


        //        $licenseFile = null;

        if ($request->hasFile('license_file')) {
            $fileName_l = time() . '_' . $request->license_file->getClientOriginalName();
            $filePath_l = $request->file('license_file')->storeAs('riderslicense', $fileName_l, 'public');
            $name = time() . '_' . $request->license_file->getClientOriginalName();
            $file_path_l = url('/storage/' . $filePath_l);
            $licenseFile = $file_path_l;
            $user->rider()->update([
                'license_file' => $licenseFile,
            ]);
        }
        $rider = $user->rider()->update([
            'license_number' => $request->license_number,
            'passport' => $request->passport,
            'is_collector' => $request->is_collector,
        ]);
        //        dd($licenseFile);
        $user->rider->workingdays()->sync($request->working_days);
        $user->rider->zones()->sync($request->zones);

        return redirect(route('riders'))->with('success', 'Rider Updated');
    }

    public function retailers(Request $request)
    {

        //        $retailers = User::whereRelation('roles', 'name', '=', 'Retailer')->with(['rider'])->get();
        //        $secret = new SecretKey('eu_sk_');
        //        dd($secret->uuid);

        $retailers = User::with('retailer')->whereRelation('roles', 'name', '=', 'Retailer')->with(['retailer'])->get();

        if ($request->ajax()) {
            return DataTables::of($retailers)
                ->addColumn('website', function ($retailers) {
                    return $retailers->retailer->website;
                })
                ->addColumn('licensefile', function ($retailers) {
                    if (!is_null($retailers->retailer->licensefile)) {
                        return '<a href="' . $retailers->retailer->licensefile . '">View File</a>';
                    } else {
                        return null;
                    }
                })
                ->addColumn('status', function ($retailers) {

                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($retailers->active == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })
                ->addColumn('actions', function ($retailers) {

                    $actions = '<div class="btn-group-sm btngroupcst"  aria-label="Basic example">';
                    $actions .= '<a href="' . route('edit.retailer', $retailers->id) . '" class="btn btn-primary">Edit</a>
                    <a href="' . route('retailer.documents', $retailers->id) . '" class="btn btn-primary">Documents</a>
                                               <a href="' . route('auth.logs', $retailers->id) . '" class="btn btn-primary">Auth Logs</a>';
                    if (!is_null($retailers->retailer->promotion)) {
                        $actions .= '<a href="' . route('retailer.promotion', $retailers->id) . '" class="btn btn-info">Promotion</a>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status', 'actions', 'licensefile'])
                ->make(true);
        }
        return view('retailers.index');
    }

    public function addRetailer()
    {
        $bussinessTypes = BusinessType::all();
        $currencies = Currency::Active()->get();
        $chargesLists = RetailerChargesList::where('active', 1)->get();
        return view('retailers.edit', compact('bussinessTypes', 'currencies', 'chargesLists'));
    }

    public function insertRetailer(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => ['required', Rule::unique('users')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'mobile' => ['required', Rule::unique('users')],
            'password' => ['required'],
            'website' => ['required'],
            'licensefile' => ['required', 'mimes:pdf,docx', 'max:2048'],
            'business_type_id' => ['required'],
            'currency_id' => ['required'],
            'charges' => ['required']
        ]);


        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }

        $request['password'] = Hash::make($request->password);

        $user = User::create($request->toArray());
        $user->password()->create([
            'password' => $request->password
        ]);

        $role = Roles::where('name', 'Retailer')->first();
        $user->roles()->attach($role->id);

        $secret = new SecretKey('eu_sk_');
        $public = new SecretKey('eu_pk_');

        $licenseFile = null;

        if ($request->hasFile('licensefile')) {
            $fileName = time() . '_' . $request->licensefile->getClientOriginalName();
            $filePath = $request->file('licensefile')->storeAs('licenseFiles', $fileName, 'public');
            $name = time() . '_' . $request->licensefile->getClientOriginalName();
            $file_path = url('/storage/' . $filePath);
            $licenseFile = $file_path;
        }


        $user->retailer()->create([
            'business_type_id' => $request->business_type_id,
            'secret_key' => $secret->uuid,
            'public_key' => $public->uuid,
            'website' => $request->website,
            'currency_id' => $request->currency_id,
            'support_email' => $request->support_email,
            'support_mobile' => $request->support_mobile,
            'licensefile' => $licenseFile,
            // 'latitude' => $request->latitude,
            // 'longitude' => $request->longitude,
            'address' => $request->address,
        ]);
        $user->retailer->charges()->attach($request->charges);
        return redirect(route('retailers'))->with('success', 'Retailer Added');
    }

    public function editRetailer($id)
    {
        //        ->ignore($id)
        $bussinessTypes = BusinessType::all();
        $currencies = Currency::Active()->get();
        $user = User::with(['roles', 'retailer'])->find($id);
        $chargesLists = RetailerChargesList::where('active', 1)->get();
        return view('retailers.edit', compact('bussinessTypes', 'user', 'currencies', 'chargesLists'));
    }

    public function updateRetailer($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'mobile' => ['required', Rule::unique('users')->ignore($id)],
            'password' => ['nullable'],
            'website' => ['required'],
            'licensefile' => ['nullable', 'mimes:pdf,docx', 'max:2048'],
            'business_type_id' => ['required'],
            'currency_id' => ['required'],
            'charges' => ['required']
        ]);
        //        dd($request->all());
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }

        $user = User::find($id);
        $user->update([
            "name" => $request->name,
            "address" => $request->address,
        ]);
        if ($request->has('password') && !is_null($request->password)) {
            $request['password'] = Hash::make($request->password);
            $updatePass = $user->password()->update([
                'password' => $request->password
            ]);

            // Auth::logoutOtherDevices($request->password);
            OptimizeTrait::removeSessionFromDB($id);
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
        $retailer->business_type_id = $request->business_type_id;
        $retailer->website = $request->website;
        $retailer->currency_id = $request->currency_id;
        $retailer->support_email = $request->support_email;
        $retailer->latitude = $request->latitude;
        $retailer->longitude = $request->longitude;
        $retailer->address = $request->address;
        if (!is_null($licenseFile)) {
            $retailer->licensefile = $licenseFile;
        }
        $retailer->save();


        $retailer->charges()->sync($request->charges);
        return redirect(route('retailers'))->with('success', 'Retailer Updated');
    }

    public function riderList()
    {
        //        $riders = Rider::with('user')->whereHas('user', function ($q){
        //            $q->where('active',1);
        //        })->get();

        $riders = User::with(['rider', 'rider.workingdays', 'rider.zones'])->whereRelation('roles', 'name', '=', 'Rider')->get();


        return response()->json(['riders' => $riders], 200);
    }

    public function orders(Request $request)
    {

        if ($request->ajax()) {
            if (!is_null($request['amp;status'])) {
                $request['status'] = $request['amp;status'];
            }
            if (!is_null($request['amp;retailer'])) {
                $request['retailer'] = $request['amp;retailer'];
            }
            if (!is_null($request['amp;radioCheck']) && $request['amp;radioCheck'] == "return_inwarehouse") {
                $request['return_inwarehouse'] = 'return_inwarehouse';
            }
            if (!is_null($request['amp;radioCheck']) && $request['amp;radioCheck'] == "collector_inwarehouse") {
                $request['collector_inwarehouse'] = 'collector_inwarehouse';
            }
            if (!is_null($request['amp;radioCheck']) && $request['amp;radioCheck'] == "return_retailer") {
                $request['return_retailer'] = 'return_retailer';
            }
            if (!is_null($request['amp;type'])) {
                $request['type'] = $request['amp;type'];
            }
            if (!is_null($request['amp;start_date'])) {
                $request['start_date'] = $request['amp;start_date'];
            }
            if (!is_null($request['amp;end_date'])) {
                $request['end_date'] = $request['amp;end_date'];
            }
            if (!is_null($request['amp;search_text'])) {
                $request['search_text'] = $request['amp;search_text'];
            }
            // if (!is_null($request['amp;radioCheck']) && $request['amp;radioCheck'] == "return_inwarehouse") {
            //     $request['collector_inwarehouse'] = 'return_inwarehouse';
            // }
        }

        $types = RetailerChargesListItem::where('shopify_package_id', '<>', null)->whereHas('retailerCharges', function ($query) {
            $query->where('active', 1);
        })
            ->with(['shopifyPackage' => function ($query) {
                $query->where('status', 1);
            }])->distinct('shopify_package_id')->get(['shopify_package_id']);

        $uniqueShopifyPackageIds = $types->pluck('shopify_package_id')->toArray();
        $packages = ShopifyPackage::whereIn('id', $uniqueShopifyPackageIds)->get();

        // if (!is_null($request['radioCheck']) && $request['radioCheck'] == "collector_inwarehouse") {

        // }
        // dd($request->all());
        if (Auth::user()->isAdmin()) {
            $orders = Orders::latest();
            if ($request->has('retailer') && !is_null($request->retailer)) {
                $orders = $orders->whereHas('retailer', function ($query) use ($request) {
                    $query->where('website', '=', $request->retailer);
                });
            }
            if ($request->has('end_user') && !is_null($request->end_user)) {
                // dd('here');
                $orders = $orders->where('enduser_email', 'LIKE', "%{$request->end_user}%");
            }

            if ($request->has('status') && !is_null($request->status)) {
                if ($request->status == 'null') {
                    $orders = $orders->where('delivery_status', NULL);
                } else {
                    $orders = $orders->where('delivery_status', $request->status);
                }
            }
            if ($request->has('return_inwarehouse') && !is_null($request->return_inwarehouse)) {
                // dd('here');
                $orders = $orders->where('return_delivery_status', 'In Warehouse');
            }
            if ($request->has('return_retailer') && !is_null($request->return_retailer)) {
                // dd('here');
                $orders = $orders->where('return_delivery_status', 'Returned to Retailer');
            }
            if ($request->has('collector_inwarehouse') && !is_null($request->collector_inwarehouse)) {
                // dd('here');
                $orders = $orders->where('collector_delivery_status', 'In Warehouse');
            }
            if ($request->has('type') && !is_null($request->type)) {
                $orders = $orders->where('shopify_package_id', $request->type);
            }

            if ($request->has('search_text') && !is_null($request->search_text)) {
                $searchText = $request->input('search_text');
                $orders = $orders->where('enduser_name', 'like', "%$searchText%")
                    ->orWhere('enduser_email', 'like', "%$searchText%")->orWhere('enduser_mobile', 'like', "%$searchText%")
                    ->orWhere('enduser_address', 'like', "%$searchText%")->orWhere('order_type', 'like', "%$searchText%")
                    ->orWhereHas('retailer.user', function ($query) use ($searchText) {
                        $query->where('name', 'like', "%$searchText%");
                    });
            }

            if (($request->has('start_date') && !is_null($request->start_date)) ||
                ($request->has('end_date') && !is_null($request->end_date))
            ) {

                $orders = $orders->when($request->has('start_date') && !is_null($request->start_date), function ($query) use ($request) {
                    $start_date = Carbon::parse($request->start_date)->startOfDay();
                    return $query->where('created_at', '>=', $start_date);
                })->when($request->has('end_date') && !is_null($request->end_date), function ($query) use ($request) {
                    $end_date = Carbon::parse($request->end_date)->endOfDay();
                    return $query->where('created_at', '<=', $end_date);
                });
            }
            // dd($orders->get());
            $totalData = $orders->count();
            $totalFiltered = $totalData;

            $limit = ($request->length) ? $request->length : 10;
            $start = ($request->start) ? $request->start : 0;
            $orders = $orders->skip($start)->take($limit)->get();
            $statuses = Orders::select('delivery_status')->distinct()->get()->pluck('delivery_status')->toArray();
            // dd($statuses);
        } else {

            $orders = Orders::where('retailer_id', Auth::user()->retailer->id)->latest();

            if ($request->has('end_user') && !is_null($request->end_user)) {
                // dd('here');
                $orders = $orders->where('enduser_email', 'LIKE', "%{$request->end_user}%");
            }
            if ($request->has('status') && !is_null($request->status)) {
                if ($request->status == 'null') {
                    $orders = $orders->where('delivery_status', NULL);
                } else {
                    $orders = $orders->where('delivery_status', $request->status);
                }
            }
            if ($request->has('type') && !is_null($request->type)) {
                // dd('here');
                $orders = $orders->where('shopify_package_id', $request->type);
            }

            if ($request->has('search_text') && !is_null($request->search_text)) {
                $searchText = $request->input('search_text');
                $orders = $orders->where(function ($query) use ($searchText) {
                    $query->where('enduser_name', 'like', "%$searchText%")
                        ->orWhere('enduser_email', 'like', "%$searchText%")
                        ->orWhere('enduser_mobile', 'like', "%$searchText%")
                        ->orWhere('enduser_address', 'like', "%$searchText%")
                        ->orWhere('order_type', 'like', "%$searchText%")
                        ->orWhereHas('retailer.user', function ($query) use ($searchText) {
                            $query->where('name', 'like', "%$searchText%");
                        });
                });
            }

            if (($request->has('start_date') && !is_null($request->start_date)) ||
                ($request->has('end_date') && !is_null($request->end_date))
            ) {

                $orders = $orders->when($request->has('start_date') && !is_null($request->start_date), function ($query) use ($request) {
                    $start_date = Carbon::parse($request->start_date)->startOfDay();
                    return $query->where('created_at', '>=', $start_date);
                })->when($request->has('end_date') && !is_null($request->end_date), function ($query) use ($request) {
                    $end_date = Carbon::parse($request->end_date)->endOfDay();
                    return $query->where('created_at', '<=', $end_date);
                });
            }

            $totalData = $orders->count();
            $totalFiltered = $totalData;

            $limit = ($request->length) ? $request->length : 10;
            $start = ($request->start) ? $request->start : 0;
            $orders = $orders->skip($start)->take($limit)->get();
            // dd($orders);
            $statuses = array_unique($orders->pluck('delivery_status')->toArray());
        }
        // dd($orders);
        if ($request->ajax()) {

            // if()
            return DataTables::of($orders)
                ->setOffset($start)
                ->addIndexColumn()
                ->addColumn('retailer', function ($orders) {
                    $html = '<p class="mb-0"><b>Name:</b> ' . $orders->retailer->user->name . '</p>';
                    $html .= '<p class="mb-0"><b>Website:</b> ' . $orders->retailer->website . '</p>';
                    $html .= '<p class="mb-0"><b>Order:</b> ' . $orders->order_number . '</p>';
                    if ((Auth::user()->isAdmin() && $orders->collection_ready == 1) && ($orders->assigned_to_collector == 0)) {
                        $html .= '<span class="mb-0"><span class="badge rounded-pill badge-soft-success">Ready for Collection</span></span>';
                    }

                    if (Auth::user()->isAdmin() && $orders->undelivered == 1) {
                        $html .= '<span class="mb-0"><span class="badge rounded-pill badge-soft-danger">Undelivered</span></span>';
                    }

                    if ($orders->return == 1) {
                        $html .= '<span class="badge badge-soft-info ms-1" style="">Return Initiated </span>';
                    } else if ($orders->return == 2) {
                        $html .= '<span class="badge badge-soft-warning ms-1" style="">Return Accepted by
                        Retailer</span>';
                    } else if ($orders->return == 4) {
                        $html .= '<span class="badge badge-soft-success ms-1" style="">Returned to Customer</span>';
                    }

                    return $html;
                })
                // ->addColumn('')
                ->editColumn('shipping_charges', function ($orders) {
                    if ($orders->is_grocery == 1) {
                        return '<span class="badge rounded-pill bg-success">Grocery</span>';
                    }
                    return $orders->shipping_charges;
                })
                ->addColumn('enduser', function ($orders) {
                    return $orders->enduser_name . ' | ' . $orders->enduser_email;
                })
                ->addColumn('delivery_info', function ($orders) {
                    $di = $orders->delivery_information;
                    $signature = $di->signature ?? '';
                    $pod = $di->pacakge_image ?? '';
                    $html = '<ul>';
                    if (!empty($pod)) {
                        $html .= '<li><a href="' . $pod . '" target="_blank">POD: View</a></li>';
                    }
                    if (!empty($signature)) {
                        $html .= '<li><a href="' . $signature . '" target="_blank">Signature: View</a></li>';
                    }
                    $html .= '</ul>';

                    return $html;
                })
                ->addColumn('zone', function ($orders) {
                    $data = 'Postal Code not found in Endup Zone list';
                    if (!is_null($orders->zone)) {
                        $data = $orders->zone->name;
                    } elseif (!is_null($orders->dropoff_postal)) {
                        $zone = Zone::whereHas(
                            'postalcodes',
                            function ($q) use ($orders) {
                                $q->where('postal', $orders->dropoff_postal);
                            }
                        )->first();
                        if ($zone != null) {
                            $data = $zone->name;
                        }
                    }



                    return $data;
                })
                ->addColumn('delivery_non_html', function ($orders) {
                    return $orders->delivery_status;
                })
                ->addColumn('delivery_status', function ($orders) {

                    $delivery_status = '<span class="badge badge-soft-primary">' . ucwords($orders->delivery_status) . '</span>';
                    if (is_null($orders->delivery_status)) {
                        $delivery_status = '<span class="badge badge-soft-info">' . ucwords('pending') . '</span>';
                    } elseif ($orders->delivery_status == 'Undelivered') {
                        $delivery_status = '<span class="badge badge-soft-danger">' . ucwords($orders->delivery_status) . '</span>';
                    }

                    return $delivery_status;
                })
                ->addColumn('collector_delivery_status', function ($orders) {



                    $delivery_status = '<span class="badge badge-soft-primary">' . ucwords($orders->collector_delivery_status) . '</span>';
                    if (is_null($orders->collector_delivery_status)) {
                        $delivery_status = '<span class="badge badge-soft-info">' . ucwords('pending') . '</span>';
                    }

                    if (!is_null($orders->return_delivery_status)) {
                        $delivery_status .= '<span class="badge badge-soft-warning my-2"><b>Return Status: </b>' . ucwords($orders->return_delivery_status) . '</span>';
                    }

                    return $delivery_status;
                })

                ->addColumn('actions', function ($orders) {
                    // if ($orders->delivery_status != null) {
                    $actions = '<div class="" aria-label="Basic example">

                                                   <a href="' . route('order.items', $orders->id) . '" class="btn btn-secondary btn-sm">Details</a>
                                                   <a href="' . route('edit.manual.order', $orders->id) . '" class="btn btn-success btn-sm">Edit</a>
                                            ';

                    if ($orders->is_shopify == 1) {
                        if ($orders->delivery_status != 'delivered') {
                            $actions .= '<a href="' . route('update.shopify.order', $orders->order_number) . '" class="btn btn-warning btn-sm">
                        Fulfill</a>';
                        } elseif ($orders->delivery_status == 'delivered') {
                            $actions .= '<button class="btn btn-warning btn-sm" disabled>Fulfilled</button>';
                        }
                    }

                    if (Auth::user()->isRetailer() && $orders->collection_ready == 0) {
                        $actions .= '<a href="' . route('ready.collection', $orders->id) . '" class="btn btn-success btn-sm">Ready for Collection</a>';
                    }

                    $actions .= '</div>';
                    return $actions;
                    // }
                })
                ->addColumn('date', function ($data) {
                    return [
                        'display' => e($data->created_at->format('F, j, Y | h:i:s a')),
                        'timestamp' => $data->created_at->timestamp
                    ];
                    // return date('d-m-Y h:i:s', strtotime($data->created_at));
                })
                ->rawColumns(['collector_delivery_status', 'actions', 'delivery_status', 'retailer', 'shipping_charges', 'delivery_type', 'delivery_info'])
                ->with([
                    "recordsTotal"    => $totalData,
                    "recordsFiltered" => $totalFiltered,
                ])
                ->make(true);
        }
        return view('orders.index', compact('statuses', 'types'));
    }

    public function collectionReady($id)
    {
        $order = Orders::find($id);
        // dd($order);
        $order->collection_ready = 1;
        $order->save();
        return back()->with('success', 'Collection Ready');
    }

    public function assignToRider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],

        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }

        $orders = Orders::with('items', 'retailer')->whereIn('id', $request->id)->where('assigned_to_collector', 1)->where('assigned_to_rider', 0)->orderBy('distance')->get();
        //        dd($orders);
        if (count($orders) < 1) {
            return back()->with('error', 'Please select available orders');
        }

        $warehouses = Warehouse::all();

        $pickup = null;
        if (count(array_unique($orders->pluck('pickup_coordinates')->toArray())) < 2) {
            $pickup = array_unique($orders->pluck('pickup_coordinates')->toArray())[0];
        }
        $riders = User::with(['rider', 'rider.workingdays', 'rider.zones'])->whereRelation('roles', 'name', '=', 'Rider')->whereHas('rider', function ($query) {
            $query->where('is_collector', '=', 0);
        })->get();
        return view('orders.assign_to_rider', compact('orders', 'riders', 'pickup', 'warehouses'));
    }

    public function createCollection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],

        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }

        // $checkLabels = Orders::whereIn('id', $request->id)
        //     ->whereHas('items', function ($query) {
        //         $query->with('scan_info');
        //     })
        //     ->with('items.scan_info')
        //     ->get();

        // foreach ($checkLabels as $or) {
        //     foreach ($or->items as $it) {
        //         if (is_null($it->scan_info)) {
        //             return back()->with('error', 'Please generate labels for each item first!');
        //         }
        //     }
        // }

        $orderIdsWithoutLabels = Orders::whereIn('id', $request->id)
            ->whereDoesntHave('items.scan_info')
            ->pluck('id')
            ->toArray();

        if (!empty($orderIdsWithoutLabels)) {
            return back()->with('error', 'Please generate labels for all items in the selected orders');
        }

        $orders = Orders::with('items', 'retailer')
            ->whereIn('id', $request->id)
            ->where('assigned_to_collector', 0)
            ->where('assigned_to_rider', 0)
            ->where('collection_ready', 1)
            ->orderBy('collector_distance', 'DESC')
            ->get();

        // $returnOrders = Orders::with('items', 'retailer')
        //     ->whereIn('id', $request->id)
        //     ->where('assigned_to_collector', 1)
        //     ->where('assigned_to_rider', 1)
        //     ->where('collection_ready', 1)
        //     ->where('return', '!=', 0)
        //     ->where('return_delivery_status', 'In Warehouse')
        //     ->orderBy('collector_distance', 'DESC');
        // $orders = $collectorders->union($returnOrders)->get();
        // dd($data);
        if (count($orders) < 1) {
            return back()->with('error', 'Please select available orders');
        }



        $warehouses = Warehouse::all();

        $pickup = null;

        if (count(array_unique($orders->pluck('pickup_coordinates')->toArray())) < 2) {
            $pickup = array_unique($orders->pluck('pickup_coordinates')->toArray())[0];
        }
        $riders = User::with(['rider', 'rider.workingdays', 'rider.zones'])->whereRelation('roles', 'name', '=', 'Rider')->whereHas('rider', function ($query) {
            $query->where('is_collector', '=', 1);
        })->get();
        return view('orders.collection', compact('orders', 'riders', 'pickup', 'warehouses'));
    }

    public function createCollectionReturn(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => ['required'],

        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }

        // dd('s');
        $orders = Orders::with('items', 'retailer')
            ->whereIn('id', $request->id)
            ->where('assigned_to_collector', 1)
            ->where('assigned_to_rider', 1)
            ->where('collection_ready', 1)
            ->where('return', '!=', 0)
            ->where('return_delivery_status', 'In Warehouse')
            ->orderBy('collector_distance', 'DESC')->get();
        // $orders = $collectorders->union($returnOrders)->get();
        // dd($orders);
        if (count($orders) < 1) {
            return back()->with('error', 'Please select available orders');
        }

        $warehouses = Warehouse::all();

        $pickup = null;

        if (count(array_unique($orders->pluck('pickup_coordinates')->toArray())) < 2) {
            $pickup = array_unique($orders->pluck('pickup_coordinates')->toArray())[0];
        }
        $riders = User::with(['rider', 'rider.workingdays', 'rider.zones'])->whereRelation('roles', 'name', '=', 'Rider')->whereHas('rider', function ($query) {
            $query->where('is_collector', '=', 1);
        })->get();
        return view('orders.collection-return', compact('orders', 'riders', 'pickup', 'warehouses'));
    }

    public function createReturnBlock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }

        $orders = Orders::with('items', 'retailer')
            ->whereIn('id', $request->id)
            ->where('assigned_to_collector', 1)
            ->where('assigned_to_rider', 1)
            ->where('collection_ready', 1)
            ->where('return', 2)
            ->where('return_delivery_status', null)
            ->where('return_to_warehouse', 0)
            ->orderBy('collector_distance', 'DESC')
            ->get();

        if (count($orders) < 1) {
            return back()->with('error', 'Please select available orders');
        }

        $warehouses = Warehouse::all();

        $pickup = null;
        if (count(array_unique($orders->pluck('return_pickup_coordinates')->toArray())) < 2) {
            $pickup = array_unique($orders->pluck('return_pickup_coordinates')->toArray())[0];
        }
        $riders = User::with(['rider', 'rider.workingdays', 'rider.zones'])
            ->whereRelation('roles', 'name', '=', 'Rider')
            ->whereHas('rider', function ($query) {
                $query->where('is_collector', '=', 1);
            })->get();
        return view('orders.return', compact('orders', 'riders', 'pickup', 'warehouses'));
    }
    public function assignOrder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => ['required'],
            'warehouse' => ['required'],
            'pickup_date_time' => ['required'],
            'per_hour_earning' => ['required'],
        ]);
        $pickup = date('h:i A', strtotime($request->pickup_date_time . ' +3 Hours'));

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }
        $warehouse = Warehouse::find($request->warehouse);
        $order_ids = json_decode($request->order_id);
        $request['pickup_date_time'] = date('Y-m-d H:i:s', strtotime($request->pickup_date_time));
        // dd($request->all());
        $block_number = (int) substr(time(), -6);

        $getPickupAddress = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $warehouse->coordinates . '&sensor=true&key=' . env('GOOGLE_MAP_KEY');

        $resPickupAddress = Http::get($getPickupAddress)->json();

        $orders = Orders::with('statuses')->find($order_ids);
        //dd($orders);
        $orders->each(function ($order) use ($request) {
            $order->delivery_status = !is_null($request->rider_id) ? 'Assign to Rider' : 'Block Created';
            $order->assigned_to_rider = 1;
            $order->save();
        });

        $statuses = $orders->map(function ($order) use ($request) {
            return ['status' => !is_null($request->rider_id) ? 'Assign to Rider' : 'Block Created'];
        });

        $orders->each(function ($order, $index) use ($statuses) {
            $order->statuses()->create($statuses[$index]);
        });


        // if (!is_null($request->rider_id)) {
        //     foreach ($orders as $o) {

        //         $o->delivery_status = 'Assign to Rider';
        //         $o->assigned_to_rider = 1;
        //         $o->save();

        //         $o->statuses()->create([
        //             'status' => 'Assign to Rider',
        //         ]);
        //     }
        // } else {
        //     foreach ($orders as $o) {
        //         $o->delivery_status = 'Block Created';
        //         $o->assigned_to_rider = 1;
        //         $o->save();
        //         $o->statuses()->create([
        //             'status' => 'Block Created',
        //         ]);
        //     }
        // }

        $block = new Block();
        if (!is_null($request->rider_id)) {
            $block->user_id = $request->rider_id;
        }
        $block->number = $block_number;
        $block->pickup_location = (isset($resPickupAddress['results'][0]['formatted_address'])) ? $resPickupAddress['results'][0]['formatted_address'] : null;
        $block->pickup_location_cordinates = $warehouse->coordinates;
        $block->pickup_date_time = $request->pickup_date_time;
        $block->per_hour_earning = $request->per_hour_earning;
        $block->save();
        $block->orders()->attach($order_ids);



        //        if (!is_null($request->rider_id)) {
        $emailData = $orders->map(function ($order) use ($request, $block, $pickup) {
            return [
                //                    'url' => 'https://enduptech.tijarah.ae/check/location?rider=' . $request->rider_id . '&block=' . $block->id . '&order=' . $order->id,
                'email' => (isset($order->enduser_email) && !empty($order->enduser_email)) ? $order->enduser_email : 'support@hashedsystem.com',
                //                    'email' => 'hamzarazzaq96@gmail.com',
                'expected_delivery_time' => $pickup,
                'delivery_time' => date('Y/m/d h:i A', strtotime($request->pickup_date_time))
            ];
        });

        $emailData->each(function ($data) {
            Mail::send('email.order-assigned', $data, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Your order assigned to rider for delivery');
            });
        });
        //        }
        //    dd('h445');

        if (!is_null($request->rider_id)) {
            return redirect()->route('orders')->with('success', 'Order Assigned to Rider Successfully');
        } else {
            return redirect()->route('orders')->with('success', 'Block Created Successfully');
        }
    }

    public function riderOrders($id, $date)
    {

        $rider = User::with([
            'rider',
            'assignedOrders' => function ($q) use ($date) {
                $q->whereDate('pickup_date_time', $date);
            }
        ])->whereRelation('roles', 'name', '=', 'Rider')->find($id);
        //        dd($rider);
        $orders = $rider->assignedOrders;
        $orders->mapWithKeys(function ($o) use ($rider) {
            $o['pickup_date_time'] = date('F,j Y h:i a', strtotime($o->pickup_date_time));
            $o['dropoff_date_time'] = date('F,j Y h:i a', strtotime($o->dropoff_date_time));
            //               dd($o);
            return $o;
        });
        //            dd($orders);
        return response()->json(['rider' => $rider], 200);
    }

    public function retailerPrices(Request $request)
    {
        $prices = RetailerPrice::with('retailer')->get();
        if ($request->ajax()) {
            return DataTables::of($prices)
                ->addColumn('retailer', function ($prices) {
                    return url($prices->retailer->website);
                })->addColumn('discount', function ($prices) {
                    return number_format($prices->extra_discount_percentage, 2) . '%';
                })->addColumn('surcharge', function ($prices) {
                    return number_format($prices->extra_surcharge, 2);
                })
                ->addColumn('actions', function ($prices) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('edit.retailer.price', $prices->id) . '" class="btn btn-primary btn-sm">Edit</a>
                                                   <a href=""  class="btn btn-danger btn-sm">Delete</a>
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('retailer_prices.index');
    }

    public function addRetailerPrice()
    {
        $retailers = Retailer::all();
        return view('retailer_prices.edit', compact('retailers'));
    }

    public function insertRetailerPrice(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'retailer_id' => ['required'],
            'extra_discount_percentage' => ['nullable', 'numeric'],
            'extra_surcharge' => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        RetailerPrice::create($request->all());

        return redirect(route('retailer.prices'))->with('success', 'Price Added Successfully');
    }

    public function editRetailerPrice($id)
    {
        $price = RetailerPrice::with('retailer')->find($id);
        $retailers = Retailer::all();
        return view('retailer_prices.edit', compact('retailers', 'price'));
    }

    public function updateRetailerPrice($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'retailer_id' => ['required'],
            'extra_discount_percentage' => ['nullable', 'numeric'],
            'extra_surcharge' => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        RetailerPrice::find($id)->update($request->all());

        return redirect(route('retailer.prices'))->with('success', 'Price Updated Successfully');
    }


    public function currencies(Request $request)
    {
        $curr = Currency::all();
        if ($request->ajax()) {
            return DataTables::of($curr)
                ->addColumn('code', function ($curr) {
                    return strtoupper($curr->code);
                })
                ->addColumn('status', function ($curr) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($curr->status == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })
                ->addColumn('actions', function ($curr) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('edit.currency', $curr->id) . '" class="btn btn-primary btn-sm">Edit</a>

                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }
        return view('currencies.index');
    }

    public function addCurrency()
    {
        return view('currencies.edit');
    }

    public function insertCurrency(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'code' => ['required', 'unique:currencies,code'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $request['code'] = strtoupper($request->code);
        if ($request->has('status') && $request->status == 'on') {
            $request['status'] = 1;
        } else {
            $request['status'] = 0;
        }

        Currency::create($request->all());

        return redirect(route('currencies'))->with('success', 'Currency Added Successfully');
    }

    public function editCurrency($id)
    {
        $currency = Currency::find($id);
        return view('currencies.edit', compact('currency'));
    }

    public function updateCurrency($id, Request $request)
    {


        $validator = Validator::make($request->all(), [
            'code' => ['required', 'unique:currencies,code,' . $id],
        ]);
        //        dd($validator);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $request['code'] = strtoupper($request->code);
        if ($request->has('status') && $request->status == 'on') {
            $request['status'] = 1;
        } else {
            $request['status'] = 0;
        }

        Currency::find($id)->update($request->all());

        return redirect(route('currencies'))->with('success', 'Currency Updated Successfully');
    }

    public function discounts(Request $request)
    {
        $discount = Discount::all();
        if ($request->ajax()) {
            return DataTables::of($discount)
                ->addColumn('code', function ($discount) {
                    return strtoupper($discount->code);
                })->addColumn('express', function ($discount) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">No</span>';
                    if ($discount->for_express == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Yes</span>';
                    }
                    return $status;
                })->addColumn('domestic', function ($discount) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">No</span>';
                    if ($discount->for_domestic == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Yes</span>';
                    }
                    return $status;
                })->addColumn('international', function ($discount) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">No</span>';
                    if ($discount->for_international == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Yes</span>';
                    }
                    return $status;
                })->addColumn('start_date', function ($discount) {
                    return date('F, j, Y', strtotime($discount->date_start_expiry));
                })->addColumn('end_date', function ($discount) {
                    return date('F, j, Y', strtotime($discount->date_end_expiry));
                })
                ->addColumn('status', function ($discount) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($discount->status == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })
                ->addColumn('actions', function ($discount) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('edit.discount', $discount->id) . '" class="btn btn-primary btn-sm">Edit</a>

                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'status', 'express', 'domestic', 'international'])
                ->make(true);
        }
        return view('discounts.index');
    }

    public function addDiscount()
    {
        return view('discounts.edit');
    }

    public function insertDiscount(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'unique:currencies,code'],
            'date_start_expiry' => ['required'],
            'date_end_expiry' => ['required'],
            'single_time' => ['required'],
            'for_express' => ['required'],
            'for_domestic' => ['required'],
            'for_international' => ['required'],
            'value' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->has('status') && $request->status == 'on') {
            $request['status'] = 1;
        } else {
            $request['status'] = 0;
        }
        Discount::create(
            [
                'code' => $request['code'],
                'value' => $request['value'],
                'date_start_expiry' => $request['date_start_expiry'],
                'date_end_expiry' => $request['date_end_expiry'],
                'single_time' => $request['single_time'],
                'for_express' => $request['for_express'],
                'for_domestic' => $request['for_domestic'],
                'for_international' => $request['for_international'],

            ]
        );
        return redirect(route('discounts'))->with('success', 'Discount Added Successfully');
    }

    public function editDiscount($id)
    {
        $discount = Discount::find($id);
        return view('discounts.edit', compact('discount'));
    }

    public function updateDiscount($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'unique:currencies,code,' . $id],
            'date_start_expiry' => ['required'],
            'date_end_expiry' => ['required'],
            'single_time' => ['required'],
            'for_express' => ['required'],
            'for_domestic' => ['required'],
            'for_international' => ['required'],
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if ($request->has('status') && $request->status == 'on') {
            $request['status'] = 1;
        } else {
            $request['status'] = 0;
        }

        Discount::find($id)->update($request->all());
        return redirect(route('discounts'))->with('success', 'Discount Updated Successfully');
    }

    public function orderItems($id)
    {
        $order = Orders::with(['items', 'block', 'retailer', 'statuses'])->find($id);

        // dd($order);
        return view('orders.items', compact('order'));
    }


    public function indexPrices(Request $request)
    {
        $prices = Price::all();
        if ($request->ajax()) {
            return DataTables::of($prices)
                ->addColumn('location', function ($prices) {
                    $from = $prices->city_from . ', ' . $prices->country_from . ', ' . $prices->postal_code_from;
                    $to = $prices->city_to . ', ' . $prices->country_to . ', ' . $prices->postal_code_to;
                    $html = '<ul class="priceLocList" style="list-style: none;padding-left: 0">
                                <li><b>From: </b>' . $from . '</li>
                                <li><b>To: </b>' . $to . '</li>
                            </ul>';
                    return $html;
                })
                ->addColumn('currency', function ($prices) {
                    return $prices->currency->code;
                })
                ->addColumn('actions', function ($prices) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('edit.currency', $prices->id) . '" class="btn btn-primary btn-sm">Edit</a>
                                                   <a href="" class="btn btn-danger btn-sm">Delete</a>
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'status', 'location'])
                ->make(true);
        }
        return view('prices.index');
    }

    public function addPrice()
    {
        $currencies = Currency::where('status', 1)->get();
        $terms = ShippingTerm::all();
        return view('prices.edit', compact('currencies', 'terms'));
    }

    public function insertPrice(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'city_from' => ['required'],
            'country_from' => ['required'],
            'postal_code_from' => ['required', 'numeric'],
            'city_to' => ['required'],
            'country_to' => ['required'],
            'postal_code_to' => ['required', 'numeric'],
            'volumetric_weight' => ['required', 'numeric'],
            'length' => ['required', 'numeric'],
            'height' => ['required', 'numeric'],
            'width' => ['required', 'numeric'],
            'quantity_box' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'currency' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $request['currency_id'] = $request['currency'];
        Price::create($request->all());

        return redirect(route('prices'))->with('success', 'Price Added Successfully');
    }

    public function retailerPromotion($id)
    {
        $user = User::find($id);
        $retailer = $user->retailer;
        //        dd($retailer);
        return view('promotions.index', compact('retailer'));
    }

    public function jsonOrder($id)
    {
        $order = Orders::with('items', 'retailer.user')->find($id);
        return response()->json(['order' => $order], 200);
    }

    public function collectorVerifyDistance(Request $request)
    {

        $warehouse = Warehouse::find($request->warehouse);
        // return $warehouse;
        $order_ids = $request->orders;
        $orders = Orders::find($order_ids);

        foreach ($orders as $key => $o) {
            // if (is_null(($o->collector_dropoff_coordinates) || is_null($o->collector_pickup_coordinates)) || is_null($o->collector_distance)) {
            // dd('hre');
            $getCord[$key] = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($o->retailer->address) . '&key=' . env('GOOGLE_MAP_KEY');

            $response[$key] = Http::get($getCord[$key])->json();
            // dd($response[$key]);
            $o->warehouse_id = $warehouse->id;
            if (isset($response[$key]['results'][0])) {
                $pickup[$key] = $response[$key]['results'][0]['geometry']['location']['lat'] . ', ' . $response[$key]['results'][0]['geometry']['location']['lng'];
                $o->collector_dropoff_coordinates = $warehouse->coordinates;
                $o->collector_pickup_coordinates = $pickup[$key];

                $getDistance[$key] = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=' . $pickup[$key] . '&destinations=' . $warehouse->coordinates . '&key=' . env('GOOGLE_MAP_KEY');
                $responsedis[$key] = Http::get($getDistance[$key])->json();
                // dd($responsedis[$key]);
                if (isset($responsedis[$key]['rows'][0]['elements'][0]['distance'])) {
                    $o->collector_distance = $responsedis[$key]['rows'][0]['elements'][0]['distance']['text'];
                }
            } else {
                return response()->json(['error' => $response[$key]['error_message']], 400);
            }
            $o->save();
            // }
        }

        return response()->json($orders);
    }

    public function returnBlockVerifyDistance(Request $request)
    {

        $warehouse = Warehouse::find($request->warehouse);
        // return $warehouse;
        $order_ids = $request->orders;
        $orders = Orders::find($order_ids);

        foreach ($orders as $key => $o) {
            // if (is_null($o->return_dropoff_coordinates) || is_null($o->return_pickup_coordinates)) {
            $getCord[$key] = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($o->enduser_address) . '&key=' . env('GOOGLE_MAP_KEY');

            $response[$key] = Http::get($getCord[$key])->json();
            // dd($response[$key]);
            $o->warehouse_id = $warehouse->id;
            if (isset($response[$key]['results'][0])) {
                $pickup[$key] = $response[$key]['results'][0]['geometry']['location']['lat'] . ', ' . $response[$key]['results'][0]['geometry']['location']['lng'];
                $o->return_dropoff_coordinates = $warehouse->coordinates;
                $o->return_pickup_coordinates = $pickup[$key];

                $getDistance[$key] = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=' . $pickup[$key] . '&destinations=' . $warehouse->coordinates . '&key=' . env('GOOGLE_MAP_KEY');
                $responsedis[$key] = Http::get($getDistance[$key])->json();
                // dd($responsedis[$key]);
                if (isset($responsedis[$key]['rows'][0]['elements'][0]['distance'])) {
                    $o->return_distance = $responsedis[$key]['rows'][0]['elements'][0]['distance']['text'];
                }
            } else {
                return response()->json(['error' => $response[$key]['error_message']], 400);
            }
            $o->save();
            // }
        }

        return response()->json($orders);
    }

    public function verifyDistance(Request $request)
    {
        // dd($request->all());
        // return $request->all();
        $warehouse = Warehouse::find($request->warehouse);
        // $pickup = $request->pickup_cordinates;
        // $pickupLat = explode(', ', $pickup)[0];
        // $pickuplong = explode(', ', $pickup)[1];

        $order_ids = $request->orders;

        // dd($pickupLat, $pickuplong, $orders);

        $orders = Orders::find($order_ids);

        foreach ($orders as $key => $o) {
            // if (is_null($o->dropoff_coordinates) || is_null($o->pickup_coordinates)) {
            $getCord[$key] = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($o->enduser_address) . '&key=' . env('GOOGLE_MAP_KEY');

            $response[$key] = Http::get($getCord[$key])->json();
            $o->warehouse_id = $warehouse->id;
            if (isset($response[$key]['results'][0])) {
                $dropoffLatLong[$key] = $response[$key]['results'][0]['geometry']['location']['lat'] . ', ' . $response[$key]['results'][0]['geometry']['location']['lng'];
                $o->dropoff_coordinates = $dropoffLatLong[$key];
                $o->pickup_coordinates = $warehouse->coordinates;

                $getDistance[$key] = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=' . $warehouse->coordinates . '&destinations=' . $dropoffLatLong[$key] . '&key=' . env('GOOGLE_MAP_KEY');
                $responsedis[$key] = Http::get($getDistance[$key])->json();
                // dd($responsedis[$key]);
                if (isset($responsedis[$key]['rows'][0]['elements'][0]['distance'])) {
                    $o->distance = $responsedis[$key]['rows'][0]['elements'][0]['distance']['text'];
                }
            }

            $o->save();
            // }
        }

        // dd($orders);

        return response()->json($orders);
    }

    public function indexBlocks(Request $request)
    {
        $blocks = Block::with('orders', 'user')->get();
        $riders = User::with(['rider', 'rider.workingdays', 'rider.zones'])->whereRelation('roles', 'name', '=', 'Rider')->whereHas('rider', function ($query) {
            $query->where('is_collector', '=', 0);
        })->get();

        if ($request->ajax()) {
            return DataTables::of($blocks)
                ->addColumn('order_count', function ($blocks) {
                    return count($blocks->orders);
                })
                ->addColumn('rider', function ($blocks) {
                    if (is_null($blocks->user_id)) {
                        return '';
                    }
                    return $blocks->user->email;
                })
                ->addColumn('orders_table', function ($blocks) {

                    $html = '<tbody>';

                    if (count($blocks->orders) > 0) {
                        foreach ($blocks->orders as $key => $order) {
                            $delivery_type = '<span class="badge badge-soft-primary">' . ucwords($order->order_type) . '</span>';
                            if ($order->order_type == 'express') {
                                $delivery_type = '<span class="badge badge-soft-success">' . ucwords($order->order_type) . '</span>';
                            }

                            $delivery_status = '<span class="badge badge-soft-primary">' . ucwords($order->delivery_status) . '</span>';
                            if (strtolower($order->delivery_status) == 'out for delivery') {
                                $delivery_status = '<span class="badge badge-soft-Warning">' . ucwords($order->delivery_status) . '</span>';
                            } elseif (strtolower($order->delivery_status) == 'delivered') {
                                $delivery_status = '<span class="badge badge-soft-success">' . ucwords($order->delivery_status) . '</span>';
                            } elseif (strtolower($order->delivery_status) == 'cancelled') {
                                $delivery_status = '<span class="badge badge-soft-danger">' . ucwords($order->delivery_status) . '</span>';
                            } elseif (strtolower($order->delivery_status) == 'assign to rider') {
                                $delivery_status = '<span class="badge badge-soft-dark">' . ucwords($order->delivery_status) . '</span>';
                            } else {
                                $delivery_status = '<span class="badge badge-soft-info">' . ucwords($order->delivery_status) . '</span>';
                            }
                            $html .= '
                        <tr>
                            <td>' . $order->retailer->website . '</td>
                            <td>' . $order->order_number . '</td>
                            <td>' . $delivery_type . '</td>
                            <td>' . $order->shipping_charges . '</td>
                            <td>' . $delivery_status . '</td>
                            <td><a href="' . route('order.items', $order->id) . '" class="btn btn-primary btn-sm">Details</a></td>
                        </tr>';
                        }
                    }
                    $html .= '</tbody>';
                    // echo $html;
                    // die();
                    return $html;
                })
                ->addColumn('riders', function ($blocks) use ($riders) {
                    // dd($riders[0]->id);
                    $html = '<select class="form-select" onChange="changeRider(this, ' . $blocks->id . ')">';

                    $html .= '<option value="">Select Rider</option>';
                    foreach ($riders as $key => $r) {
                        $selected[$key] = '';
                        if (!is_null($blocks->user) && $blocks->user->id == $r->id) {
                            $selected[$key] = 'selected';
                        }
                        $html .= '<option data-block="' . $blocks->id . '" ' . $selected[$key] . ' value="' . $r->id . '">' . $r->name . ' | ' . $r->email . '</option>';
                    }
                    $html .= '</select>';
                    return $html;
                })
                ->addColumn('actions', function ($blocks) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a  href="" class="btn btn-primary btn-sm">Details</a>
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'orders_table', 'riders'])
                ->make(true);
        }

        return view('blocks.index');
    }

    public function chargesList(Request $request)
    {

        $list = RetailerChargesList::all();
        if ($request->ajax()) {
            return DataTables::of($list)
                ->addColumn('active', function ($discount) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($discount->active == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })

                ->addColumn('actions', function ($list) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('charges.items', $list->id) . '" class="btn btn-primary btn-sm">Items</a>
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'active'])
                ->make(true);
        }

        return view('chargesList.index');
    }

    public function addCharges()
    {
        return view('chargesList.edit');
    }

    public function insertCharges(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if ($request->has('active') && $request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }
        // dd($request->all());

        RetailerChargesList::create($request->all());
        return redirect(route('charges'))->with('success', 'Charges Added Successfully');
    }

    public function chargesItems($id, Request $request)
    {
        // $parent = RetailerChargesList::find()
        $data = RetailerChargesListItem::where('retailer_charges_list_id', $id)->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('package_name', function ($data) {
                    $shopifyPackage = $data->shopifyPackage;
                    if ($shopifyPackage) {
                        return $shopifyPackage->name ?? '';
                    }
                    return '';
                })
                ->addColumn('actions', function ($data) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('edit.charges.items', $data->id) . '" class="btn btn-primary btn-sm">Edit</a>
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'active', 'package_name'])
                ->make(true);
        }

        return view('chargesList.items.index', compact('id'));
    }

    public function addChargesItem($parent_id)
    {
        $packages = ShopifyPackage::where('status', 1)->get();
        return view('chargesList.items.edit', compact('parent_id', 'packages'));
    }

    public function insertChargesItem($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'max_volumetric_weight' => ['nullable', 'numeric'],
            'min_volumetric_weight' => ['nullable', 'numeric'],
            'price' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $new = new RetailerChargesListItem();
        $new->retailer_charges_list_id = $id;
        $new->max_volumetric_weight = $request->max_volumetric_weight;
        $new->min_volumetric_weight = $request->min_volumetric_weight;
        $new->price = $request->price;
        $new->shopify_package_id = $request->shopify_package_id;
        $new->save();

        return redirect(route('charges.items', $id))->with('success', 'Charges Item Added Successfully');
    }

    public function editChargesItem($id)
    {
        $item = RetailerChargesListItem::find($id);
        $packages = ShopifyPackage::where('status', 1)->get();
        return view('chargesList.items.edit', compact('item', 'packages'));
    }

    public function updateChargesItem($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'max_volumetric_weight' => ['nullable', 'numeric'],
            'min_volumetric_weight' => ['nullable', 'numeric'],
            'price' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $new = RetailerChargesListItem::find($id);
        // $new->retailer_charges_list_id = $id;
        $new->max_volumetric_weight = $request->max_volumetric_weight;
        $new->min_volumetric_weight = $request->min_volumetric_weight;
        $new->price = $request->price;
        $new->shopify_package_id = $request->shopify_package_id;
        $new->save();
        return redirect(route('charges.items', $new->retailer_charges_list_id))->with('success', 'Charges Item Updated Successfully');
    }

    public function generateLabelPrint($order_id)
    {

        $order = Orders::with('retailer.user', 'items', 'block', 'delivery_information')->find($order_id);
        $qr = $order['delivery_information']['order_qr_code'];
        // dd($order->toArray());
        $view = View('pdf.print_label', array('order' => $order->toArray(), 'qr' => $qr));
        $pdf = \App::make('dompdf.wrapper')->setPaper([0, 0, 283.465, 425.197], 'portrait');
        $pdf->loadHTML($view->render());
        return $pdf->stream(); // screenshot #2

    }

    public function generateItemsLabel($qr_code)
    {

        $items = Items::with('scan_info', 'order.retailer.user', 'order.zone')->whereHas('scan_info', function ($q) use ($qr_code) {
            $q->where('qr_code', $qr_code);
        })->get();
        // dd($items);
        $view = View('pdf.print_label', array('items' => $items->toArray(), 'qr' => $qr_code));
        $pdf = \App::make('dompdf.wrapper')->setPaper([0, 0, 283.465, 425.197], 'portrait');
        $pdf->loadHTML($view->render());
        // return $pdf->stream();
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="file.pdf"');
    }


    public function postalCodes(Request $request)
    {
        $data = PostalCode::all();

        if ($request->ajax()) {
            return DataTables::of($data)

                ->addColumn('zone', function ($data) {
                    // dd($data);
                    return $data->zone->name;
                })
                ->addColumn('actions', function ($data) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('postal-codes.edit', $data->id) . '" class="btn btn-primary btn-sm">Edit</a>
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'active'])
                ->make(true);
        }

        return view('postalcodes.index');
    }

    public function addPostalCode()
    {
        $zones = Zone::Active()->get();
        return view('postalcodes.edit', compact('zones'));
    }

    public function insertPostalCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone' => ['required'],
            'code' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        $postalCheck = PostalCode::where('zone_id', $request->zone)->where('postal', $request->code)->first();

        if (!is_null($postalCheck)) {
            return back()->with('error', 'Please try another code');
        }

        PostalCode::create([
            'zone_id' => $request->zone,
            'postal' => $request->code
        ]);
        return redirect(route('postal-codes'))->with('success', 'Postal Added Successfully');
    }

    public function editPostalCode($id, Request $request)
    {
        $zones = Zone::Active()->get();
        $postal = PostalCode::find($id);
        return view('postalcodes.edit', compact('zones', 'postal'));
    }

    public function updatePostalCode($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone' => ['required'],
            'code' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        PostalCode::find($id)->update([
            'zone_id' => $request->zone,
            'postal' => $request->code
        ]);
        return redirect(route('postal-codes'))->with('success', 'Postal Updated Successfully');
    }

    public function warehouses(Request $request)
    {
        $data = Warehouse::all();

        if ($request->ajax()) {
            return DataTables::of($data)


                ->addColumn('actions', function ($data) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('warehouse.edit', $data->id) . '" class="btn btn-primary btn-sm">Edit</a>
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'active'])
                ->make(true);
        }

        return view('warehouse.index');
    }


    public function addWarehouse()
    {
        return view('warehouse.edit');
    }

    public function insertWarehouse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'coordinates' => ['required'],
            'address' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Warehouse::create($request->all());
        return redirect(route('warehouse'))->with('success', 'Warehouse Added Successfully');
    }

    public function editWarehouse($id)
    {
        $warehouse = Warehouse::find($id);
        return view('warehouse.edit', compact('warehouse'));
    }

    public function updateWarehouse($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'coordinates' => ['required'],
            'address' => ['required'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Warehouse::find($id)->update($request->all());
        return redirect(route('warehouse'))->with('success', 'Warehouse Updated Successfully');
    }

    public function postCollector(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'order_id' => ['required'],
            // 'rider_id' => ['required'],
            'pickup_date_time' => ['required'],
            'per_hour_earning' => ['required'],
            'warehouse' => ['required']
            // 'pickup_coordinates' => ['required'],

        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }

        $warehouse = Warehouse::find($request->warehouse);
        $order_ids = json_decode($request->order_id);
        $request['pickup_date_time'] = date('Y-m-d H:i:s', strtotime($request->pickup_date_time));
        // dd($request->all());
        $block_number = (int) substr(time(), -6);

        $getPickupAddress = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $warehouse->coordinates . '&sensor=true&key=' . env('GOOGLE_MAP_KEY');

        $resPickupAddress = Http::get($getPickupAddress)->json();
        // dd($resPickupAddress);
        $orders = Orders::find($order_ids);
        // dd($orders);
        foreach ($orders as $order) {
            if (is_null($order->collector_distance)) {
                return back()->with('error', 'Please Verify distance first');
            }
        }
        if (!is_null($request->rider_id)) {

            foreach ($orders as $o) {
                if ($o->return == 2 && $o->return_delivery_status == 'In Warehouse') {
                    $o->return_delivery_status = 'Assign to Collector (Return to Retailer)';
                    $o->return = 3;
                    $o->save();
                    $o->statuses()->create([
                        'status' => 'Assign to Collector (Return to Retailer)',
                    ]);
                } else {
                    $o->collector_delivery_status = 'Assign to Collector';
                    $o->assigned_to_collector = 1;
                    $o->save();
                    $o->statuses()->create([
                        'status' => 'Assign to Collector',
                    ]);
                }
            }
        } else {
            foreach ($orders as $o) {
                if ($o->return == 2 && $o->return_delivery_status == 'In Warehouse') {
                    $o->return_delivery_status = 'Collection Created (Return to Retailer)';
                    $o->return = 3;
                    $o->save();
                    $o->statuses()->create([
                        'status' => 'Collection Created (Return to Retailer)',
                    ]);
                } else {
                    $o->collector_delivery_status = 'Collection Created';
                    $o->assigned_to_collector = 1;
                    $o->save();
                    $o->statuses()->create([
                        'status' => 'Collection Created',
                    ]);
                }
            }
        }

        $block = new Collection();
        if (!is_null($request->rider_id)) {
            $block->user_id = $request->rider_id;
        }
        $block->number = $block_number;
        $block->pickup_location = (isset($resPickupAddress['results'][0]['formatted_address'])) ? $resPickupAddress['results'][0]['formatted_address'] : null;
        $block->pickup_location_cordinates = $warehouse->coordinates;
        $block->pickup_date_time = $request->pickup_date_time;
        $block->per_hour_earning = $request->per_hour_earning;
        if ($request->has('return_collection') && $request->return_collection == 1) {
            $block->return = 1;
        }
        $block->save();
        $block->orders()->attach($order_ids);
        if (!is_null($request->rider_id)) {
            return redirect()->route('orders')->with('success', 'Order Assigned to collector Successfully');
        } else {
            return redirect()->route('orders')->with('success', 'Collection Created Successfully');
        }
        // dd($request->all(), $resPickupAddress);
    }
    public function postReturnBlock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required'],
            'rider_id' => ['required'],
            'pickup_date_time' => ['required'],
            'per_hour_earning' => ['required'],
            'warehouse' => ['required']
            // 'pickup_coordinates' => ['required'],

        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }
        $warehouse = Warehouse::find($request->warehouse);
        $order_ids = json_decode($request->order_id);
        $request['pickup_date_time'] = date('Y-m-d H:i:s', strtotime($request->pickup_date_time));
        // dd($request->all());
        $block_number = (int) substr(time(), -6);

        $getPickupAddress = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $warehouse->coordinates . '&sensor=true&key=' . env('GOOGLE_MAP_KEY');

        $resPickupAddress = Http::get($getPickupAddress)->json();
        // dd($resPickupAddress);
        $orders = Orders::find($order_ids);
        foreach ($orders as $order) {
            if (is_null($order->return_distance)) {
                return back()->with('error', 'Please Verify distance first');
            }
        }
        if (!is_null($request->rider_id)) {

            foreach ($orders as $o) {
                $o->return_delivery_status = 'Assign to Return Collector';

                $o->save();

                $o->statuses()->create([
                    'status' => 'Assign to Return Collector',
                ]);
            }
        } else {
            foreach ($orders as $o) {
                $o->return_delivery_status = 'Return Block Created';

                $o->save();
                $o->statuses()->create([
                    'status' => 'Return Block Created',
                ]);
            }
        }

        $block = new ReturnBlock();
        if (!is_null($request->rider_id)) {
            $block->user_id = $request->rider_id;
        }
        $block->number = $block_number;
        $block->pickup_location = (isset($resPickupAddress['results'][0]['formatted_address'])) ? $resPickupAddress['results'][0]['formatted_address'] : null;
        $block->pickup_location_cordinates = $warehouse->coordinates;
        $block->pickup_date_time = $request->pickup_date_time;
        $block->per_hour_earning = $request->per_hour_earning;
        $block->save();
        $block->orders()->attach($order_ids);
        if (!is_null($request->rider_id)) {
            return redirect()->route('orders')->with('success', 'Return Order Assigned to collector Successfully');
        } else {
            return redirect()->route('orders')->with('success', 'Return Block Created Successfully');
        }
        // dd($request->all(), $resPickupAddress);
    }

    public function indexCollections(Request $request)
    {
        $blocks = Collection::with('orders', 'user')->latest()->get();
        $collectors = User::with(['rider', 'rider.workingdays', 'rider.zones'])->whereRelation('roles', 'name', '=', 'Rider')->whereHas('rider', function ($query) {
            $query->where('is_collector', '=', 1);
        })->get();
        if ($request->ajax()) {
            return DataTables::of($blocks)
                ->addColumn('order_count', function ($blocks) {
                    return count($blocks->orders);
                })
                ->addColumn('created_at', function ($blocks) {
                    return date('F, j Y H:i a', strtotime($blocks->created_at));
                })
                ->addColumn('rider', function ($blocks) {
                    if (is_null($blocks->user_id)) {
                        return '';
                    }
                    return $blocks->user->email;
                })
                ->addColumn('orders_table', function ($blocks) {

                    $html = '<tbody>';

                    if (count($blocks->orders) > 0) {
                        foreach ($blocks->orders as $key => $order) {
                            $delivery_type = '<span class="badge badge-soft-primary">' . ucwords($order->order_type) . '</span>';
                            if ($order->order_type == 'express') {
                                $delivery_type = '<span class="badge badge-soft-success">' . ucwords($order->order_type) . '</span>';
                            }

                            $delivery_status = '<span class="badge badge-soft-primary">' . ucwords($order->collector_delivery_status) . '</span>';
                            $return_delivery_status = '<span class="badge badge-soft-info">' . ucwords($order->return_delivery_status) . '</span>';
                            if ($order->return == 3) {
                                $orderNumber = $order->order_number . ' <span class="badge badge-soft-danger">Return</span>';
                            } else {
                                $orderNumber = $order->order_number . '<span></span>';
                            }
                            $html .= '
                        <tr>
                            <td>' . $order->retailer->website . '</td>
                            <td>' . $orderNumber . '</td>
                            <td>' . $delivery_type . '</td>
                            <td>' . $order->shipping_charges . '</td>
                            <td>' . $delivery_status . '</td>
                            <td>' . $return_delivery_status . '</td>
                            <td><a href="' . route('order.items', $order->id) . '" class="btn btn-primary btn-sm">Details</a></td>
                        </tr>';
                        }
                    }
                    $html .= '</tbody>';
                    // echo $html;
                    // die();
                    return $html;
                })
                ->addColumn('collectors', function ($blocks) use ($collectors) {
                    // dd($riders[0]->id);
                    $html = '<select class="form-select" onChange="changeRider(this, ' . $blocks->id . ')">';

                    $html .= '<option value="">Select Collector</option>';
                    foreach ($collectors as $key => $r) {
                        $selected[$key] = '';
                        if (!is_null($blocks->user) && $blocks->user->id == $r->id) {
                            $selected[$key] = 'selected';
                        }
                        $html .= '<option data-block="' . $blocks->id . '" ' . $selected[$key] . ' value="' . $r->id . '">' . $r->name . ' | ' . $r->email . '</option>';
                    }
                    $html .= '</select>';
                    return $html;
                })
                ->addColumn('actions', function ($blocks) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a  href="" class="btn btn-primary btn-sm">Details</a>
                                            </div>';
                    return $actions;
                })
                ->addColumn('number', function ($blocks) {
                    $html = '<span>' . $blocks->number . '</span>';
                    if ($blocks->return == 1) {
                        $html .= '<span class="badge badge-soft-danger">Return Collection</span>';
                    }
                    return $html;
                })
                ->rawColumns(['actions', 'orders_table', 'collectors', 'number'])
                ->make(true);
        }

        return view('collections.index');
    }


    public function indexReturnBlocks(Request $request)
    {
        $blocks = ReturnBlock::with('orders', 'user')->latest()->get();
        // dd($blocks);
        $collectors = User::with(['rider', 'rider.workingdays', 'rider.zones'])->whereRelation('roles', 'name', '=', 'Rider')->whereHas('rider', function ($query) {
            $query->where('is_collector', '=', 1);
        })->get();
        if ($request->ajax()) {
            return DataTables::of($blocks)
                ->addColumn('order_count', function ($blocks) {
                    return count($blocks->orders);
                })
                ->addColumn('created_at', function ($blocks) {
                    return date('F, j Y H:i a', strtotime($blocks->created_at));
                })
                ->addColumn('rider', function ($blocks) {
                    if (is_null($blocks->user_id)) {
                        return '';
                    }
                    return $blocks->user->email;
                })
                ->addColumn('orders_table', function ($blocks) {

                    $html = '<tbody>';

                    if (count($blocks->orders) > 0) {
                        foreach ($blocks->orders as $key => $order) {
                            $delivery_type = '<span class="badge badge-soft-primary">' . ucwords($order->order_type) . '</span>';
                            if ($order->order_type == 'express') {
                                $delivery_type = '<span class="badge badge-soft-success">' . ucwords($order->order_type) . '</span>';
                            }

                            $delivery_status = '<span class="badge badge-soft-primary">' . ucwords($order->return_delivery_status) . '</span>';

                            $html .= '
                        <tr>
                            <td>' . $order->retailer->website . '</td>
                            <td>' . $order->order_number . '</td>
                            <td>' . $delivery_type . '</td>
                            <td>' . $order->shipping_charges . '</td>
                            <td>' . $delivery_status . '</td>
                            <td><a href="' . route('order.items', $order->id) . '" class="btn btn-primary btn-sm">Details</a></td>
                        </tr>';
                        }
                    }
                    $html .= '</tbody>';
                    // echo $html;
                    // die();
                    return $html;
                })
                ->addColumn('collectors', function ($blocks) use ($collectors) {
                    // dd($riders[0]->id);
                    $html = '<select class="form-select" onChange="changeRider(this, ' . $blocks->id . ')">';

                    $html .= '<option value="">Select Collector</option>';
                    foreach ($collectors as $key => $r) {
                        $selected[$key] = '';
                        if (!is_null($blocks->user) && $blocks->user->id == $r->id) {
                            $selected[$key] = 'selected';
                        }
                        $html .= '<option data-block="' . $blocks->id . '" ' . $selected[$key] . ' value="' . $r->id . '">' . $r->name . ' | ' . $r->email . '</option>';
                    }
                    $html .= '</select>';
                    return $html;
                })
                ->addColumn('actions', function ($blocks) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a  href="" class="btn btn-primary btn-sm">Details</a>
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'orders_table', 'collectors'])
                ->make(true);
        }

        return view('returnBlocks.index');
    }

    public function changeRider(Request $request)
    {
        // return $request->all();
        $status = 200;
        $block = Block::find($request->block);

        if (!empty($request->rider)) {
            $block->user_id = (int) $request->rider;

            if (count($block->orders) > 0) {
                foreach ($block->orders as $o) {
                    $o->delivery_status = 'Assigned to Rider';
                    $o->save();
                }
            }
        } else {
            $block->user_id = null;
            if (count($block->orders) > 0) {
                foreach ($block->orders as $o) {
                    $o->delivery_status = null;
                    $o->save();
                }
            }
            $status = 400;
        }
        $block->save();
        return response()->json(['status' => $status]);
    }

    public function changeCollector(Request $request)
    {
        // return $request->all();
        $status = 200;
        $block = Collection::with('orders')->find($request->block);

        if (!empty($request->rider)) {
            $block->user_id = (int) $request->rider;
            if (count($block->orders) > 0) {
                foreach ($block->orders as $o) {
                    $o->collector_delivery_status = 'Assigned to Collector';
                    $o->save();
                }
            }
        } else {
            $block->user_id = null;
            if (count($block->orders) > 0) {
                foreach ($block->orders as $o) {
                    $o->collector_delivery_status = null;
                    $o->save();
                }
            }
            $status = 400;
        }
        $block->save();
        return response()->json(['status' => $status]);
    }


    public function viewOnMap(Request $request)
    {
        if (!isset($request->rider) || !isset($request->order)) {
            abort(404);
        }

        $rider = Crypt::decrypt($request->rider);
        $order_id = Crypt::decrypt($request->order);

        $locationDB = RidersLocation::where('rider_id', $rider)->first();
        if (isset($locationDB) && !empty($locationDB)) {
            $current = $locationDB->coordinates;
        } else {
            abort(404);
        }

        $order = Orders::select('id', 'order_number', 'dropoff_coordinates', 'enduser_name', 'enduser_email', 'enduser_mobile', 'enduser_address', 'enduser_ordernotes', 'delivery_status')->find($order_id);

        if (is_null($order)) {
            abort(404);
        }

        if (strtolower($order->delivery_status) === 'delivered') {
            abort(404);
        }

        // if($order->is_grocery == 1){
        // $groceryData = config('app.grocery_url')."api/order/details/$order->order_number";
        // $groceryData = config('app.grocery_url')."api/order/details/228990";
        // $responseGrocery = Http::get($groceryData)->json();
        // if(isset($responseGrocery) && !empty($responseGrocery)){
        //     $dataG = $responseGrocery['data']['order_info'];
        // } else {
        //     $dataG = [];
        // }
        // if(isset($dataG) && !empty(($dataG))){
        //     $order->grocery_order = collect((object)$dataG);
        //     // dd($order);
        // }
        // }
        // dd($order->grocery_order['items']);
        $location = [];

        $getDistance = 'https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&units=imperial&origins=' . $current . '&destinations=' . $order['dropoff_coordinates'] . '&key=' . env('GOOGLE_MAP_KEY');
        $responsedis = Http::get($getDistance)->json();
        $time_per_order = isset($responsedis['rows'][0]['elements'][0]['duration_in_traffic']['text']) ? $responsedis['rows'][0]['elements'][0]['duration_in_traffic']['text'] : 0;

        array_push($location, $order['dropoff_coordinates']);
        $location = json_encode($location);

        $riderInfo = Rider::with('user')->find($rider);
        return view('livelocation', compact('current', 'rider', 'location', 'time_per_order', 'order', 'riderInfo'));
    }


    public function zones(Request $request)
    {
        $data = Zone::all();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('status', function ($data) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($data->active == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })
                ->addColumn('actions', function ($data) {
                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('zones.edit', $data->id) . '" class="btn btn-primary btn-sm">Edit</a>
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view('zones.index');
    }

    public function addZone()
    {
        return view('zones.edit');
    }

    public function insertZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'unique:zones'],
        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }

        // dd($request->all());

        if ($request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }
        $new = new Zone();
        $new->name = $request->name;
        $new->active = $request->active;
        $new->save();
        $postals = [];
        if (is_countable($request->postal) && count($request->postal) > 0) {
            foreach ($request->postal as $key => $p) {
                // $postals[$key]['postal'] = $p;
                // $postals[$key]['zone_id'] = $new->id;
                // dd($postals);
                $Post[$key] = new PostalCode();
                $Post[$key]->postal = $p;
                $Post[$key]->zone_id = $new->id;
                $Post[$key]->save();
            }
            // $new->postalcodes()->create($postals);
        }
        return redirect(route('zones'))->with('success', 'Zone Created');
    }

    public function editZone($id)
    {
        $zone = Zone::find($id);
        return view('zones.edit', compact('zone'));
    }

    public function updateZone($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'unique:zones,name,' . $id],
        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }

        if ($request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }
        $new = Zone::find($id);
        $new->name = $request->name;
        $new->active = $request->active;
        $new->save();
        $new->postalcodes()->delete();

        if ($request->hasFile('file')) {
            $file = $request->file;
            Excel::import(new PostalImport($id), $file);
        }

        if (is_countable($request->postal) && count($request->postal) > 0) {
            foreach ($request->postal as $key => $p) {
                // $postals[$key]['postal'] = $p;
                // $postals[$key]['zone_id'] = $new->id;
                // dd($postals);
                $Post[$key] = new PostalCode();
                $Post[$key]->postal = $p;
                $Post[$key]->zone_id = $new->id;
                $Post[$key]->save();
            }
            // $new->postalcodes()->create($postals);
        }
        return redirect(route('zones'))->with('success', 'Zone Updated');
    }

    public function deleteZone($id)
    {

        $zone = Zone::with('postalcodes')->find($id);
        $postalIDs = $zone->postalcodes->toArray();
        dd($postalIDs);
    }

    public function importPostalToZones($zone_id, Request $request)
    {
        // dd($zone_id);
        $file = $request->file;
        // set_time_limit(1200);
        // ini_set('memory_limit', '256M');
        // ini_set('max_execution_time', 180);


        Excel::import(new PostalImport($zone_id), $file);
        return redirect()->back()->with('success', 'Postal Imported');
    }


    public function getallDocumentTypes(Request $request)
    {
        $orderType = OrderType::all();
        if (!is_null($orderType)) {
            return response()->json(
                ['data' => $orderType, 'status' => 200],
                200
            );
        }
        return response()->json(
            [
                'message' => 'Document type not found',
                'status' => 201
            ],
            201
        );
    }

    public function importZones(Request $request)
    {
        set_time_limit(1200);
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 180);

        $file = $request->file;
        // set_time_limit(1200);
        // ini_set('memory_limit', '256M');
        // ini_set('max_execution_time', 180);
        Excel::import(new ZoneImport(), $file);

        return 'Done';
    }


    // Buiness Hours CRUD for the platform

    public function businessHoursView()
    {

        $businessHours = BusinessHour::get()->keyBy('day')->toArray();
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $businessHoursData = [];
        foreach ($daysOfWeek as $day) {
            $openTime = isset($businessHours[$day]) ? $businessHours[$day]['open_time'] : null;
            $breakTime = isset($businessHours[$day]) ? $businessHours[$day]['break_time_start'] : null;
            $breakTimeEnd = isset($businessHours[$day]) ? $businessHours[$day]['break_time_end'] : null;
            $closeTime = isset($businessHours[$day]) ? $businessHours[$day]['close_time'] : null;

            $businessHoursData[$day] = [
                'open_time' => $openTime ? date('H:i:s', strtotime($openTime)) : '',
                'break_time_start' => $breakTime ? date('H:i:s', strtotime($breakTime)) : '',
                'break_time_end' => $breakTimeEnd ? date('H:i:s', strtotime($breakTimeEnd)) : '',
                'close_time' => $closeTime ? date('H:i:s', strtotime($closeTime)) : '',
            ];
        }
        return view('business-hours.index', compact('businessHoursData'));
    }


    public function addBusinessHours(Request $request)
    {

        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            $businessHour = BusinessHour::where('day', $day)->first();
            if (!$businessHour) {
                $businessHour = new BusinessHour();
                $businessHour->time_zone = $request->time_zone;
                $businessHour->day = $day;
            }
            $businessHour->open_time = $request->input($day . '_open_time');
            $businessHour->break_time_start = $request->input($day . '_break_time');
            $businessHour->break_time_end = $request->input($day . '_break_time_end');
            $businessHour->close_time = $request->input($day . '_close_time');
            $businessHour->save();
        }

        return redirect()->back()->with('success', 'Business hours saved.');
    }
    // Buiness Hours CRUD for the platform

    public function shopifyPackage(Request $request)
    {
        $data = ShopifyPackage::all();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('status', function ($data) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($data->status == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })
                ->addColumn('actions', function ($data) {
                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                    <a href="' . route('edit.shopify.package', $data->id) . '" class="btn btn-primary btn-sm">Edit</a>
                                                </div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view('shopify.packages.index');
    }

    public function formShopifyPackage()
    {
        $businessHours = ShopifyBusinessHour::get()->keyBy('day')->toArray();
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $businessHoursData = [];
        foreach ($daysOfWeek as $day) {
            $openTime = isset($businessHours[$day]) ? $businessHours[$day]['open_time'] : null;
            $breakTime = isset($businessHours[$day]) ? $businessHours[$day]['break_time_start'] : null;
            $breakTimeEnd = isset($businessHours[$day]) ? $businessHours[$day]['break_time_end'] : null;
            $closeTime = isset($businessHours[$day]) ? $businessHours[$day]['close_time'] : null;

            $businessHoursData[$day] = [
                'open_time' => $openTime ? date('H:i:s', strtotime($openTime)) : '',
                'break_time_start' => $breakTime ? date('H:i:s', strtotime($breakTime)) : '',
                'break_time_end' => $breakTimeEnd ? date('H:i:s', strtotime($breakTimeEnd)) : '',
                'close_time' => $closeTime ? date('H:i:s', strtotime($closeTime)) : '',
            ];
        }

        return view('shopify.packages.edit', compact('businessHoursData'));
    }

    public function addShopifyPackage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'price' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }

        if ($request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }
        $new = new ShopifyPackage();
        $new->name = $request->name;
        $new->price = $request->price;
        $new->status = $request->active;
        $new->save();

        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {

            $businessHour = new ShopifyBusinessHour();
            $businessHour->time_zone = $request->time_zone;
            $businessHour->shopify_package_id = $new->id;
            $businessHour->day = $day;
            $businessHour->open_time = $request->input($day . '_open_time');
            $businessHour->break_time_start = $request->input($day . '_break_time');
            $businessHour->break_time_end = $request->input($day . '_break_time_end');
            $businessHour->close_time = $request->input($day . '_close_time');
            $businessHour->save();
        }
        return redirect()->route('index.shopify.package')->with('success', 'Package added successfully');
    }


    public function editShopifyPackage($id)
    {

        $type = ShopifyPackage::find($id);

        $businessHours = ShopifyBusinessHour::where('shopify_package_id', $type->id)->get()->keyBy('day')->toArray();
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $businessHoursData = [];
        foreach ($daysOfWeek as $day) {
            $openTime = isset($businessHours[$day]) ? $businessHours[$day]['open_time'] : null;
            $breakTime = isset($businessHours[$day]) ? $businessHours[$day]['break_time_start'] : null;
            $breakTimeEnd = isset($businessHours[$day]) ? $businessHours[$day]['break_time_end'] : null;
            $closeTime = isset($businessHours[$day]) ? $businessHours[$day]['close_time'] : null;

            $businessHoursData[$day] = [
                'open_time' => $openTime ? date('H:i:s', strtotime($openTime)) : '',
                'break_time_start' => $breakTime ? date('H:i:s', strtotime($breakTime)) : '',
                'break_time_end' => $breakTimeEnd ? date('H:i:s', strtotime($breakTimeEnd)) : '',
                'close_time' => $closeTime ? date('H:i:s', strtotime($closeTime)) : '',
            ];
        }
        return view('shopify.packages.edit', compact('type', 'businessHoursData'));
    }


    public function updateShopifyPackage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('validerrors', $validator->errors()->all())->withInput();
        }

        if ($request->active == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }

        $new = ShopifyPackage::find($id);
        $new->name = $request->name;
        $new->price = $request->price;
        $new->status = $request->active;
        $new->save();

        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            $businessHour = ShopifyBusinessHour::where('shopify_package_id', $id)->where('day', $day)->first();
            if (!$businessHour) {
                $businessHour = new ShopifyBusinessHour();
                $businessHour->time_zone = $request->time_zone;
                $businessHour->shopify_package_id = $id;
                $businessHour->day = $day;
            }
            $businessHour->open_time = $request->input($day . '_open_time');
            $businessHour->break_time_start = $request->input($day . '_break_time');
            $businessHour->break_time_end = $request->input($day . '_break_time_end');
            $businessHour->close_time = $request->input($day . '_close_time');
            $businessHour->save();
        }

        return redirect()->route('index.shopify.package')->with('success', 'Package updated successfully');
    }


    public function AuthLogs($id)
    {

        $user = User::with('auth_logs')->find($id);

        return view('auth.logs', compact('user'));
    }


    public function exportOrders(Request $request)
    {
        $baseQuery = Orders::query()->latest();
        $type = strtolower($request->type);

        if (isset($type) && !empty($type) && in_array($type, ['50', '100', '200'])) {
            $baseQuery->limit($type);
        }

        $orders = $baseQuery->get();

        // Format data for CSV export
        $csvData = [];

        // Header row
        $headerRow = [
            'Customer Name',
            'Customer Email',
            'Address',
            'Zone',
            'Date',
        ];

        $csvData[] = $headerRow;

        // Data rows
        foreach ($orders as $order) {
        $rowData = [
            ucfirst($order?->enduser_name),
            $order?->enduser_email,
            $order?->enduser_address,
            $order?->zone?->name,
            $order->created_at->format('d/m/Y g:i:s a'),
        ];
            $csvData[] = $rowData;
        }

        // Create CSV file
        $fileName = 'Orders_Export' . date('Y-m-d_H-i-s') . '.csv';
        $file = fopen(storage_path('app/' . $fileName), 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        // Download the CSV file
        return Response::download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }
}
