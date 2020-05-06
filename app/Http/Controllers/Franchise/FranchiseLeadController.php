<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Lead as LeadResource;
use App\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\SalesContact;
use App\Services\Interfaces\PostcodeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FranchiseLeadController extends ApiController
{

    private $postcodeService;
    private $leadRepository;

    public function __construct(PostcodeServiceInterface $postcodeService, LeadRepositoryInterface $leadRepository) {
        $this->middleware('auth:sanctum');
        $this->postcodeService = $postcodeService;
        $this->leadRepository = $leadRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Franchise $franchise)
    {

        $this->authorize('view', $franchise);

        $leads = $this->leadRepository->findSortPaginateByFranchise($franchise, $this->getRequestParams());

        return $this->showPaginated($leads);
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
            'lead_number' => 'required',
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
    public function show(Franchise $franchise, $lead_id)
    {
        $this->authorize('view', $franchise);

        // DB::enableQueryLog();

        $lead = Lead::with(['franchise', 'salesContact', 'leadSource', 'jobType',
                            'appointment' ,'jobType.product', 'jobType.designAssessor'])->findOrFail($lead_id);

        // $lead = $this->leadRepository->findLeadById($lead_id);

        // dump(DB::getQueryLog());

        return $this->showOne(new LeadResource($lead));
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

        $lead->update($request->only(['lead_source_id','lead_date' ]));

        return $this->showOne($lead);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Franchise $franchise, Lead $lead)
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return $this->showOne($lead);

    }
}
