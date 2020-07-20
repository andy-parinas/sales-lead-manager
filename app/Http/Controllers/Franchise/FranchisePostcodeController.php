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


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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

    public function detach(Franchise $franchise, Postcode $postcode)
    {
        //Check if postcode is actually attached
        if(!$franchise->postcodes->contains('id', $postcode->id)){
            abort(Response::HTTP_BAD_REQUEST, "Postcode is not attached to the franchise");
        }

        //Check if Franchise is parent and the postcode is also attached to the Children
        if($franchise->isParent()){

            $children = $franchise->children;

            foreach ($children as $child){
                if($child->postcodes->contains('id', $postcode->id)){
                    abort(Response::HTTP_BAD_REQUEST, "Can't detach Postcode that is attached to a Sub-Franchise: " . $child->franchise_number);
                }
            }

        }

        $franchise->postcodes()->detach($postcode->id);

        return $this->showOne(new PostcodeResource($postcode));

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
