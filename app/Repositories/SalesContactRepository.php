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
                ->select("*")
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);
        }

        return DB::table('sales_contacts')
            ->select("*")
            ->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);
    }
}
