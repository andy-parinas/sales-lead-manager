<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;

class LeadController extends ApiController
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

        $this->authorize('viewAny', Lead::class);

        $leads = Lead::all();

        return $this->showAll($leads);
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {

        $this->authorize('view', $lead);

        // $lead = Lead::findOrFail($id);

        return $this->showOne($lead);
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
