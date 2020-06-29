<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Contract as ContractResource;

class LeadContractController extends ApiController
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
    public function index($lead_id)
    {
        $lead = Lead::findOrFail($lead_id);

        $contract = $lead->contract;


        return $this->showOne(new ContractResource($contract));


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $lead_id)
    {

        $lead = Lead::findOrFail($lead_id);
        //dd($lead);

        $data = $this->validate($request, [
            'contract_date' => 'required',
            'contract_number' => 'required',
            'contract_price' => 'required',
            'warranty_required' => 'required',
            'date_warranty_sent' => ''
        ]);



        if($request->deposit_amount)
        {

            if($request->deposit_amount > $data['contract_price'])
            {
                abort(Response::HTTP_BAD_REQUEST, "Deposit Amount should be less than Contract Price");
            }

            $data['deposit_amount'] = $request->deposit_amount;

            $this->validate($request, ['date_deposit_received' => 'required']);

            $data['date_deposit_received'] = $request->date_deposit_received;


        }else {

            $data['deposit_amount'] = 0;
        }

        $data['total_contract'] = $data['contract_price'] - $data['deposit_amount'];

        $contract = $lead->contract()->create($data);

        return $this->showOne(new ContractResource($contract), Response::HTTP_CREATED);

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
