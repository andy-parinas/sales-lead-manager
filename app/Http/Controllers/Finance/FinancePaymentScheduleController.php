<?php

namespace App\Http\Controllers\Finance;

use App\Finance;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMade as PaymentMadeResource;
use App\PaymentMade;
use App\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $financeId, $paymentId)
    {
        $finance = Finance::findOrFail($financeId);

        $payment = PaymentSchedule::findOrFail($paymentId);

        if($payment->finance_id != $finance->id)
            abort(Response::HTTP_BAD_REQUEST, "Payment is not associated with Finance");


        $data = $this->validate($request, [
            'due_date' => 'sometimes|date',
            'description' => 'sometimes',
            'amount' => 'sometimes'
        ]);

        $payment->update($data);


        return $this->showOne(new PaymentScheduleResource($payment));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($financeId, $paymentId)
    {
        $finance = Finance::findOrFail($financeId);
        $payment = PaymentSchedule::findOrFail($paymentId);

        if($payment->finance_id != $finance->id)
            abort(Response::HTTP_BAD_REQUEST, "Payment is not associated with Finance");

        $payment->delete();

        return $this->showOne(new PaymentScheduleResource($payment));
    }

    public function convert($financeId, $paymentId)
    {
        $finance = Finance::findOrFail($financeId);
        $paymentSchedule = PaymentSchedule::findOrFail($paymentId);

        if($paymentSchedule->finance_id != $finance->id)
            abort(Response::HTTP_BAD_REQUEST, "Payment is not associated with Finance");


        DB::beginTransaction();

        try {

            $data = [
                'payment_date' => date("Y-m-d"),
                'description' => $paymentSchedule->description,
                'amount' => $paymentSchedule->amount
            ];

            $payment = $finance->paymentsMade()->create($data);

            $paymentSchedule->update([
                'status' => PaymentSchedule::PAID
            ]);

            $total_payment = $finance->total_payment_made + $payment->amount;
            $balance = $finance->total_contract - $finance->deposit - $total_payment;

            $finance->update([
                'total_payment_made' => $total_payment,
                'balance' => $balance
            ]);

            DB::commit();

            return $this->showOne(new PaymentMadeResource($payment), Response::HTTP_CREATED);

        }catch (\Exception $exception){
            DB::rollBack();
            throw new \Exception($exception);

        }
    }
}
