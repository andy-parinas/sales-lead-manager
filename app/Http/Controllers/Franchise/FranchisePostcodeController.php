<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostcodeCollection;
use App\Postcode;
use App\Services\Interfaces\PostcodeServiceInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FranchisePostcodeController extends ApiController
{

    private $postcodeService;

    public function __construct(PostcodeServiceInterface $postcodeService) {
        $this->middleware('auth:sanctum');
        $this->postcodeService = $postcodeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Franchise $franchise)
    {

        $this->authorize('view', $franchise);

        $postcodes = $franchise->postcodes;

        return $this->showApiCollection(new PostcodeCollection($postcodes));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $franchise_id)
    {

        $franchise = Franchise::with('parent')->findOrFail($franchise_id);

        $this->authorize('create', $franchise);

        $data = $this->validate($request, [
            'postcodes' => 'required|array'
        ]);

        $postcodes = $this->postcodeService->checkParentPostcodes($franchise, $data['postcodes']);

        $franchise->postcodes()->attach($postcodes);

        return $this->showAll($franchise->postcodes, Response::HTTP_CREATED);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Franchise $franchise, Postcode $postcode)
    {
        $this->authorize('delete', $franchise);

        //Check if the postcode is assigned to the child franchise
        $children = $franchise->children;

        foreach ($children as $child) {
            if($child->postcodes->contains('id', $postcode->id)){
                return $this->errorResponse("Postcode is associated to a sub-franchise. Cannot be deleted", Response::HTTP_BAD_REQUEST);
            }
        }

        $franchise->postcodes()->detach($postcode->id);

        return response()->json($postcode);

    }
}
