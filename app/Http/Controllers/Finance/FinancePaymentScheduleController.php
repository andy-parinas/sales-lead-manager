<?php

namespace App\Http\Controllers\Finance;

use App\Finance;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\PaymentSchedule;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\PaymentSchedule as PaymentScheduleResource;

class FinancePaymentScheduleController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param $finance_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($finance_id)
    {

        //dd("HERE", $finance_id);

        $finance = Finance::findOrFail($finance_id);


        $payments = $finance->paymentsSchedule;

        return $this->showAll(PaymentScheduleResource::collection($payments));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $finance_id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, $finance_id)
    {

        $finance = Finance::findOrFail($finance_id);

        $data = $this->validate($request, [
            'due_date' => 'required|date',
            'description' => 'required',
            'amount' => 'required'
        ]);


        $payment = $finance->paymentsSchedule()->create($data);

        return $this->showOne($payment, Response::HTTP_CREATED);

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
