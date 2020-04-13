<?php

namespace App\Http\Controllers\User;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\Services\Interfaces\FranchiseServiceInterface;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserFranchiseController extends ApiController
{

    private $franchiseService;
    private $franchiseRepository;

    public function __construct(FranchiseServiceInterface $franchiseService, FranchiseRepositoryInterface $franchiseRepository) 
    {
        $this->middleware('auth:sanctum');
        $this->franchiseService = $franchiseService;
        $this->franchiseRepository = $franchiseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {

        $this->isAllowed('user_access');


        $data = $this->validate($request, [
            'franchises' => 'required|array',
            'franchises.*' => 'integer'
        ]);


        // Check the type of User the Franchise is being attached
        // FranchiseAdmin can have multiple related franchises.
        if($user->isFranchiseAdmin()){

            $franchises = Franchise::find($data['franchises']);


            //Condition1. Fresh user and don't have Franchises attached yet. 
            if($user->franchises()->count() === 0)
            {

                // Validate that the Franchises are related. It should have common parent_id
                // Must Include the parent in the request. Otherwise will be a bad Request
                $franchiseData = $this->franchiseService->validateParentChildRelationship($franchises);

                $user->franchises()->attach($franchiseData->pluck('id'));

                return $this->showAll($user->franchises, Response::HTTP_CREATED);

            }else {

                $parent = $this->franchiseRepository->findUsersParentFranchise($user);

                $children = $this->franchiseService->validateFranchisesBelongsToParent($franchises, $parent);

                $user->franchises()->attach($children->pluck('id'));

                $user->refresh();
                return $this->showAll($user->franchises, Response::HTTP_CREATED);

            }

        }

        if($user->isStaffUser()){

            if($user->franchises()->count() >= 1 || count($data['franchises']) > 1 ){
                return $this->errorResponse("Staff User can only have one franchise", Response::HTTP_BAD_REQUEST);
            }

            $franchise = Franchise::find($data['franchises'][0]);

            if($franchise == null){
                return $this->errorResponse("Franchise being attach not found.", Response::HTTP_BAD_REQUEST);
            }

            if($franchise->isParent()){
                return $this->errorResponse("Can not attach Parent Franchise to Staff User", Response::HTTP_BAD_REQUEST);
            }

            $user->franchises()->attach($data['franchises']);

            $user->refresh();

            return $this->showAll($user->franchises, Response::HTTP_CREATED);

        }

        $this->errorResponse("Can only attach franchise to Franchise Admin or Staff User", Response::HTTP_BAD_REQUEST);

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
