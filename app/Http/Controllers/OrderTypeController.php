<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\OrderType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class OrderTypeController extends Controller
{
    public function index(Request $request)
    {
        $pkg = OrderType::all();
        if ($request->ajax()) {
            return DataTables::of($pkg)
                ->addColumn('image', function ($pkg) {;
                    $image =      ($pkg->image);
                    return "<img  src='" . $image . "'  width='100px' height='auto' />";
                })

                ->addColumn('status', function ($pkg) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($pkg->active == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })

                ->addColumn('actions', function ($pkg) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('ordertype.edit', $pkg->id) . '" class="btn btn-primary btn-sm">Edit</a>
                                            
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['image',  'actions', 'status'])

                ->make(true);
        }
        return view("ordertype.index");
    }

    /* View All Packages */
    public function viewPackages()
    {
        try {
            $Packages = OrderType::where('is_active', 1)->get();
            return response()->json(['status' => 200, 'Packages' => $Packages], 200);
        } catch (Throwable $th) {
            return response()->json(['status' => 402, 'Error' => $th->getMessage()], 402);
        }
    }

    /* Store Package Information */
    public function storePkgInfo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required|image|mimes:svg,png,jpg,jpeg,gif'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move((public_path('OrderTypes')), $imageName);

        if ($request['active'] == 'on') {
            $is_active  = 1;
        } else {
            $is_active = 0;
        }

        OrderType::create([
            'name' => $request->name,
            'image' => url('OrderTypes/' . $imageName),
            'active' => $is_active
        ]);
        return redirect(route('order_types'))->with('success', 'Order Type Added Successfully.');
    }

    public function add()
    {
        return view("ordertype.edit");
    }

    public function edit($pkgid)
    {
        $pkgDetail =  OrderType::find($pkgid);
        $image =  $pkgDetail->image;
        return view("ordertype.edit", compact('pkgDetail', 'image'));
    }


    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if ($request['active'] == 'on') {
            $request['active'] = 1;
        } else {
            $request['active'] = 0;
        }

        OrderType::find($id)->update($request->all());
        return redirect(route('order_types'))->with('success', 'Package Updated Successfully');
    }
}
