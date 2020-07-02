<?php

namespace App\Http\Controllers\Contract;

use App\Contract;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ContractVariationController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $contractId)
    {
        $contract = Contract::findOrFail($contractId);

        $data = $this->validate($request, [
            'variation_date' => 'required|date',
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try
        {

            $variation = $contract->contractVariations()->create($data);

            $total_variation = $contract->total_variation + $variation->amount;
            $total_contract = $contract->total_contract + $total_variation;

            if($total_contract < 0 ){
                throw new \Exception("Variation will make the Total Contract Price Negative");
            }


            $contract->update([
                'total_variation' => $total_variation,
                'total_contract' => $total_contract
            ]);

            DB::commit();

            $contract->refresh();

            return $this->showOne($variation, Response::HTTP_CREATED);

        }
        catch (\Exception $exception)
        {
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
