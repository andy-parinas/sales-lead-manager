<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends ApiController
{

//    use AuthenticatesUsers;
//
//    /**
//     * Where to redirect users after login.
//     *
//     * @var string
//     */
//    protected $redirectTo = RouteServiceProvider::HOME;
//
//    /**
//     * Create a new controller instance.
//     *
//     * @return void
//     */
//    public function __construct()
//    {
//        $this->middleware('guest')->except('logout');
//    }
//
//    public function username()
//    {
//        return 'username';
//    }

    public function login(Request $request){

        $loginData = $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if(Auth::attempt($loginData)){
            return response()->json(Auth::user(), Response::HTTP_OK);
        }

        return $this->errorResponse("Invalid Username or Password", Response::HTTP_UNAUTHORIZED);

    }
}
