<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Postcode;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FranchisePostcodeController extends ApiController
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

        $postcodes = $franchise->postcodes;

        return $this->showAll($postcodes);

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
            'postcodes' => 'required|array'
        ];

        $this->validate($request, $rules);

        $postcodes = $request['postcodes'];

        //Check if the Postcode is within the Parent Franchise
        $parent = $franchise->parent;

        if($parent !== null){

            $parentPostcodes = $parent->postcodes->pluck('id')->toArray();
            
            foreach ($postcodes as $postcode) {

                if(!in_array($postcode, $parentPostcodes)){

                    return $this->errorResponse("Some Postcode is not within the Parent Postcodes", Response::HTTP_BAD_REQUEST);
                }
            }
         
        }

        $franchise->postcodes()->attach($request['postcodes']);

        return $this->showAll($franchise->postcodes, Response::HTTP_CREATED);
        
    }

  
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Franchise $franchise, Postcode $postcode)
    {
        $this->authorize('delete', $franchise);

        //Check if the postcode is assigned to the child franchise
        $children = $franchise->children;
        
        foreach ($children as $child) {
            if($child->postcodes->contains('id', $postcode->id)){
                return $this->errorResponse("Postcode is associated to a sub-franchise. Cannot be deleted", Response::HTTP_BAD_REQUEST);
            }
        }
        
        $franchise->postcodes()->detach($postcode->id);

        return response()->json($postcode);

    }
}
