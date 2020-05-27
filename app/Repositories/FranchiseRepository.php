<?php


namespace App\Repositories;

use App\Franchise;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\DB;
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

        if(key_exists('search', $params) && key_exists('on', $params))
        {
            return $user->franchises()->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);
        }

        return $user->franchises()
            ->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);
    }

    public function sortAndPaginate(Array $params)
    {
//        $query = DB::table('franchises')
//            ->select(
//                'franchise_number as franchiseNumber',
//                'name',
//                'description'
//            );
//
//        if(key_exists('search', $params) && key_exists('on', $params))
//        {
//            return $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
//                ->orderBy($params['column'], $params['direction'])
//                ->paginate($params['size']);
//        }
//
//        return $query->orderBy($params['column'], $params['direction'])->paginate($params['size']);

        if(key_exists('search', $params) && key_exists('on', $params))
        {
            return Franchise::with('parent')->where('franchise_number', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('name','LIKE', '%' . $params['search'] . '%' )
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);
        }

        return Franchise::orderBy($params['column'], $params['direction'])->paginate($params['size']);


    }


    public function findUsersParentFranchise(User $user)
    {
        foreach ($user->franchises as $franchise) {
            if($franchise->isParent()){
                return $franchise;
            }else {
                return null;
            }
        }
    }


}
