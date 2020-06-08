<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCollection;
use App\Lead;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use Illuminate\Http\Request;

class LeadDocumentController extends ApiController
{

    private $documentRepository;

    public function __construct(DocumentRepositoryInterface $documentRepository)
    {
        $this->middleware('auth:sanctum');
        $this->documentRepository = $documentRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Lead $lead)
    {
        $documents = $this->documentRepository->getAllByLeadId($this->getRequestParams(), $lead->id);

        return $this->showApiCollection(new DocumentCollection($documents));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
