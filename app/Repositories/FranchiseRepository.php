<?php


namespace App\Repositories;

use App\Franchise;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
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

    public function findByUser(User $user, Array $params)
    {
        return $user->franchises()
            ->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);
    }

    public function sortAndPaginate(Array $params)
    {

        return Franchise::orderBy($params['column'], $params['direction'])->paginate($params['size']);
    }

}