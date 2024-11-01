<?php

namespace App\Http\Controllers;

use App\Events\Test;
use App\Models\Package;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PackageController extends Controller
{

    public function index(Request $request)
    {
        $pkg = Package::all();
        if ($request->ajax()) {
            return DataTables::of($pkg)
                ->addColumn('weight', function ($pkg) {
                    return strtoupper($pkg->weight);
                })
                ->addColumn('image', function ($pkg) {
                    $image = url('packages/' . $pkg->image);
                    return "<img  src='" . $image . "'  width='100px' height='auto' />";
                })
                ->addColumn('length', function ($pkg) {
                    if (isset($pkg->length)) {
                        return $pkg->length;
                    } else {
                        return "";
                    }
                })

                ->addColumn('width', function ($pkg) {
                    if (isset($pkg->width)) {
                        return ucwords($pkg->width);
                    } else {

                        return '';
                    }
                })
                ->addColumn('height', function ($pkg) {
                    if (isset($pkg->height)) {
                        return ucwords($pkg->height);
                    } else {

                        return '';
                    }
                })
                ->addColumn('status', function ($pkg) {
                    $status = '<span class="badge badge-pill badge-soft-danger font-size-11">InActive</span>';
                    if ($pkg->is_active == 1) {
                        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                    }
                    return $status;
                })

                ->addColumn('actions', function ($pkg) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('package.edit', $pkg->id) . '" class="btn btn-primary btn-sm">Edit</a>
                                            
                                            </div>';
                    return $actions;
                })
                ->rawColumns(['image',  'actions', 'status'])

                ->make(true);
        }
        return view("packages.index");
    }

    /* View All Packages */
    public function viewPackages()
    {
        try {
            $Packages = Package::where('is_active', 1)->get();
            return response()->json(['status' => 200, 'Packages' => $Packages], 200);
        } catch (Throwable $th) {
            return response()->json(['status' => 402, 'Error' => $th->getMessage()], 402);
        }
    }

    /* Store Package Information */
    public function storePkgInfo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'weight' => 'required',
            'image' => 'required|image|mimes:svg,png,jpg,jpeg,gif|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('packages'), $imageName);
        if ($request['is_active'] == 'on') {
            $is_active  = 1;
        } else {
            $is_active = 0;
        }
        try {
            Package::create([
                'weight' => $request->weight,
                'image' => $imageName,
                "length" => $request->length,
                "width" => $request->width,
                "height" => $request->height,
                'is_active' => $is_active
            ]);
            return redirect(route('packages'))->with('success', 'Package Added Successfully.');
        } catch (\Throwable $th) {
            return  redirect(route('packages'))->with('error', 'Error' . $th->getMessage());
        }
    }

    public function add()
    {
        return view("packages.edit");
    }

    public function edit($pkgid)
    {
        $pkgDetail =  Package::find($pkgid);
        $image =  $pkgDetail->image;


        return view("packages.edit", compact('pkgDetail', 'image'));
    }


    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'weight' => ['required'],
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if ($request['is_active'] == 'on') {
            $request['is_active'] = 1;
        } else {
            $request['is_active'] = 0;
        }
        Package::find($id)->update($request->all());
        return redirect(route('packages'))->with('success', 'Package Updated Successfully');
    }
}
