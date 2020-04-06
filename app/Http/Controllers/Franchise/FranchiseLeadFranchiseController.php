<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;

class FranchiseLeadFranchiseController extends ApiController
{
   
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

        $lead->update($request->only(['franchise_id']));
        
        return $this->showOne($lead);
    }

 
}
