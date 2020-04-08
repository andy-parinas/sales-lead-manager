<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use App\Services\Interfaces\PostcodeServiceInterface;
use Illuminate\Http\Request;

class FranchiseLeadFranchiseController extends ApiController
{

    private $postcodeService;

    public function __construct(PostcodeServiceInterface $postcodeService) {
        $this->middleware('auth:sanctum');
        $this->postcodeService = $postcodeService;
    }
   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Franchise $franchise, Lead $lead)
    {
        $this->authorize('updateLead', $franchise);

        $newFranchise = Franchise::findOrFail($request['franchise_id']);
        $this->authorize('changeLeadFranchise', $newFranchise);

        //Need to adjust the postcode_status based on the Franchise postcodes.
        $data = [
            'franchise_id' => $request['franchise_id'],
            'postcode_status' => $this->postcodeService->checkSalesContactPostcode($lead->salesContact, $newFranchise)
        ];

        $lead->update($data);
        
        return $this->showOne($lead);
    }

 
}
