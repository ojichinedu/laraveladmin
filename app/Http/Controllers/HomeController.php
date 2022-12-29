<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $user=Auth::user()->email;
        $activity= Activity::where('email','=',$user)->orderBy('created_at','DESC')->paginate(7)->withQueryString();


        return view('home',compact('user','activity'));
    }

    public function profile()
    {
        return view('profile');

    }

    public function profileupdate(Request $request)
    {



        $request->validate([
            'brief_info' => 'required|min:3|max:255',
            'name' => 'required|min:3|max:50',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|string|email',
            'profilepicture' => 'nullable|image',
        ]); 

        $user= User::find(Auth()->id());
        $user->name=$request->name;
        $user->brief_info=$request->brief_info;
        $user->address=$request->address;
        $user->phonenumber=$request->phone;
        $user->email=$request->email;
        
        if($request->hasFile('profilepicture'))
        {
          
            $photo=Auth::user()->name.".".$request->profilepicture->getClientOriginalExtension();
            if(file::exists($photo))
            {
              unlink($photo);
            }
            $request->profilepicture->move(public_path('profilepicture'), $photo);
            $user->profilepicture = $photo;
        }

          $user->update();

          return back()->with('success', 'Profile updated successfully.');


    }

    public function passwordupdate(Request $request)
    {

        if(!(Hash::check($request->get('password'), Auth::user()->password)))
        {
          return back()->with("error","Your Current password does not match with the password you provided. Please try again.");
        }
        if(Hash::check($request->get('newpassword'), Auth::user()->password))
        {
          return back()->with("error","New Password cannot be the same as your current Password. Please choose a different password.");
        }
        $request->validate([
          'newpassword'=>'required|min:6|confirmed',
          'newpassword_confirmation'=>'required|min:6',

        ]);
        $pass=Hash::make($request->newpassword);
        $user= User::find(auth()->id());
        $user->password=$pass;
        $user->update();
        
        return back()->with('success', 'Password updated successfully.');







    }





}
