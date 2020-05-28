<?php

namespace App\Http\Controllers\User;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\FranchiseServiceInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\FranchiseCollection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Http\Resources\Franchise as FranchiseResource;

class UserFranchiseController extends ApiController
{

    private $franchiseService;
    private $franchiseRepository;
    private $userRepository;

    public function __construct(FranchiseServiceInterface $franchiseService,
                                FranchiseRepositoryInterface $franchiseRepository,
                                UserRepositoryInterface $userRepository)
    {
        $this->middleware('auth:sanctum');
        $this->franchiseService = $franchiseService;
        $this->franchiseRepository = $franchiseRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        Gate::authorize('head-office-only');

        $franchises = $this->userRepository->findUsersFranchise($this->getRequestParams(), $id);

        return $this->showApiCollection(new FranchiseCollection($franchises));
    }


    /**
     * Store a newly created resource in storage.
     * This method is for attaching a group of Franchises to the User
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, User $user)
    {

        Gate::authorize('head-office-only');


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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Franchise $franchise)
    {

        Gate::authorize('head-office-only');

        $usersFranchises = $user->franchises;

        if($usersFranchises->contains('id', $franchise->id)){

            if($usersFranchises->count() > 1 && $franchise->isParent()){

                $children = $franchise->children;

                foreach ($children as $child){
                    if($usersFranchises->contains('id', $child->id)){
                        //return $this->errorResponse("Cannot detach parent franchise with children", Response::HTTP_BAD_REQUEST);
                        abort(Response::HTTP_BAD_REQUEST, "Cannot detach Main franchise with it's Sub-Franchise attached to the user");
                    }
                }
            }

            $user->franchises()->detach($franchise->id);

            $user->refresh();

            return $this->showOne(new FranchiseResource($franchise));

        }else {

            //return $this->errorResponse("Franchise does not belong to the user", Response::HTTP_BAD_REQUEST);
            abort(Response::HTTP_BAD_REQUEST,"Franchise does not belong to the user" );
        }


    }


    /**
     * Store a newly created resource in storage.
     * This method is for attaching a single Franchises to the User
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @param Franchise $franchise
     * @return void
     * @throws \HttpException
     */
    public function attach(Request $request, User $user, Franchise $franchise)
    {

        //Get that user's franchises.
        $usersFranchises = $user->franchises;

        // Check if you are attaching the same Franchise that has been attached already

        if($usersFranchises->contains('id', $franchise->id)){
            abort(Response::HTTP_BAD_REQUEST,"Franchise is already attached to the user" );
        }

        //If the User is a staffUser, it should only have one franchise.
        if ($user->isStaffUser() && $usersFranchises->count() >= 1){

            abort(Response::HTTP_BAD_REQUEST,"Staff User should only have one franchise" );
            //throw new BadRequestHttpException("Staff User should only have one franchise");
        }

        // Check if the Franchise that is being attached is a parent or child
        // Staff User can only be attached by a Child or SubFranchise
        if($user->isStaffUser() && $franchise->isParent()){

            abort(Response::HTTP_BAD_REQUEST,"Staff User Cannot be attached with a Main Franchise" );
            //throw new BadRequestHttpException("Staff User Cannot be attached with a Main Franchise");
        }

        // Check if FranchiseAdmin
        // Franchise Admin can only have one parent Franchise
        if($user->isFranchiseAdmin() && $usersFranchises->count() >= 1 && $franchise->isParent()){
  ;
            $usersFranchises->each(function ($fran){
                if($fran->isParent()){
                    abort(Response::HTTP_BAD_REQUEST,"Franchise Admin can only have one Main Franchise" );
                    //throw new BadRequestHttpException("Franchise Admin can only have one Main Franchise");
                }
            });
        }

        // Check if FranchiseAdmin
        // Should attached the Main or Parent franchise First before any Child Franchise
        if($user->isFranchiseAdmin() && $usersFranchises->count() == 0 && !$franchise->isParent() ){

            abort(Response::HTTP_BAD_REQUEST,"Franchise Admin must have a Main Franchise before attaching Sub Franchise" );
            //throw new BadRequestHttpException("Franchise Admin must have a Main Franchise before attaching Sub Franchise");
        }

        // Check if FranchiseAdmin
        // Should only attached the Child Franchise that is under the Parent Franchise
        if($user->isFranchiseAdmin() && $usersFranchises->count() >= 1 && !$franchise->isParent()){

            $parentId = -1;

            foreach ($usersFranchises as $fran){

                if($fran->isParent()){
                    $parentId = $fran->id;
                }
            }

            if($parentId == -1){
                abort(Response::HTTP_BAD_REQUEST,"The Franchise Admin does not have a main franchise. Please correct this" );
                //throw new BadRequestHttpException("The Franchise Admin does not have a main franchise. Please correct this");
            }

            if($franchise->parent_id != $parentId){
                abort(Response::HTTP_BAD_REQUEST,"The Franchise being attached does not belong to the User's Main Franchise" );
                //throw new BadRequestHttpException("The Franchise being attached does not belong to the User's Main Franchise");
            }

        }

        $user->franchises()->attach($franchise->id);


        return $this->showOne(new  FranchiseResource($franchise));

    }


}
