<?php


namespace App\Repositories\Interfaces;

use App\User;

interface FranchiseRepositoryInterface
{

    public function all();

    public function sortAndPaginate(Array $params);

    public function findById($franchiseId);

    public function findByUser(User $user, Array $params);


}