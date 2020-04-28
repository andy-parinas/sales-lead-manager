<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SalesContactRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SalesContactRepository  implements SalesContactRepositoryInterface
{

    public function sortAndPaginate(Array $params)
    {
        if(key_exists('search', $params) && key_exists('on', $params))
        {
            return DB::table('sales_contacts')
                ->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                ->select("id",
                    'first_name as firstName',
                    'last_name as lastName',
                    'contact_number as contactNumber',
                    'customer_type as customerType',
                    'street1', 'street2', 'suburb', 'state', 'postcode', 'status','email', 'email2')
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);
        }

        return DB::table('sales_contacts')
            ->select("id",
                'first_name as firstName',
                'last_name as lastName',
                'contact_number as contactNumber',
                'customer_type as customerType',
                'street1', 'street2', 'suburb', 'state', 'postcode', 'status','email', 'email2')
            ->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);
    }
}
