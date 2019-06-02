<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use cbp\Http\Requests\UpdateProfile;
use cbp\Http\Requests\UpdateEmail;
use cbp\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */

    // public function edit(Request $request, User $user)
    // {
    //     // user
    //     $viewData = [
    //         'user' => $user,
    //     ];
    //     // render view with data
    //     return view('profile.edit', $viewData);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    
    public function update(UpdateProfile $request)
    {

        // form data
        $data = $request->all();

        User::where('id', $data['id'])
          ->update(['password' =>  Hash::make($data['password'])]);

        return Response()->json(['status'=>true, 'info' => 'Your profile has been updated successfully.', 'data' => $data]);

        // return redirect(route('profile.edit', ['user' => $user]))
        //         ->with('info', 'Your profile has been updated successfully.');
    }

    public function updateEmail(UpdateEmail $request)
    {

        // form data
        $data = $request->all();

        User::where('id', $data['id'])
          ->update(['email' => $data['email']]);

        return Response()->json(['status'=>true, 'info' => 'Your profile has been updated successfully.', 'data' => $data]);

        // return redirect(route('profile.edit', ['user' => $user]))
        //         ->with('info', 'Your profile has been updated successfully.');
    }

    
}