<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use \App\Mail\Verification;
use Mail;
use Illuminate\Support\Facades\Session;
use DB;

class HomeController extends Controller
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
    public function index()
    {


        $arr = array();
        $arr['isAbove800'] =  (auth()->User()->is_above_maximum)? 'allow':'';
        $arr['isPrintPostage'] =  (auth()->User()->is_print_postage)? 'allow':'';
        $arr['isPrintInvoice'] =  (auth()->User()->is_print_invoice)? 'allow':'';
        $arr['isPrintBillOfLading'] =  (auth()->User()->is_print_billoflading)? 'allow':'';
        $arr['isDownload'] =  (auth()->User()->is_download_reports)? 'allow':'';

 
        //return view('layouts.app', ['access' => $arr]); 

        if(auth()->user()->is_admin == 1 && Session::get('loggedFrom') == 'portal')
        {
            return redirect('staff_portal');
        }
        else if(auth()->user()->is_admin == 0 && Session::get('loggedFrom') == 'customer')
        {
            return view('layouts.app', ['access' => $arr]);
        }
        else
        {
            //auth()->logout();
            //return view('layouts.no_access');
            return view('layouts.app', ['access' => $arr]);
        }

        
    }

    public function admin()
    {
        //return view('layouts.admin');
        return redirect()->route('user.access');
    }

}
