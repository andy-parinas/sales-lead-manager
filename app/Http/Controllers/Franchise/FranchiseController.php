<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\FranchiseCollection;
use App\Http\Resources\ParentFranchiseCollection;
use App\Http\Resources\RelatedFranchiseCollection;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Franchise as FranchiseResource;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class FranchiseController extends ApiController
{

    private $franchiseRepository;

    public function __construct(FranchiseRepositoryInterface $franchiseRepository) {
        $this->middleware('auth:sanctum');
        $this->franchiseRepository = $franchiseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if(Auth::user()->can('viewAny', Franchise::class))
        {
            $franchises = $this->franchiseRepository->sortAndPaginate($this->getRequestParams());

            return $this->showApiCollection(new FranchiseCollection($franchises));

        }
        else
        {
            $franchises = $this->franchiseRepository->findByUser(Auth::user(), $this->getRequestParams());

            return $this->showApiCollection(new FranchiseCollection($franchises));
        }


    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Franchise::class);

        $data = $this->validate($request, [
            'franchise_number' => 'required',
            'name' => 'required',
            'description' => '',
            'parent_id' => ''
        ]);


        $franchise = Franchise::create($data);


        return $this->showOne(new FranchiseResource($franchise), Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Franchise $franchise)
    {

        $this->authorize('view', $franchise);

        return $this->showOne($franchise);

    }

    public function related(Request $request, $id)
    {
        $franchises = $this->franchiseRepository->findRelatedFranchise($this->getRequestParams(), $id);

        return $this->showApiCollection(new RelatedFranchiseCollection($franchises));

    }

    public function parents(Request $request)
    {
        $franchises = $this->franchiseRepository->findParents($this->getRequestParams());

        return $this->showApiCollection(new ParentFranchiseCollection($franchises));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Franchise $franchise)
    {

        $this->authorize('update', $franchise);

        $data = $this->validate($request, [
            'franchise_number' => '',
            'name' => '',
            'description' => '',
            'parent_id' => ''
        ]);

        //Check if the Franchise is a parent franchise with children;
        if(array_key_exists('parent_id', $data) && $data['parent_id'] !=null &&
            $franchise->isParent() && $franchise->children->count() > 0 ){

            abort(Response::HTTP_BAD_REQUEST, "Cannot assigned Main Franchise to Franchise with Sub-Franchises");
        }

        if(array_key_exists('parent_id', $data) && $data['parent_id'] == $franchise->franchise_number){
            abort(Response::HTTP_BAD_REQUEST, "Cannot assigned Main Franchise to self");
        }

        $franchise->update($data);

        $franchise->refresh();

        return $this->showOne(new FranchiseResource($franchise));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Franchise $franchise)
    {
        $this->authorize('delete', $franchise);

        $franchise->delete();

        return $this->showOne($franchise);

    }
}
