<?php


namespace App\Repositories;

use App\Franchise;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\Schema;

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

        if(key_exists('search', $params) && key_exists('on', $params))
        {
            return Franchise::where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);
        }

        return Franchise::orderBy($params['column'], $params['direction'])->paginate($params['size']);
    }

    


}