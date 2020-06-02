<?php


namespace App\Repositories;

use App\Franchise;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpParser\Node\Expr\Array_;

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

        if(key_exists('search', $params))
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

    public function findRelatedFranchise(Array $params, $id)
    {
        $franchise = Franchise::findOrFail($id);

        //Check if it is a Parent Franchise
        if($franchise->isParent()){

            return DB::table('franchises')
                ->select('id', 'name',
                    'franchise_number', 'description', 'parent_id')
                ->selectRaw('(CASE WHEN parent_id IS NULL THEN "Main Franchise" ELSE "Sub Franchise" END) as type')
                ->selectRaw('(CASE WHEN parent_id IS NULL THEN NULL ELSE ? END) as parent', [$franchise->franchise_number])
                ->where('id', $id)
                ->orWhere('parent_id', $id)
                ->orderBy($params['column'], $params['direction'])->paginate($params['size']);

//            return Franchise::with('children')->where('id', $id)
//                ->orderBy($params['column'], $params['direction'])->paginate($params['size']);


        }else {

            $parent = $franchise->parent;

            return DB::table('franchises')
                ->select('id', 'name',
                    'franchise_number', 'description', 'parent_id')
                ->selectRaw('(CASE WHEN parent_id IS NULL THEN "Main Franchise" ELSE "Sub Franchise" END) as type')
                ->selectRaw('(CASE WHEN parent_id IS NULL THEN NULL ELSE ? END) as parent', [$parent->franchise_number])
                ->where('id', $parent->id)
                ->orWhere('parent_id', $parent->id)
                ->orderBy($params['column'], $params['direction'])->paginate($params['size']);

//            return Franchise::with('children')->where('id', $parent->id)
//                ->orderBy($params['column'], $params['direction'])->paginate($params['size']);

        }

    }


    public function findParents(Array $params)
    {

        return DB::table('franchises')
                ->select('id', 'name',
                        'franchise_number', 'description')
                ->where('parent_id', null)
                ->orderBy('franchise_number', 'asc')
                ->get();


    }


}
