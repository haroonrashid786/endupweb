<?php

namespace App\Http\Controllers;

use App\Classes\FilePaths;
use App\Helpers\OptimizeTrait;
use App\Models\Retailer;
use App\Models\RetailerDocument;
use Illuminate\Http\Request;

class RetailerDocumentController extends Controller
{
    protected $view = 'retailers_documents';
    protected $parentRoute = 'retailer.documents';
    protected $filePath = FilePaths::retailerDocuments;

    public function index($retailer_id)
    {
        $retailer = Retailer::find($retailer_id);
        $docs = RetailerDocument::where('retailer_id', $retailer_id)->get();
        return view("$this->view.index", compact('docs', 'retailer'));
    }

    public function uploadDoc($id, Request $request)
    {

        $checkRetailer = Retailer::find($id);

        if (empty($checkRetailer)) {
            return redirect()->back()->with('error', 'Retailer not found');
        }
        $FILENAME = OptimizeTrait::uploadFile($this->filePath, $request->file, 1);
        $new = new RetailerDocument();
        $new->name = $request->name;
        $new->retailer_id = $id;
        $new->path = (isset($FILENAME) && !empty($FILENAME)) ? $FILENAME : null;
        $new->save();
        return redirect(route($this->parentRoute, $id))->with('success', 'Document uploaded successfully');
        // dd($request->all());
    }

    public function delete($id){
        $doc = RetailerDocument::where('id', $id)->first();
        if(isset($doc) && !empty($doc)){
            $doc->delete();
            return redirect()->back()->with('success', 'Document Deleted');
        }
        return redirect()->back()->with('error', 'File not found');
    }

}
