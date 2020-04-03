<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\PostcodeServiceInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FranchiseLeadController extends ApiController
{

    private $postcodeService;

    public function __construct(PostcodeServiceInterface $postcodeService) {
        $this->middleware('auth:sanctum');
        $this->postcodeService = $postcodeService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Franchise $franchise)
    {

        $this->authorize('view', $franchise);

        $leads = $franchise->leads;

        return $this->showAll($leads);
    }

   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $franchise_id)
    {

        $franchise = Franchise::with('postcodes')->findOrFail($franchise_id);

        $this->authorize('createLead', $franchise);

        $data = $this->validate($request, [
            'number' => 'required',
            'sales_contact_id' => 'required|integer',
            'lead_source_id' => 'required|integer',
            'lead_date' => 'required'
        ]);

        /**
         * Check if the SalesContact postcode is within the franchise postcode assignment. 
         * If not, Need to tag the Lead as Outside-of-franchise
         */
        $salesContact = SalesContact::findOrFail($request->sales_contact_id);
        $data['postcode_status'] = $this->postcodeService->checkSalesContactPostcode($salesContact, $franchise);
 
        $lead = $franchise->leads()
            ->create($data);

        return $this->showOne($lead, Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Franchise $franchise, Lead $lead)
    {
        $this->authorize('view', $franchise);
        
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
