<?php

namespace App\Http\Controllers\Lead;

use App\Construction;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;
use App\Http\Resources\Construction as ConstructionResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Create the TradeStaff Schedule
        // Does not need to initial Database Transaction as this is a less critical component
        // Should be under Try-Catch block to prevent any interuption in controller if the Schedule failed

        try {
            $scheduleData = [
                'job_number' => $lead->lead_number,
                'anticipated_start' => $construction->anticipated_construction_start,
                'actual_start' => $construction->actual_construction_start,
                'anticipated_end' => $construction->anticipated_construction_complete,
                'actual_end' => $construction->actual_construction_complete,
            ];

            $tradeStaff = $construction->tradeStaff;

            $tradeStaff->tradeStaffSchedules()->create($scheduleData);


        }catch (\Exception $exception){

            Log::error("Error Creating TradeStaffSchedule");
            Log::error($exception);
        }


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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $leadId, $constructionId)
    {
        $lead = Lead::findOrFail($leadId);

        $construction = Construction::findOrFail($constructionId);

        if($construction->lead_id != $lead->id){
            abort(Response::HTTP_BAD_REQUEST, "Construction is not associated with the lead");
        }

        $data = $this->validate($request, [
            'site_address' => 'sometimes',
            'postcode_id' => 'sometimes',
            'material_list' => 'sometimes',
            'date_materials_received' => 'sometimes',
            'date_assembly_completed' => 'sometimes',
            'date_anticipated_delivery' => 'sometimes',
            'date_finished_product_delivery' => 'sometimes',
            'coil_number' => 'sometimes',
            'trade_staff_id' => 'sometimes',
            'anticipated_construction_start' => 'sometimes',
            'anticipated_construction_complete' => 'sometimes',
            'actual_construction_start' => 'sometimes',
            'actual_construction_complete' => 'sometimes',
            'comments' => 'sometimes',
            'final_inspection_date' => 'sometimes',
        ]);

        $construction->update($data);

        return $this->showOne(new ConstructionResource($construction));


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
