<?php

namespace App\Http\Controllers\Finance;

use App\Finance;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\PaymentMade;
use Illuminate\Http\Request;
use App\Http\Resources\PaymentMade as PaymentMadeResource;

class FinancePaymentMadeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($finance_id)
    {
        $finance = Finance::findOrFail($finance_id);

        $payments = $finance->paymentsMade;

        return $this->showApiCollection(PaymentMadeResource::collection($payments));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $finance_id)
    {
        $finance = Finance::findOrFail($finance_id);

        $data = $this->validate($request, [
            'payment_date' => 'required|date',
            'description' => 'required',
            'amount' => 'required'
        ]);

        $payment = $finance->paymentsMade()->create($data);


        return $this->showOne(new PaymentMadeResource($payment));

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
