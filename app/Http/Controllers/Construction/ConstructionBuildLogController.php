<?php

namespace App\Http\Controllers\Construction;

use App\BuildLog;
use App\Construction;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BuildLog as BuildLogResource;

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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
