<?php

namespace App\Http\Controllers\TradeType;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\TradeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TradeTypeController extends ApiController
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
        $tradeTypes = TradeType::all();

        return $this->showAll($tradeTypes);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('head-office-only');

        $data = $this->validate($request, [
            'name' => 'required'
        ]);

        $tradeType = TradeType::create($data);

        return $this->showOne($tradeType, Response::HTTP_CREATED);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Gate::authorize('head-office-only');

        $tradeType = TradeType::findOrFail($id);

        $updates = $this->validate($request, [
            'name' => ''
        ]);

        $tradeType->update($updates);

        $tradeType->refresh();

        return $this->showOne($tradeType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Gate::authorize('head-office-only');

        $tradeType = TradeType::findOrFail($id);

        $tradeType->delete();

        return $this->showOne($tradeType);
    }
}
