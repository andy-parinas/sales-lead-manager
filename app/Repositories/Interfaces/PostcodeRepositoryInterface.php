<?php


namespace App\Repositories\Interfaces;


use App\Franchise;

interface PostcodeRepositoryInterface
{

    public function getAll(array $params);

    public function getFranchisePostcodes(array $params, Franchise $franchise);

    public function searchAll($search);

    public function getAvailableFranchisePostcode(array $params, $franchise);

}
