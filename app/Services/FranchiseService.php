<?php

namespace App\Services;

use App\Franchise;
use App\Services\Interfaces\FranchiseServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FranchiseService implements FranchiseServiceInterface
{

    public function validateParentChildRelationship(Collection $franchises)
    {
        $parent = new Collection();
        $children = new Collection();

        // Loop through the franchise and Find the parent first
        // We need to run this first to make sure that parent is acquired first 
        // In cases that the sorting where parent comes last.
        foreach ($franchises as $franchise ) {
            
            if($franchise->isParent()){
                $parent->push($franchise);
            }
        }

        //Check There should only be one parent or none 
        if($parent->count() > 1)
        {
            throw new BadRequestHttpException("Cannot have more than one parent");
        }

        if($parent->count() == 0)
        {
            throw new BadRequestHttpException("Must Include the Parent");

        }

        if($parent->count() == 1 ){

            foreach ($franchises as $franchise) {
                if($parent->contains('id', $franchise->parent_id)){
                    $children->push($franchise);
                }
            }
        }

        return $children->merge($parent);

    }

    public function validateFranchisesAreSibling(Collection $franchises)
    {

    }

    public function validateFranchisesBelongsToParent(Collection $franchises, Franchise $parent)
    {
        $children = new Collection();

        foreach ($franchises as $franchise ) {
            if($franchise->parent_id !== null && $franchise->parent_id == $parent->id ){
                $children->push($franchise);
            }
        }

        return $children;
    }
}