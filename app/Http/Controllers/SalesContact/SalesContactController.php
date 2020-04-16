<?php

namespace App\Http\Controllers\SalesContact;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Postcode;
use App\SalesContact;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SalesContactController extends ApiController
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
        //
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'string|nullable',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email',
            'contact_number' => 'required|string',
            'street1' => 'required|string',
            'street2' => 'string|nullable',
            'suburb' => 'required|string',
            'state' => 'required|string',
            'postcode' => 'required|string|max:10',
            'customer_type' => 'required|in:' . SalesContact::COMMERCIAL . ',' . SalesContact::RESIDENTIAL
        ]);

        $postcode = Postcode::where('pcode', $data['postcode'])->first();

        if($postcode === null){
            return $this->errorResponse("Invalid postcode", Response::HTTP_BAD_REQUEST );
        }

        $salesContact = SalesContact::create($data);
        
        
        return $this->showOne($salesContact, Response::HTTP_CREATED);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
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
