<?php

namespace App\Http\Controllers\Lead;

use App\CustomerReview;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;
use App\Http\Resources\CustomerReview as CustomerReviewResource;
use Symfony\Component\HttpFoundation\Response;

class LeadCustomerReviewController extends ApiController
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

        $customerReview = $lead->customerReview;

        if($customerReview == null){
            return response()->json([], Response::HTTP_NO_CONTENT);
        }

        return $this->showOne(new CustomerReviewResource($customerReview));

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
            'date_project_completed' => 'required',
            'date_warranty_received' => 'required',
            'home_addition_type' => 'sometimes',
            'home_addition_description' => 'sometimes',
            'service_received_rating' => 'sometimes',
            'workmanship_rating' => 'sometimes',
            'finished_product_rating' => 'sometimes',
            'design_consultant_rating' => 'sometimes',
            'maintenance_letter_sent' => 'sometimes',
            'comments' => 'sometimes',
        ]);


        $customerReview = $lead->customerReview()->create($data);


        return $this->showOne(new CustomerReviewResource($customerReview), Response::HTTP_CREATED);

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
    public function update(Request $request, $leadId, $customerReviewId)
    {
        $lead = Lead::findOrFail($leadId);

        $customerReview = CustomerReview::findOrFail($customerReviewId);

        if($customerReview->lead_id != $lead->id){
            abort(Response::HTTP_BAD_REQUEST, "Lead and Verification do not match");
        }

        $data = $this->validate($request, [
            'date_project_completed' => 'sometimes',
            'date_warranty_received' => 'sometimes',
            'home_addition_type' => 'sometimes',
            'home_addition_description' => 'sometimes',
            'service_received_rating' => 'sometimes',
            'workmanship_rating' => 'sometimes',
            'finished_product_rating' => 'sometimes',
            'design_consultant_rating' => 'sometimes',
            'maintenance_letter_sent' => 'sometimes',
            'comments' => 'sometimes',
        ]);


        $customerReview->update($data);


        return $this->showOne(new CustomerReviewResource($customerReview));
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
