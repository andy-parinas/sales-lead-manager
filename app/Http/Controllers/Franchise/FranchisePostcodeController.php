<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostcodeAllCollection;
use App\Http\Resources\PostcodeCollection;
use App\Postcode;
use App\Repositories\Interfaces\PostcodeRepositoryInterface;
use App\Services\Interfaces\PostcodeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FranchisePostcodeController extends ApiController
{

    private $postcodeService;
    private $postcodeRepository;

    public function __construct(PostcodeServiceInterface $postcodeService, PostcodeRepositoryInterface $postcodeRepository) {
        $this->middleware('auth:sanctum');
        $this->postcodeService = $postcodeService;
        $this->postcodeRepository = $postcodeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Franchise $franchise
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Franchise $franchise)
    {

        $this->authorize('view', $franchise);


        $postcodes = $this->postcodeRepository->getFranchisePostcodes($this->getRequestParams(), $franchise );

        if ($postcodes instanceof LengthAwarePaginator){
            return $this->showApiCollection(new PostcodeCollection($postcodes));
        }

        return $this->showApiCollection(new PostcodeAllCollection($postcodes));

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
