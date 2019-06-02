<?php

namespace cbp\Http\Controllers;

class LogoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function logout () {
        //logout user
        auth()->logout();
        // redirect to homepage
        //return redirect('/login');
    }

}
