<?php

namespace App\Http\Controllers\SalesContact;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Postcode;
use App\Repositories\Interfaces\SalesContactRepositoryInterface;
use App\SalesContact;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\SalesContact as SalesContactResource;
use Illuminate\Support\Facades\Gate;

class SalesContactController extends ApiController
{

    private $salesContactRepository;

    public function __construct(SalesContactRepositoryInterface $salesContactRepository) {
        $this->middleware('auth:sanctum');
        $this->salesContactRepository = $salesContactRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesContacts = $this->salesContactRepository->sortAndPaginate($this->getRequestParams());


        return $this->showPaginated($salesContacts);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'string|nullable',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email',
            'contact_number' => 'required|string',
            'street1' => 'required|string',
            'street2' => 'string|nullable',
            'suburb' => 'required|string',
            'state' => 'required|string',
            'postcode' => 'required|string|max:10',
            'customer_type' => 'required|in:' . SalesContact::COMMERCIAL . ',' . SalesContact::RESIDENTIAL
        ]);

        $postcode = Postcode::where('pcode', $data['postcode'])->first();

        if($postcode === null){
            return $this->errorResponse("Invalid postcode", Response::HTTP_BAD_REQUEST );
        }

        $salesContact = SalesContact::create($data);


        return $this->showOne(new SalesContactResource($salesContact), Response::HTTP_CREATED);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = SalesContact::with('leads')->findOrFail($id);

        return $this->showOne(new SalesContactResource($contact));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalesContact $contact)
    {
        $data = $this->validate($request, [
            'title' => 'string',
            'first_name' => 'string|max:50',
            'last_name' => 'string|max:50',
            'email' => 'email',
            'contact_number' => '|string',
            'street1' => 'string',
            'street2' => 'string|nullable',
            'suburb' => 'string',
            'state' => 'string',
            'postcode' => 'string|max:10',
            'customer_type' => 'in:' . SalesContact::COMMERCIAL . ',' . SalesContact::RESIDENTIAL,
            'status' => 'in:'. SalesContact::ACTIVE . ',' . SalesContact::ARCHIVED,
        ]);

//        dd($data);

        if(($request['postcode'] || $request['state'] || $request['suburb']) && $contact->leads()->count() > 0)
        {
            return $this->errorResponse("Cannot update postode, state, or suburb when Contact is already a lead", Response::HTTP_BAD_REQUEST);
        }

        $contact->update($data);
        $contact->refresh();

        return $this->showOne(new SalesContactResource($contact));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalesContact $contact)
    {

        Gate::authorize('head-office-only');

        $contact->delete();

        return $this->showOne($contact);
    }
}
