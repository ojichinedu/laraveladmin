<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'address' => ['required'],
            'phonenumber' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'id_card' => ['required'],
            'brief_info' => ['required','min:8'],

        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
       $user= User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phonenumber' => $data['phonenumber'],
            'password' => Hash::make($data['password']),
            'brief_info' => $data['brief_info'],

        ]);

        if(request()->hasFile('id_card')){
          
            $id_card=request()->email.".".request()->id_card->getClientOriginalExtension();
            if(file::exists($id_card))
            {
              unlink($id_card);
            }
            request()->id_card->move(public_path('idcard'), $id_card);
            $user->id_card= $id_card;
            $user->update(['id_card'=>$id_card]);
          }

        return $user;
    }
}
