<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserCollection;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends ApiController
{

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->middleware('auth:sanctum');
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('head-office-only');

        $users = $this->userRepository->findUsersSortedAndPaginated($this->getRequestParams());

        return $this->showApiCollection(new UserCollection($users));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Gate::authorize('head-office-only');

        $this->validate($request, [
            'username' => ['required', 'string', 'max:50','unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users' ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required']
        ]);


        $user = User::create([
            'username' => $request['username'],
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'user_type' => $request['user_type']
        ]);

        // dd($user->user_type);

        return $this->showOne(new UserResource($user), Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        Gate::authorize('head-office-only');

        $user = User::with('franchises')->findOrFail($id);

        return $this->showOne(new UserResource($user));;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        Gate::authorize('head-office-only');

        $user = User::findOrFail($id);

        $data = $this->validate($request, [
            'username' => ['string', 'max:50'],
            'name' => ['string', 'max:255'],
            'email' => ['string', 'email', 'max:255'],
            'password' => ['string', 'min:8', 'confirmed'],
            'user_type' => ''
        ]);

        $user->update($data);

        $user->refresh();

        return $this->showOne(new UserResource($user));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Gate::authorize('head-office-only');

        $user = User::findOrFail($id);

        //Check for the franchises attached to the user.
        $franchises = $user->franchises()->pluck('id');

        $user->franchises()->detach($franchises);

        $user->delete();

        return $this->showOne($user);
    }
}
