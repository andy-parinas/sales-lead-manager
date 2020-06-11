<?php


namespace App\Repositories;


use App\Postcode;

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
}
