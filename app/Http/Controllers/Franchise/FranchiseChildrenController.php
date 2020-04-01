<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FranchiseChildrenController extends ApiController
{

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Franchise $franchise)
    {

        $this->authorize('view', $franchise);

        $children = $franchise->children;

        return $this->showAll($children);        

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Franchise $franchise)
    {
        
        $this->authorize('create', $franchise);

        $rules = [
            'number' => 'required',
            'name' => 'required'
        ];

        $this->validate($request, $rules);

        $createdChild = $franchise->children()->create($request->all());

        return $this->showOne($createdChild, Response::HTTP_CREATED);
    }

  
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
