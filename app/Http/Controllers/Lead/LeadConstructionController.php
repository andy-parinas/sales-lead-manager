<?php

namespace App\Http\Controllers\Lead;

use App\Construction;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;
use App\Http\Resources\Construction as ConstructionResource;
use Symfony\Component\HttpFoundation\Response;


class LeadConstructionController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $leadId)
    {
        $lead = Lead::findOrfail($leadId);


        $construction = Construction::with(['postcode', 'tradeStaff'])
            ->where('lead_id', $lead->id)->first();


        if($construction == null){

            return response()->json([], Response::HTTP_NO_CONTENT);
        }

        return $this->showOne(new ConstructionResource($construction));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $leadId)
    {

        $lead = Lead::findOrFail($leadId);

        $data = $this->validate($request, [
            'site_address' => 'required',
            'postcode_id' => 'required',
            'material_list' => 'sometimes',
            'date_materials_received' => 'sometimes',
            'date_assembly_completed' => 'sometimes',
            'date_anticipated_delivery' => 'sometimes',
            'date_finished_product_delivery' => 'sometimes',
            'coil_number' => 'sometimes',
            'trade_staff_id' => 'required',
            'anticipated_construction_start' => 'sometimes',
            'anticipated_construction_complete' => 'sometimes',
            'actual_construction_start' => 'sometimes',
            'actual_construction_complete' => 'sometimes',
            'comments' => 'sometimes',
            'final_inspection_date' => 'sometimes',
        ]);


        $construction = $lead->construction()->create($data);

        return $this->showOne($construction, Response::HTTP_CREATED);


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
