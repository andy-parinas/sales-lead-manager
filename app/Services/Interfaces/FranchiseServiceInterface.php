<?php

namespace App\Services\Interfaces;

use App\Franchise;
use Illuminate\Database\Eloquent\Collection;

interface FranchiseServiceInterface 
{
    public function validateParentChildRelationship(Collection $franchises);

    public function validateFranchisesAreSibling(Collection $franchises);

    public function validateFranchisesBelongsToParent(Collection $franchises, Franchise $parent);
    


}