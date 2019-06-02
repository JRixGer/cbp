<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use \App\Mail\Verification;
use Mail;
use Illuminate\Support\Facades\Session;
use DB;

class MessageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function getMessages()
    {

        $user_email = auth()->User()->email;
        $noti_qry = DB::table('messages')
            ->select('messages.id',
                    'messages.type',
                    'messages.title',
                    'messages.body',
                    'messages.created_at',
                    'messages.updated_at',
                    'message_recipients.email',
                    'message_recipients.id as message_recipients_id'
            )
            ->leftjoin('message_recipients', 'message_recipients.message_id', '=', 'messages.id')
            ->where('message_recipients.email', '=', $user_email)
            ->where('message_recipients.is_marked_as_read', '=', 0)
            ->orWhere(function ($query) {
                $query->Where('message_recipients.email', '=', 'all')
                      ->where('message_recipients.is_marked_as_read', '=', 0);
            })            
            ->orderBy('messages.id', 'DESC')->get();

        if($noti_qry){
            return ['status' => true, 'noti' => $noti_qry];
        }else{
            return ['status' => false, 'noti' => null];
        }
    }

    public function markRead(Request $r)
    {

        $status = DB::table('message_recipients')
                ->where('id', $r->message_recipients_id)
                ->update(['is_marked_as_read' => 1]);    

        return $status;
    }
}
