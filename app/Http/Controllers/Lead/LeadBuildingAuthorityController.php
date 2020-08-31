<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;
use App\Http\Resources\BuildingAuthority as BuildingAuthorityResource;
use Symfony\Component\HttpFoundation\Response;

class LeadBuildingAuthorityController extends ApiController
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
    public function index($leadId)
    {

        $lead = Lead::findOrFail($leadId);

        $buildingAuthority = $lead->buildingAuthority;

        if($buildingAuthority == null){
            abort(Response::HTTP_NOT_FOUND, "Building Authority is not created");
        }

        return $this->showOne(new BuildingAuthorityResource($buildingAuthority));

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
            'approval_required' => 'required',
            'date_plans_sent_to_draftsman'  => 'sometimes|date',
            'date_plans_completed' => 'sometimes|date',
            'date_plans_sent_to_authority' => 'sometimes|date',
            'building_authority_comments' => 'sometimes',
            'date_anticipated_approval' => 'sometimes|date',
            'date_received_from_authority' => 'sometimes|date',
            'permit_number' => 'sometimes',
            'security_deposit_required' => 'sometimes',
            'building_insurance_name' => 'sometimes',
            'building_insurance_number' => 'sometimes',
            'date_insurance_request_sent' => 'sometimes|date'
        ]);

        $buildingAuthority = $lead->buildingAuthority()->create($data);


        return $this->showOne(new BuildingAuthorityResource($buildingAuthority), Response::HTTP_CREATED);


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
