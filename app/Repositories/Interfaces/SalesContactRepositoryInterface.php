<?php

namespace App\Repositories\Interfaces;


interface SalesContactRepositoryInterface
{

    public function sortAndPaginate(Array $params);
}