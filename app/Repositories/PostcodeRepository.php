<?php


namespace App\Repositories;


use App\Franchise;
use App\Postcode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostcodeRepository implements Interfaces\PostcodeRepositoryInterface
{

    public function getAll(array $params)
    {
        if(key_exists('search', $params))
        {
            if (key_exists('size', $params) && $params['size'] > 0){

                return Postcode::where('pcode', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('locality','LIKE', '%' . $params['search'] . '%' )
                    ->orWhere('state','LIKE', '%' . $params['search'] . '%' )
                    ->orderBy($params['column'], $params['direction'])
                    ->paginate($params['size']);

            }else {

                return Postcode::where('pcode', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('locality','LIKE', '%' . $params['search'] . '%' )
                    ->orWhere('state','LIKE', '%' . $params['search'] . '%' )
                    ->orderBy($params['column'], $params['direction'])
                    ->get();

            }

        }else {

            if (key_exists('size', $params) && $params['size'] > 0){
                return Postcode::orderBy($params['column'], $params['direction'])->paginate($params['size']);
            }else {
                return Postcode::orderBy($params['column'], $params['direction'])->get();
            }

        }
    }


    public function getFranchisePostcodes(array $params, Franchise $franchise)
    {

        $query = DB::table('postcodes')
            ->join('franchise_postcode', 'franchise_postcode.postcode_id', '=', 'postcodes.id')
            ->join('franchises', 'franchises.id', '=' , 'franchise_postcode.franchise_id')
            ->select('postcodes.id',
                'postcodes.pcode',
                'postcodes.locality',
                'postcodes.state'
            )
            ->where('franchise_postcode.franchise_id', '=', $franchise->id);

        if($params['column'] == 'postcode') {
            $params['column'] = 'pcode';
        }

        if($params['column'] == 'suburb'){
            $params['column'] = 'locality';
        }

        if(key_exists('search', $params)) {

            $query = $query->where(function ($query) use ($params){
                $query->where('pcode', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('locality','LIKE', '%' . $params['search'] . '%' )
                ->orWhere('state','LIKE', '%' . $params['search'] . '%' )
                ->orderBy($params['column'], $params['direction']);
            });


        }else {

            $query = $query->orderBy($params['column'], $params['direction']);

        }

        return $query->paginate($params['size']);

    }

    public function searchAll($search)
    {
        return DB::table('postcodes')
            ->select( 'id',
                'pcode as postcode',
                'locality as suburb',
                'state'
            )
            ->where('pcode', 'LIKE', $search . '%' )
            ->orWhere('locality', 'LIKE','%' . $search . '%' )
            ->get();

    }

    public function getAvailableFranchisePostcode(array $params, $franchise){


        if($params['column'] == 'postcode') {
            $params['column'] = 'pcode';
        }

        if($params['column'] == 'suburb'){
            $params['column'] = 'locality';
        }


        $subQuery = DB::table('franchises')
            ->select(
                'franchises.id as franchise_id', 'franchises.parent_id', 'franchise_postcode.postcode_id as postcode_id'
            )->join('franchise_postcode', 'franchise_postcode.franchise_id', '=', 'franchises.id')
            ->where('franchises.parent_id', '<>', null);

        $query = DB::table('postcodes')
            ->select('postcodes.id',
                'postcodes.pcode',
                'postcodes.locality',
                'postcodes.state'
            )
            ->leftJoinSub($subQuery, 'sub_franchises', function ($join){
                $join->on('postcodes.id', '=', 'sub_franchises.postcode_id');
            })
            ->where(function($query) use ($franchise){
                $query->where('sub_franchises.franchise_id', '<>', $franchise->id)
                    ->orWhere('sub_franchises.franchise_id', '=', null);
            });



        if(key_exists('search', $params)) {

            $query = $query->where(function ($query) use ($params){
                $query->where('pcode', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('locality','LIKE', '%' . $params['search'] . '%' )
                    ->orWhere('state','LIKE', '%' . $params['search'] . '%' )
                    ->orderBy($params['column'], $params['direction']);
            });


        }else {

            $query = $query->orderBy($params['column'], $params['direction']);

        }

        $query = $query->groupBy(['postcodes.id', 'pcode', 'locality', 'state']);

        return $query->paginate($params['size']);


    }
}
