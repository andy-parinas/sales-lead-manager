<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FranchiseController extends ApiController
{

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = Auth::user();
        
        if($user->can('viewAny', Franchise::class))
        {
            $franchises = Franchise::all();
            return $this->showAll($franchises);

        }
        else
        {
            $franchises = $user->franchises;

            return $this->showAll($franchises);
        }

        
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Franchise::class);

        $rules = [
            'number' => 'required',
            'name' => 'required',
        ];

        $this->validate($request, $rules);

        $franchise = Franchise::create($request->all());


        return $this->showOne($franchise, Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Franchise $franchise)
    {

        $this->authorize('view', $franchise);

        return $this->showOne($franchise);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Franchise $franchise)
    {

        $this->authorize('update', $franchise);
        
        $franchise->update($request->all());

        return $this->showOne($franchise);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Franchise $franchise)
    {
        $this->authorize('delete', $franchise);

        $franchise->delete();

        return $this->showOne($franchise);
        
    }
}
