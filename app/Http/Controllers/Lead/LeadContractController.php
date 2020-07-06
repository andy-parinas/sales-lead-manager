<?php

namespace App\Http\Controllers\Lead;

use App\Contract;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use App\Services\Interfaces\ContractFinanceServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Contract as ContractResource;

class LeadContractController extends ApiController
{

    protected $contractService;

    public function __construct(ContractFinanceServiceInterface $contractService)
    {
        $this->middleware('auth:sanctum');
        $this->contractService = $contractService;
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

        $data = $this->validateContractData($request);

        DB::beginTransaction();

        try {

            $contract = $this->contractService->createContract($data);
            $finance = $this->contractService->createFinance($contract);

            $lead->contract()->save($contract);
            $lead->finance()->save($finance);

            DB::commit();

            return $this->showOne(new ContractResource($contract), Response::HTTP_CREATED);

        }catch (\Exception $exception){

            DB::rollBack();
            throw new \Exception($exception);
        }

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

        $finance = $lead->finance;
        $contract = Contract::findOrFail($contractId);

        $data = $this->validateContracUpdates($request);

        DB::beginTransaction();

        try
        {

            $updatedContract = $this->contractService->updateContract($contract, $data);

            $this->contractService->updateFinance($finance, $updatedContract);

            DB::commit();

            return $this->showOne(new ContractResource($updatedContract));


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



    private function validateContractData(Request $request){


        $data = $this->validate($request, [
            'contract_date' => 'required|date',
            'contract_number' => 'required',
            'contract_price' => 'required|numeric',
            'warranty_required' => 'required',
            'date_warranty_sent' => ''
        ]);

        if($request->deposit_amount)
        {

            $data['deposit_amount'] = $request->deposit_amount;
            $this->validate($request, ['date_deposit_received' => 'required']);
            $data['date_deposit_received'] = $request->date_deposit_received;
        }

        $data['deposit_amount'] = 0;

        return $data;
    }


    private function validateContracUpdates(Request $request){
        $data = $this->validate($request, [
            'contract_date' => 'sometimes|date',
            'contract_number' => 'sometimes|string',
            'contract_price' => 'sometimes|numeric',
            'deposit_amount' => 'sometimes|numeric',
            'warranty_required' => 'sometimes|string',
            'date_warranty_sent' => 'sometimes|'
        ]);

        if($request->deposit_amount > 0 ){
            $this->validate($request, ['date_deposit_received' => 'required|date']);
            $data['date_deposit_received'] = $request->date_deposit_received;
        }

        return $data;
    }
}
