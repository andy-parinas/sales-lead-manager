<?php


namespace App\Repositories\Interfaces;

use App\User;

interface FranchiseRepositoryInterface
{

    public function all();

    public function sortAndPaginate(Array $params);

    public function findById($franchiseId);

    public function findByUser(User $user, Array $params);

    public function findUsersParentFranchise(User $user);

    public function findRelatedFranchise(Array $params, $id);

    public function findParents(Array $params);

    public function getAllSubFranchise(array $params);

}
