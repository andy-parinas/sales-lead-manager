<?php

namespace App\Http\Controllers\LeadSource;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\LeadSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class LeadSourceController extends ApiController
{

    public function __construct() {
        $this->middleware('auth:sanctum');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sources = LeadSource::all();

        return $this->showAll($sources);
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
            'name' => 'required|string|max:100'
        ]);

        $leadSource = LeadSource::create($data);

        // dd($leadSource);

        return $this->showOne($leadSource, Response::HTTP_CREATED);
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

        $source = LeadSource::findOrFail($id);

        $data = $this->validate($request, [
            'name' => 'string|max:100'
        ]);


        $source->update($data);

        return $this->showOne($source);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Gate::authorize('head-office-only');

        $leadSource = LeadSource::findOrFail($id);

        $leadSource->delete();

        return $this->showOne($leadSource);
    }
}
