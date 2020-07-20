<?php


namespace App\Repositories;


use App\Franchise;
use App\Postcode;
use Illuminate\Support\Facades\DB;

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

        if(key_exists('search', $params))
        {
            if (key_exists('size', $params) && $params['size'] > 0){

                return $franchise->postcodes()->where('pcode', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('locality','LIKE', '%' . $params['search'] . '%' )
                    ->orWhere('state','LIKE', '%' . $params['search'] . '%' )
                    ->orderBy($params['column'], $params['direction'])
                    ->paginate($params['size']);

            }else {

                return $franchise->postcodes()->where('pcode', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('locality','LIKE', '%' . $params['search'] . '%' )
                    ->orWhere('state','LIKE', '%' . $params['search'] . '%' )
                    ->orderBy($params['column'], $params['direction'])
                    ->get();

            }

        }else {

            if (key_exists('size', $params) && $params['size'] > 0){
                return $franchise->postcodes()->orderBy($params['column'], $params['direction'])->paginate($params['size']);
            }else {
                return $franchise->postcodes()->orderBy($params['column'], $params['direction'])->get();
            }

        }
    }

    public function searchAll($search)
    {
        return DB::table('postcodes')
            ->select( 'id',
                'pcode as postcode',
                'locality as suburb',
                'state',
            )
            ->where('pcode', 'LIKE', $search . '%' )
            ->orWhere('locality', 'LIKE','%' . $search . '%' )
            ->get();

    }
}
