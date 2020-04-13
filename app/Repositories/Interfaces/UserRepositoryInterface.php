<?php

namespace App\Repositories\Interfaces;


interface UserRepositoryInterface
{

    public function findUsersSortedAndPaginated(Array $params);

}