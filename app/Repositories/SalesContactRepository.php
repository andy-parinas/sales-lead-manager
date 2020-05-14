<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SalesContactRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SalesContactRepository  implements SalesContactRepositoryInterface
{

    public function sortAndPaginate(Array $params)
    {
        $query =  DB::table('sales_contacts')
                    ->select("id",
                        'first_name as firstName',
                        'last_name as lastName',
                        'contact_number as contactNumber',
                        'customer_type as customerType',
                        'street1', 'street2', 'suburb', 'state', 'postcode', 'status','email', 'email2');


        if(key_exists('search', $params) && key_exists('on', $params))
        {
            return $query
                    ->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                    ->orderBy($params['column'], $params['direction'])
                    ->paginate($params['size']);
        }

        return $query->orderBy($params['column'], $params['direction'])
                    ->paginate($params['size']);
    }


    public function simpleSearch(Array $params)
    {
        $query =  DB::table('sales_contacts')
            ->select("id",
                'first_name as firstName',
                'last_name as lastName',
                'contact_number as contactNumber',
                'customer_type as customerType',
                'street1', 'street2', 'suburb', 'state', 'postcode', 'status','email', 'email2');

        if(key_exists('search', $params))
        {
            return $query
                ->where('first_name', 'LIKE',  $params['search'] . '%')
                ->orWhere('last_name', 'LIKE',  $params['search'] . '%')
                ->orWhere('email', 'LIKE',  '%'. $params['search'] . '%')
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);
        }

        return $query->paginate($params['size']);
    }
}
