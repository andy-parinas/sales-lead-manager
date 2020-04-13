<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    public function findUsersSortedAndPaginated(Array $params)
    {

        if(key_exists('search', $params) && key_exists('on', $params)){


            return DB::table('users')->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);

        }

        return DB::table('users')
            ->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);


    }
}
