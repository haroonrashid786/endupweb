<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use http\Client\Curl\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {

        $input = $request->all();

        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = \App\Models\User::where($fieldType, $input['email'])->first();

        if (!is_null($user)){
            if (Hash::check($input['password'], $user->password->password)){
                $IP = ($request->has('ip_address') && !empty($request->ip_address)) ? $request->ip_address : null;
                $country = ($request->has('country') && !empty($request->country)) ? $request->country : null;
                $city = ($request->has('city') && !empty($request->city)) ? $request->city : null;

                $user->auth_logs()->create([
                    'ip' => $IP,
                    'city' => $city,
                    'country' => $country,
                ]);
                Auth::login($user);
            }
        }
        if (auth()->attempt(array($fieldType => $input['email'], 'password' => $input['password']))) {

            return redirect()->route('home');

        } else {

            return redirect()->route('login')
                ->with('error', 'Email-Address And Password Are Wrong.')->withInput();

        }


    }
}
