<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\JobType;
use App\Lead;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Http\Resources\Lead as LeadResource;

class LeadJobTypeController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function update(Request $request, $leadId, $jobTypeId)
    {

        $lead = Lead::with(['jobType', 'appointment', 'documents'])->findOrFail($leadId);
        $jobType = JobType::findOrFail($jobTypeId);

        if($lead->jobType->id != $jobType->id){
            throw new BadRequestHttpException("The Jobtype is not associated with the lead");
        }

        $data = $this->validate($request, [
            'taken_by' => '',
            'date_allocated' => 'date',
            'product_id' => '',
            'sales_staff_id' => '',
            'description' => ''
        ]);



        $jobType->update($data);

        $lead->refresh();

        return $this->showOne(new LeadResource($lead), Response::HTTP_OK);

    }
}
