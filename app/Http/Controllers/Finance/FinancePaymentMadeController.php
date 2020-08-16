<?php

namespace App\Http\Controllers\Finance;

use App\Finance;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\PaymentMade;
use Illuminate\Http\Request;
use App\Http\Resources\PaymentMade as PaymentMadeResource;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

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

        DB::beginTransaction();

        try {
            $payment = $finance->paymentsMade()->create($data);

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

        $payment = PaymentMade::findOrFail($paymentId);

        if($payment->finance_id != $finance->id)
            abort(Response::HTTP_BAD_REQUEST, "Payment is not associated with Finance");

        $data = $this->validate($request, [
            'payment_date' => 'sometimes|date',
            'description' => 'sometimes',
            'amount' => 'sometimes'
        ]);

        DB::beginTransaction();

        try {

            //Reverse Total Payment
            $total_payment = $finance->total_payment_made - $payment->amount;

            //Update the Payment
            $payment->update($data);

            //Apply the New Total Payment
            $total_payment = $total_payment + $payment->amount;

            //Re-Compute Balance
            $balance = $finance->total_contract - $finance->deposit - $total_payment;

            // Update Finance
            $finance->update([
                'total_payment_made' => $total_payment,
                'balance' => $balance
            ]);

            DB::commit();

            return $this->showOne(new PaymentMadeResource($payment), Response::HTTP_OK);

        }catch (\Exception $exception){
            DB::rollBack();
            throw new \Exception($exception);
        }



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

        $payment = PaymentMade::findOrFail($paymentId);

        if($payment->finance_id != $finance->id)
            abort(Response::HTTP_BAD_REQUEST, "Payment is not associated with Finance");


        DB::beginTransaction();

        try {

            //Reverse Total Payment
            $total_payment = $finance->total_payment_made - $payment->amount;

            //Update the Payment
            $payment->delete();

            //Re-Compute Balance
            $balance = $finance->total_contract - $finance->deposit - $total_payment;

            // Update Finance
            $finance->update([
                'total_payment_made' => $total_payment,
                'balance' => $balance
            ]);

            DB::commit();

            return $this->showOne(new PaymentMadeResource($payment), Response::HTTP_OK);

        }catch (\Exception $exception){
            DB::rollBack();
            throw new \Exception($exception);
        }



    }
}
