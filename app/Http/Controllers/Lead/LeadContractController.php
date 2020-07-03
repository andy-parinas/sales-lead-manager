<?php

namespace App\Http\Controllers\Lead;

use App\Contract;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        if($contract == null){

            return response()->json([], Response::HTTP_NO_CONTENT);
        }

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
            'contract_date' => 'required|date',
            'contract_number' => 'required',
            'contract_price' => 'required|numeric',
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $leadId, $contractId)
    {
        $lead = Lead::findOrFail($leadId);

        $contract = Contract::findOrFail($contractId);

        $data = $this->validate($request, [
            'contract_date' => 'date',
            'contract_number' => '',
            'contract_price' => 'numeric',
            'deposit_amount' => 'numeric',
            'warranty_required' => '',
            'date_warranty_sent' => ''
        ]);

        if($data['deposit_amount'] > 0 && $data['deposit_amount'] != $contract->deposit_amount ){
            $this->validate($request, ['date_deposit_received' => 'required|date']);
            $data['date_deposit_received'] = $request->date_deposit_received;
        }

        DB::beginTransaction();

        try
        {
            $total_contract = $contract->total_contract;

            if($data['contract_price'] != $contract->contract_price){

                if($data['deposit_amount'] != $contract->deposit_amount){
                    $total_contract = $data['contract_price'] - $data['deposit_amount'] + $contract->total_variation;
                }else {
                    $total_contract = $data['contract_price'] - $contract->deposit_amount  + $contract->total_variation;
                }

            }else {

                if($data['deposit_amount'] != $contract->deposit_amount){
                    $total_contract = $data['contract_price'] - $data['deposit_amount'] + $contract->total_variation;
                }

            }

            if($total_contract < 0 ){
                throw new \Exception("Update will cause negative value on  Total Contract");
            }


            $data['total_contract'] = $total_contract;

            $contract->update($data);

            /**
             * TODO Add the Adjustments for Finance Here
             */

            DB::commit();

            $contract->refresh();

            return $this->showOne(new ContractResource($contract));


        }
        catch (\Exception $exception)
        {
            DB::rollBack();
            throw new \Exception($exception);
        }

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
