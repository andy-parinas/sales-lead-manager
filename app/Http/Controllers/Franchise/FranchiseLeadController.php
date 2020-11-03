<?php

namespace App\Http\Controllers\Franchise;

use App\Events\LeadCreated;
use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Lead as LeadResource;
use App\JobType;
use App\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\SalesContact;
use App\Services\Interfaces\PostcodeServiceInterface;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
     * @param Franchise $franchise
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
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
     * @param Request $request
     * @param $franchise_id
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     * @throws Exception
     */
    public function store(Request $request, $franchise_id)
    {

        $franchise = Franchise::with('postcodes')->findOrFail($franchise_id);

        $this->authorize('createLead', $franchise);


        $data = $this->validate($request, [
            'lead.lead_number' => 'required',
            'lead.sales_contact_id' => 'required|integer',
            'lead.lead_source_id' => 'required|integer',
            'lead.lead_date' => 'required',
            'lead.received_via' => '',
            'job_type.taken_by' => 'required',
            'job_type.date_allocated' => 'required',
            'job_type.description' => '',
            'job_type.product_id' => 'required',
            'job_type.sales_staff_id' => 'required',
            'appointment.appointment_date' => 'required',
            'appointment.followup_date' => '',
            'appointment.appointment_notes' => '',
            'appointment.quoted_price' => 'required',
            'appointment.outcome' => 'required',
            'appointment.comments' => '',
        ]);

        /**
         * Check if the SalesContact postcode is within the franchise postcode assignment.
         * If not, Need to tag the Lead as Outside-of-franchise
         */
        $salesContact = SalesContact::findOrFail($data['lead']['sales_contact_id']);
        $data['lead']['postcode_status'] = $this->postcodeService->checkSalesContactPostcode($salesContact, $franchise);

        DB::beginTransaction();

        try {
            $lead = $franchise->leads()->create($data['lead']);
            $job_type = $lead->jobType()->create($data['job_type']);
            $appointment = $lead->appointment()->create($data['appointment']);

            DB::commit();


            $lead->load('jobType.salesStaff', 'salesContact');

            LeadCreated::dispatch($lead);

            return $this->showOne(new LeadResource($lead), Response::HTTP_CREATED);

        }catch (Exception $exception){
            DB::rollBack();

            throw new Exception($exception);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Franchise $franchise
     * @param $lead_id
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function show(Franchise $franchise, $lead_id)
    {
        $this->authorize('view', $franchise);

        // DB::enableQueryLog();

        $lead = Lead::with(['franchise', 'salesContact', 'leadSource', 'jobType',
                            'appointment' ,'jobType.product', 'jobType.salesStaff', 'contracts'])->findOrFail($lead_id);

        // $lead = $this->leadRepository->findLeadById($lead_id);

        // dump(DB::getQueryLog());

        return $this->showOne(new LeadResource($lead));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Franchise $franchise
     * @param Lead $lead
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
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
     * @param Franchise $franchise
     * @param Lead $lead
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function destroy(Franchise $franchise, Lead $lead)
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return $this->showOne($lead);

    }
}
