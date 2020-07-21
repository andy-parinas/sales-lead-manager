<?php


namespace App\Repositories\Interfaces;


interface SalesStafRepositoryInterface
{

    public function getAll(array $params);

    public function searchAll($search);

}
