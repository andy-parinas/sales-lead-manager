<?php


namespace App\Repositories;

use App\Franchise;
use App\Repositories\FranchiseRepositoryInterface;
use App\User;

class FranchiseRepository implements FranchiseRepositoryInterface
{

    public function all()
    {

        return Franchise::all();
    }

    public function findById($franchiseId)
    {

    }

    public function findByUser(User $user )
    {
        return $user->franchises;
    }

    public function sortAndPaginate($column, $direction, $perPage)
    {
        return Franchise::orderBy($column, $direction)->paginate($perPage);
    }

}