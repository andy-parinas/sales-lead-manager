<?php


namespace App\Repositories\Interfaces;


interface SalesStafRepositoryInterface
{

    public function getAll(array $params);

    public function searchAll($search);


    public function getAllByFranchise(array $franchiseIds, array $params);

    public function searchAllByFranchise(array $franchiseIds, $search);

}
