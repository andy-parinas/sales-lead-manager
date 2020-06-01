<?php

namespace App\Http\Controllers\Franchise;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\FranchiseCollection;
use App\Http\Resources\RelatedFranchiseCollection;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


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

            return $this->showPaginated($franchises);
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

        $rules = [
            'franchise_number' => 'required',
            'name' => 'required',
        ];

        $this->validate($request, $rules);

        $franchise = Franchise::create($request->all());


        return $this->showOne($franchise, Response::HTTP_CREATED);

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

        $franchise->update($request->all());

        return $this->showOne($franchise);

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
