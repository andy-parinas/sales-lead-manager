<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use App\Verification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Verification as VerificationResource;

class LeadVerificationController extends ApiController
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

        $verification = $lead->verification;

        if($verification == null){
            return response()->json([], Response::HTTP_NO_CONTENT);
        }

        $verification->load('roofSheet', 'roofColour');

        return $this->showOne(new VerificationResource($verification));

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
            'design_correct' => 'required',
            'date_design_check' => 'required',
            'costing_correct' => 'sometimes',
            'date_costing_check' => 'sometimes',
            'estimated_build_days' => 'sometimes',
            'trades_required' => 'sometimes',
            'building_supervisor' => 'sometimes',
            'roof_sheet_id' => 'sometimes',
            'roof_colour_id' => 'sometimes',
            'lineal_metres' => 'sometimes',
            'franchise_authority' => 'sometimes',
            'authority_date' => 'sometimes',
        ]);


        $verification = $lead->verification()->create($data);

        $verification->load('roofSheet', 'roofColour');

        return $this->showOne(new VerificationResource($verification), Response::HTTP_CREATED);

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $leadId, $verifcationId)
    {
        $lead = Lead::findOrFail($leadId);

        $verfication = Verification::findOrFail($verifcationId);

        if($verfication->lead_id != $lead->id){
            abort(Response::HTTP_BAD_REQUEST, "Lead and Verification do not match");
        }

        $data = $this->validate($request, [
            'design_correct' => 'sometimes',
            'date_design_check' => 'sometimes',
            'costing_correct' => 'sometimes',
            'date_costing_check' => 'sometimes',
            'estimated_build_days' => 'sometimes',
            'trades_required' => 'sometimes',
            'building_supervisor' => 'sometimes',
            'roof_sheet_id' => 'sometimes',
            'roof_colour_id' => 'sometimes',
            'lineal_metres' => 'sometimes',
            'franchise_authority' => 'sometimes',
            'authority_date' => 'sometimes',
        ]);


        $verfication->update($data);

        $verfication->load('roofSheet', 'roofColour');

        return $this->showOne(new VerificationResource($verfication));
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
