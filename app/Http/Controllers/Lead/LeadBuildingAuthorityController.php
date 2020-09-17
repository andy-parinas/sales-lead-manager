<?php

namespace App\Http\Controllers\Lead;

use App\BuildingAuthority;
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
    public function index(Request $request, $leadId)
    {

        $lead = Lead::findOrFail($leadId);

        $buildingAuthority = $lead->buildingAuthority;

        if($buildingAuthority == null){
            return response()->json([], Response::HTTP_NO_CONTENT);
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
            'building_authority_name' => 'sometimes',
            'date_plans_sent_to_draftsman'  => 'sometimes',
            'date_plans_completed' => 'sometimes',
            'date_plans_sent_to_authority' => 'sometimes',
            'building_authority_comments' => 'sometimes',
            'date_anticipated_approval' => 'sometimes',
            'date_received_from_authority' => 'sometimes',
            'permit_number' => 'sometimes',
            'security_deposit_required' => 'sometimes',
            'building_insurance_name' => 'sometimes',
            'building_insurance_number' => 'sometimes',
            'date_insurance_request_sent' => 'sometimes'
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

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $leadId, $buildingAuthorityId)
    {

        $lead = Lead::findOrFail($leadId);

        $buildingAuthority = BuildingAuthority::findOrFail($buildingAuthorityId);

        if($buildingAuthority->lead_id != $lead->id){
            abort(Response::HTTP_BAD_REQUEST, "Building Authority is not associated with the lead");
        }

        $data = $this->validate($request, [
            'approval_required' => 'sometimes',
            'building_authority_name' => 'sometimes',
            'date_plans_sent_to_draftsman'  => 'sometimes',
            'date_plans_completed' => 'sometimes',
            'date_plans_sent_to_authority' => 'sometimes',
            'building_authority_comments' => 'sometimes',
            'date_anticipated_approval' => 'sometimes',
            'date_received_from_authority' => 'sometimes',
            'permit_number' => 'sometimes',
            'security_deposit_required' => 'sometimes',
            'building_insurance_name' => 'sometimes',
            'building_insurance_number' => 'sometimes',
            'date_insurance_request_sent' => 'sometimes'
        ]);

        $buildingAuthority->update($data);

        return $this->showOne(new BuildingAuthorityResource($buildingAuthority));

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
