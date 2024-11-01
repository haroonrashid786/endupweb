<?php

namespace App\Http\Controllers;

use App\Helpers\OptimizeTrait;
use App\Mail\GenerateTicket;
use App\Models\RetailerTicket;
use App\Models\Ticket;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Validator;
use Yajra\DataTables\DataTables;

class TicketController extends Controller
{

    protected $view = 'tickets';
    public $parentRoute = 'tickets';
    public function index(Request $request)
    {
        if(Auth::user()->isRetailer()){
            $tickets = RetailerTicket::with('user.retailer:id,user_id,website')->where('user_id', Auth::id())->latest()->get();
        } else {
            if(Auth::user()->isAdmin()){
                $tickets = RetailerTicket::with('user.retailer:id,user_id,website')->latest()->get();
            }
        }

        if ($request->ajax()) {
            return DataTables::of($tickets)

                ->addColumn('retailer', function ($tickets) {
                    $retailer = '';
                    if (isset($tickets->user->retailer) && !empty($tickets->user->retailer)) {
                        $retailer = $tickets->user->retailer->website;
                    }

                    return $retailer;
                })
                ->addColumn('actions', function ($tickets) {

                    $actions = '<div class="btn-group-sm btngroupcst" role="group" aria-label="Basic example">
                                                <a href="' . route('edit.rider', $tickets->id) . '" class="btn btn-primary">Edit</a>


                                            </div>';
                    return $actions;
                })
                ->rawColumns(['status', 'actions', 'type'])
                ->make(true);
        }
        return view("$this->view.index");
    }

    public function add(){
        return view("$this->view.edit");
    }
    public function generateTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'message' => 'required',
        ]);

        $user = Auth::user();

        $request['user_id'] = $user->id;
        $request['ticket_id'] = OptimizeTrait::generateUniqStr();

        // dd($request->all());

        // $msg = $request->message;
        // $user_name = $user->firstname . " " . $user->lastname;
        // $user_phone_number = $user->number;
        // $mail_subject = $request->subject;
        // Mail::to(env('ADMIN_EMAIL'))->send(new GenerateTicket($msg, $mail_subject, $user_name, $user_phone_number));
        $user->retailer_tickets()->create(
            $request->all()
        );
        return redirect(route($this->parentRoute))->with('success', 'Ticket Generated Successfully');
    }
}
