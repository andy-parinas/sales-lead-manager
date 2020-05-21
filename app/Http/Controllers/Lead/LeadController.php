<?php

namespace App\Http\Controllers\Lead;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\Services\Interfaces\PostcodeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Lead as LeadResource;
use Symfony\Component\HttpFoundation\Response;

class LeadController extends ApiController
{

    private $leadRepository;
    private $postcodeService;

    public function __construct(LeadRepositoryInterface $leadRepository, PostcodeServiceInterface $postcodeService) {
        $this->middleware('auth:sanctum');
        $this->leadRepository = $leadRepository;
        $this->postcodeService = $postcodeService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$this->authorize('viewAny', Lead::class);

        $user = Auth::user();

        if ($user->isHeadOffice()){

            $leads = $this->leadRepository->getAllLeads($this->getRequestParams());

            return $this->showPaginated($leads);
        }

        $franchiseIds = $user->franchises->pluck('id')->toArray();

        $leads = $this->leadRepository->findLeadsByUsersFranchise($franchiseIds, $this->getRequestParams());

//        dd($leads);

        return $this->showPaginated($leads);

    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lead = Lead::with(['franchise', 'salesContact', 'leadSource', 'jobType', 'appointment', 'documents'])->findOrFail($id);

        $this->authorize('view', $lead);

        // $lead = Lead::findOrFail($id);

        return $this->showOne(new LeadResource($lead));
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
        $lead = Lead::with(['franchise', 'salesContact'])->findOrFail($id);

        $user = Auth::user();

        // Data for StaffUser
        $data = $this->validate($request, [
            'lead_source_id' => '',
            'lead_date' => '',
        ]);

        if($user->isFranchiseAdmin()){

            $data = $this->validate($request, [
                'franchise_id' => '',
                'lead_source_id' => '',
                'lead_date' => '',
            ]);

            if ($request->has('franchise_id')){

                $franchise = Franchise::with('postcodes')->find($data['franchise_id']);
                $this->authorize('update',  [$lead, $franchise] );

                $data['postcode_status'] = $this->postcodeService->checkSalesContactPostcode($lead->salesContact, $franchise);
            }

        }

        if($user->isHeadOffice()){

            $data = $this->validate($request, [
                'lead_number' => '',
                'franchise_id' => '',
                'lead_source_id' => '',
                'lead_date' => '',
            ]);
        }

        $lead->update($data);
        $lead->refresh();

        return $this->showOne(new LeadResource($lead));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);

        $this->authorize('delete', $lead);

        $lead->delete();

        return $this->showOne([], Response::HTTP_NO_CONTENT);
    }
}
