<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Franchise as FranchiseResource;

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

            $user = Auth::user();
//            $franchises = $user->franchises->pluck('franchise_number', 'id')->toArray();
            $franchises = $user->franchises;

            return response()->json(['data' => Auth::user(), 'franchises' => FranchiseResource::collection($franchises) ], Response::HTTP_OK);
        }

        return $this->errorResponse("Invalid Username or Password", Response::HTTP_UNAUTHORIZED);

    }

    public function logout(Request $request){

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response('', Response::HTTP_NO_CONTENT);

//        Auth::guard('web')->logout();


    }
}
