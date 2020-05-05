<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends ApiController
{

    private $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository) {
        $this->middleware('auth:sanctum');
        $this->leadRepository = $leadRepository;
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
    public function show(Lead $lead)
    {

        $this->authorize('view', $lead);

        // $lead = Lead::findOrFail($id);

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
