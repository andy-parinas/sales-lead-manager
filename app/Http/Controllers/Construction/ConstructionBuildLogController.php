<?php

namespace App\Http\Controllers\Construction;

use App\BuildLog;
use App\Construction;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BuildLog as BuildLogResource;
use Symfony\Component\HttpFoundation\Response;

class ConstructionBuildLogController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $constructionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $constructionId)
    {
        $construction = Construction::findOrFail($constructionId);

        $buildLogs = BuildLog::with('tradeStaff')
                        ->where('construction_id', $construction->id)
                        ->get();

        return $this->showAll(BuildLogResource::collection($buildLogs));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $constructionId)
    {
        $construction = Construction::findOrfail($constructionId);

        $data = $this->validate($request, [
            'work_date' => 'required|date',
            'time_spent' => 'required|numeric',
            'hourly_rate' => 'required|numeric',
            'comments' => 'sometimes',
            'trade_staff_id' => 'required',
        ]);

        $data['total_cost'] = $data['time_spent'] * $data['hourly_rate'];

        $buildLog = $construction->buildLogs()->create($data);

        $buildLog->load('tradeStaff');

        return $this->showOne(new BuildLogResource($buildLog), Response::HTTP_CREATED);

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
