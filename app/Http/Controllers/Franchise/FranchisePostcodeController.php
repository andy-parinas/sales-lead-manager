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
use App\Http\Resources\Postcode as PostcodeResource;

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
     * @return \Illuminate\Http\JsonResponse
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

    public function available(Request $request, Franchise $franchise)
    {

        $this->authorize('view', $franchise);

        $postcodes = $this->postcodeRepository->getAvailableFranchisePostcode($this->getRequestParams(), $franchise);


        return $this->showApiCollection(new PostcodeCollection($postcodes));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $franchise_id)
    {

        $franchise = Franchise::findOrFail($franchise_id);
        $parent = Franchise::find($franchise->parent_id);

        $this->authorize('create', $franchise);



        $data = $this->validate($request, [
            'postcodes' => 'required|array'
        ]);

        $parent->postcodes()->attach($data['postcodes']);
        $franchise->postcodes()->attach($data['postcodes']);

        return $this->showOne($data, Response::HTTP_CREATED);

    }

    public function attach(Franchise $franchise, Postcode $postcode)
    {
        //Check if postcode is already attached
        if($franchise->postcodes->contains('id', $postcode->id)){
            abort(Response::HTTP_BAD_REQUEST, "Postcode already attached to the franchise");
        }

        //Check if Children and Postcode is in the parent
        if(!$franchise->isParent()){

            $parent = $franchise->parent;

            //Check if the Postcode is in the parent postcode
            if(!$parent->postcodes->contains('id', $postcode->id)){
                abort(Response::HTTP_BAD_REQUEST, "Postcode is not assigned to the Main Franchise");
            }

        }

        $franchise->postcodes()->attach($postcode->id);

        return $this->showOne(new PostcodeResource($postcode));

    }

    public function detach(Request $request, $franchise_id)
    {
        $franchise = Franchise::findOrFail($franchise_id);
        $parent = Franchise::find($franchise->parent_id);


        $data = $this->validate($request, [
            'postcodes' => 'required|array'
        ]);


        $parent->postcodes()->detach($data['postcodes']);
        $franchise->postcodes()->detach($data['postcodes']);

        return $this->showOne($data, Response::HTTP_OK);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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

    public function check($franchiseId, $postcodeId)
    {

        $franchise = Franchise::findOrFail($franchiseId);
        $postcode = Postcode::findOrFail($postcodeId);


        $result = $franchise->postcodes->contains('id', $postcode->id);


        return response()->json($result);

    }
}
