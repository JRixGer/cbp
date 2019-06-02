<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;


class NoAccessController extends Controller
{

    public function index()
    {
        return Response()->json(['status' => false, 'message' => 'You are not authorized to access in this portal.']); 
    }

    public function noAaccess()
    {
		return view('layouts.no_access');
    }
   

}

